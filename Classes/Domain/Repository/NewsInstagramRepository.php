<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram2News\Domain\Repository;

use GeorgRinger\News\Domain\Repository\NewsRepository as BaseRepository;
use SvenPetersen\Instagram2News\Domain\Model\NewsInstagram;

class NewsInstagramRepository extends BaseRepository
{
    public function findOneByInstagramId(string $instagramId): ?NewsInstagram
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching($query->equals('instagramId', $instagramId));

        /** @var NewsInstagram|null $result */
        $result = $query
            ->setLimit(1)
            ->execute()
            ->getFirst();

        return $result;
    }
}
