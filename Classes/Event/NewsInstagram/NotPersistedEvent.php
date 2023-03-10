<?php

declare(strict_types=1);

namespace DSKZPT\Instagram2News\Event\NewsInstagram;

use DSKZPT\Instagram2News\Domain\Model\NewsInstagram;
use SvenPetersen\Instagram\Domain\Model\Dto\PostDTO;

class NotPersistedEvent
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

    public function getPostDTO(): PostDTO
    {
        return $this->postDTO;
    }
}
