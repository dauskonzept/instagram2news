<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram2News\Event\NewsInstagram;

use SvenPetersen\Instagram\Domain\Model\Dto\PostDTO;
use SvenPetersen\Instagram2News\Domain\Model\NewsInstagram;

class PostPersistEvent
{
    private NewsInstagram $NewsInstagram;

    private PostDTO $postDTO;

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
}
