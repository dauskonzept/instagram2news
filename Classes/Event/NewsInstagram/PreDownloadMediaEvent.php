<?php

declare(strict_types=1);

namespace DSKZPT\Instagram2News\Event\NewsInstagram;

use DSKZPT\Instagram2News\Domain\Model\NewsInstagram;

class PreDownloadMediaEvent
{
    private NewsInstagram $NewsInstagram;

    public function __construct(NewsInstagram $NewsInstagram)
    {
        $this->NewsInstagram = $NewsInstagram;
    }

    public function getNewsInstagram(): NewsInstagram
    {
        return $this->NewsInstagram;
    }

    public function setNewsInstagram(NewsInstagram $NewsInstagram): void
    {
        $this->NewsInstagram = $NewsInstagram;
    }
}
