<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram2news" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram2News\Command;

use SvenPetersen\Instagram\Domain\Model\Feed;
use SvenPetersen\Instagram\Domain\Repository\FeedRepository;
use SvenPetersen\Instagram\Factory\ApiClientFactoryInterface;
use SvenPetersen\Instagram2News\Service\PostUpserter;
use SvenPetersen\Instagram2News\Service\SlugService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class ImportPostsCommand extends Command
{
    private ApiClientFactoryInterface $apiClientFactory;

    private FeedRepository $feedRepository;

    private PostUpserter $postUpserter;

    public function __construct(
        PostUpserter $postUpserter,
        FeedRepository $feedRepository,
        ApiClientFactoryInterface $apiClientFactory,
    ) {
        $this->apiClientFactory = $apiClientFactory;
        $this->feedRepository = $feedRepository;
        $this->postUpserter = $postUpserter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Imports tweets as ETX:news articles.')
            ->addArgument('username', InputArgument::REQUIRED, 'The Twitter username to import tweets from')
            ->addArgument('storagePid', InputArgument::REQUIRED, 'The PID where to save the news records')
            ->addArgument('limit', InputArgument::OPTIONAL, 'The maximum number of tweets to import (max: 100)', 25);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $limit = (int)$input->getArgument('limit');
        $storagePid = $input->getArgument('storagePid');

        if (is_numeric($storagePid) === false) {
            throw new \InvalidArgumentException(sprintf('The StoragePid argument must be numeric. "%s" given.', $storagePid));
        }

        $feed = $this->getFeed($username);
        assert($feed instanceof Feed);

        $apiClient = $this->apiClientFactory->create($feed);
        $posts = $apiClient->getPosts($limit);

        foreach ($posts as $postDTO) {
            $this->postUpserter->upsertPost($postDTO, (int)$storagePid, $apiClient);
        }

        SlugService::populateEmptySlugsInCustomTable('tx_news_domain_model_news', 'path_segment');

        return Command::SUCCESS;
    }

    private function getFeed(string $username): ?Feed
    {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->feedRepository->setDefaultQuerySettings($querySettings);

        return $this->feedRepository->findOneByUsername($username);
    }
}
