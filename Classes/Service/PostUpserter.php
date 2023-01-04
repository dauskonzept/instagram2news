<?php

declare(strict_types=1);

namespace DSKZPT\Instagram2News\Service;

use DSKZPT\Instagram2News\Domain\Model\NewsInstagram;
use DSKZPT\Instagram2News\Domain\Repository\NewsInstagramRepository;
use DSKZPT\Instagram2News\Event\NewsInstagram\NotPersistedEvent;
use DSKZPT\Instagram2News\Event\NewsInstagram\PostPersistEvent;
use DSKZPT\Instagram2News\Event\NewsInstagram\PrePersistEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use SvenPetersen\Instagram\Client\ApiClientInterface;
use SvenPetersen\Instagram\Domain\Model\Dto\PostDTO;
use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Service\EmojiRemover;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class PostUpserter
{
    /** @var string */
    private const TABLENAME = 'tx_news_domain_model_news';

    /** @var string */
    private const MEDIA_FIELDNAME = 'fal_media';

    private NewsInstagramRepository $newsRepository;

    private EventDispatcherInterface $eventDispatcher;

    private PersistenceManagerInterface $persistenceManager;

    /**
     * @var array<string, string>
     */
    private array $extConf;

    public function __construct(
        NewsInstagramRepository $newsRepository,
        EventDispatcherInterface $eventDispatcher,
        PersistenceManagerInterface $persistenceManager
    ) {
        $this->newsRepository = $newsRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->persistenceManager = $persistenceManager;

        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->extConf = $extensionConfiguration->get('instagram2news');
    }

    public function upsertPost(PostDTO $dto, int $storagePid, ApiClientInterface $apiClient): NewsInstagram
    {
        $newsItem = $this->newsRepository->findOneByInstagramId($dto->getId()) ?? new NewsInstagram();

        $filteredText = EmojiRemover::filter($dto->getCaption());

        $newsItem->setTitle(substr($filteredText, 0, 255));
        if ($filteredText === '') {
            // If post has no caption fallback to its ID as title since it is a mandatory field.
            $newsItem->setTitle($dto->getId());
        }

        $newsItem->setBodytext($filteredText);
        $newsItem->setTeaser($filteredText);
        $newsItem->setPid($storagePid);
        $newsItem
            ->setInstagramId($dto->getId())
            ->setPostedBy($apiClient->getFeed()->getUsername())
            ->setMediaType($dto->getMediaType());

        /** @var \DateTimeImmutable $postedAt */
        $postedAt = $dto->getTimestamp();
        $newsItem->setDatetime((new \DateTime())->setTimestamp($postedAt->getTimestamp()));

        /** @var PrePersistEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new PrePersistEvent($newsItem, $dto)
        );

        if ($event->peristPost() === false) {
            $this->eventDispatcher->dispatch(new NotPersistedEvent($newsItem, $dto));

            return $newsItem;
        }

        $newsItem = $event->getNewsInstagram();
        $isAlreadyImported = $newsItem->getUid() !== null;

        $this->newsRepository->add($newsItem);
        $this->persistenceManager->persistAll();

        // Don't download posts media(s) again
        if ($isAlreadyImported === true) {
            return $newsItem;
        }

        $newsItem = $this->processPostMedia($newsItem, $dto);

        /** @var PostPersistEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new PostPersistEvent($newsItem, $dto)
        );

        return $event->getNewsInstagram();
    }

    private function processPostMedia(NewsInstagram $newsItem, PostDTO $dto): NewsInstagram
    {
        switch ($dto->getMediaType()) {
            case Post::MEDIA_TYPE_IMAGE:
                $fileObject = $this->downloadFile($dto->getMediaUrl(), Post::IMAGE_FILE_EXT);
                $this->addFileToFal($fileObject, $newsItem, self::TABLENAME, self::MEDIA_FIELDNAME);

                break;
            case Post::MEDIA_TYPE_VIDEO:
                $fileObject = $this->downloadFile($dto->getMediaUrl(), Post::VIDEO_FILE_EXT);
                $this->addFileToFal($fileObject, $newsItem, self::TABLENAME, self::MEDIA_FIELDNAME);

                // Download thumbnail image
                $fileObject = $this->downloadFile($dto->getThumbnailUrl(), Post::IMAGE_FILE_EXT);
                $this->addFileToFal($fileObject, $newsItem, self::TABLENAME, self::MEDIA_FIELDNAME);

                break;
            case Post::MEDIA_TYPE_CAROUSEL_ALBUM:
                $children = $dto->getChildren();

                foreach ($children as $child) {
                    switch ($child->getMediaType()) {
                        case Post::MEDIA_TYPE_IMAGE:
                            $fileObject = $this->downloadFile(
                                $child->getMediaUrl(),
                                Post::IMAGE_FILE_EXT
                            );

                            $this->addFileToFal($fileObject, $newsItem, self::TABLENAME, self::MEDIA_FIELDNAME);

                            break;
                        case Post::MEDIA_TYPE_VIDEO:
                            $fileObject = $this->downloadFile($child->getMediaUrl(), Post::VIDEO_FILE_EXT);
                            $this->addFileToFal($fileObject, $newsItem, self::TABLENAME, self::MEDIA_FIELDNAME);

                            break;
                    }
                }

                break;
        }

        $this->newsRepository->update($newsItem);
        $this->persistenceManager->persistAll();

        return $newsItem;
    }

    private function downloadFile(string $fileUrl, string $fileExtension): File
    {
        $relativeFilePath = $this->extConf['local_file_storage_path'];
        $directory = sprintf('%s%s', Environment::getProjectPath(), $relativeFilePath);
        GeneralUtility::mkdir_deep($directory);

        $directory = str_replace('1:', 'uploads', $directory);
        $filePath = sprintf('%s/%s.%s', $directory, md5($fileUrl), $fileExtension);
        $data = file_get_contents($fileUrl);
        file_put_contents($filePath, $data);

        /** @var ResourceFactory $resourceFactory */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        /** @var File $file */
        $file = $resourceFactory->retrieveFileOrFolderObject($filePath);

        return $file;
    }

    private function addFileToFal(File $file, NewsInstagram $newElement, string $tablename, string $fieldname): void
    {
        $fields = [
            'pid' => $newElement->getPid(),
            'uid_local' => $file->getUid(),
            'uid_foreign' => $newElement->getUid(),
            'tablenames' => $tablename,
            'table_local' => 'sys_file',
            'fieldname' => $fieldname,
            'l10n_diffsource' => '',
            'sorting_foreign' => $file->getUid(),
            'tstamp' => time(),
            'crdate' => time(),
        ];

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        $databaseConn = $connectionPool->getConnectionForTable('sys_file_reference');
        $databaseConn->insert('sys_file_reference', $fields);
    }
}
