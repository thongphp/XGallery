<?php

namespace App\Services\Flickr\Objects;

use App\Exceptions\Flickr\FlickrApiGalleryGetInfoException;
use App\Facades\FlickrClient;
use Illuminate\Support\Collection;

/**
 * Class FlickrGallery
 * @package App\Services\Flickr\Objects
 */
class FlickrGallery extends FlickrDownload
{
    private ?object $gallery;

    /**
     * @return int
     */
    public function getPhotosCount(): int
    {
        return (int) $this->gallery->gallery->count_photos;
    }

    /**
     * @return Collection
     */
    public function getPhotos(): Collection
    {
        if (!$this->isValid()) {
            return $this->photos;
        }

        $page = 1;

        do {
            $photos = FlickrClient::getGalleryPhotos($this->getId(), $page);
            $this->photos = $this->photos->merge($photos->photos->photo);
            $page++;
        } while ($page <= $photos->photos->pages);

        return $this->photos;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->gallery->gallery->title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->gallery->gallery->description ?? null;
    }

    /**
     * @return bool
     */
    protected function load(): bool
    {
        try {
            $this->gallery = FlickrClient::getGalleryInformation($this->getId());
        } catch (FlickrApiGalleryGetInfoException $exception) {
            $this->gallery = null;
        }

        return $this->isValid();
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->gallery !== null;
    }
}
