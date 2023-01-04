<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram2News\Domain\Model;

use GeorgRinger\News\Domain\Model\News;

class NewsInstagram extends News
{
    /**
     * @var int
     */
    protected $_languageUid = -1;

    protected string $instagramId = '';

    protected string $postedBy = '';

    protected string $mediaType = '';

    public function getInstagramId(): string
    {
        return $this->instagramId;
    }

    public function setInstagramId(string $instagramId): self
    {
        $this->instagramId = $instagramId;

        return $this;
    }

    public function getPostedBy(): string
    {
        return $this->postedBy;
    }

    public function setPostedBy(string $postedBy): self
    {
        $this->postedBy = $postedBy;

        return $this;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): self
    {
        $this->mediaType = $mediaType;

        return $this;
    }
}
