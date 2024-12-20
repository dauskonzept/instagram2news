<?php

declare(strict_types=1);

namespace DSKZPT\Instagram2News\Domain\Model;

use GeorgRinger\News\Domain\Model\News;

class NewsInstagram extends News
{
    protected ?int $_languageUid = -1;

    protected string $instagramId = '';

    protected string $postedBy = '';

    protected string $mediaType = '';

    protected string $permalink = '';

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

    public function getPermalink(): string
    {
        return $this->permalink;
    }

    public function setPermalink(string $permalink): void
    {
        $this->permalink = $permalink;
    }
}
