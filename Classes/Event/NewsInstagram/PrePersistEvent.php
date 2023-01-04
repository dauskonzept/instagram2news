<?php

declare(strict_types=1);

namespace DSKZPT\Instagram2News\Event\NewsInstagram;

use DSKZPT\Instagram2News\Domain\Model\NewsInstagram;
use SvenPetersen\Instagram\Domain\Model\Dto\PostDTO;

class PrePersistEvent
{
    private NewsInstagram $NewsInstagram;

    private PostDTO $postDTO;

    /**
     * Used to control if given Post should be imported/persisted
     */
    private bool $persistPost = true;

    public function __construct(NewsInstagram $NewsInstagram, PostDTO $postDTO)
    {
        $this->NewsInstagram = $NewsInstagram;

        $this->postDTO = $postDTO;
    }

    public function getNewsInstagram(): NewsInstagram
    {
        return $this->NewsInstagram;
    }

    public function setNewsInstagram(NewsInstagram $NewsInstagram): void
    {
        $this->NewsInstagram = $NewsInstagram;
    }

    public function getPostDTO(): PostDTO
    {
        return $this->postDTO;
    }

    public function peristPost(): bool
    {
        return $this->persistPost;
    }

    public function doNotPersistPost(): self
    {
        $this->persistPost = false;

        return $this;
    }
}
