<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram2News\Event\NewsInstagram;

use SvenPetersen\Instagram2News\Domain\Model\NewsInstagram;
use TYPO3\CMS\Core\Resource\File;

class PostDownloadMediaEvent
{
    private NewsInstagram $NewsInstagram;

    private File $file;

    public function __construct(NewsInstagram $NewsInstagram, File $file)
    {
        $this->NewsInstagram = $NewsInstagram;
        $this->file = $file;
    }

    public function getNewsInstagram(): NewsInstagram
    {
        return $this->NewsInstagram;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }
}
