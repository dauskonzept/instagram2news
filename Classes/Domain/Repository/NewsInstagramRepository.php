<?php

declare(strict_types=1);

namespace DSKZPT\Instagram2News\Domain\Repository;

use DSKZPT\Instagram2News\Domain\Model\NewsInstagram;

class NewsInstagramRepository extends \GeorgRinger\News\Domain\Repository\NewsRepository
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
