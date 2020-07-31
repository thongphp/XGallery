<?php

namespace App\Services\Flickr\Objects;

use App\Exceptions\Flickr\FlickrApiPhotoSetsGetInfoException;
use App\Facades\FlickrClient;
use Illuminate\Support\Collection;

/**
 * Class FlickrAlbum
 * @package App\Services\Flickr\Objects
 */
final class FlickrAlbum extends FlickrDownload
{
    private ?object $album;

    /**
     * @return int
     */
    public function getPhotosCount(): int
    {
        if (!$this->isValid()) {
            return 0;
        }

        return (int) $this->album->photoset->count_photos;
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
            $photos = FlickrClient::getPhotoSetPhotos($this->getId(), $page);

            $this->photos = $this->photos->merge($photos->photoset->photo);
            $page++;
        } while ($page <= $photos->photoset->pages);

        return $this->photos;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->album->photoset->title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->album->photoset->description ?? null;
    }

    /**
     * @return bool
     */
    protected function load(): bool
    {
        try {
            $this->album = FlickrClient::getPhotoSetInfo($this->getId());
        } catch (FlickrApiPhotoSetsGetInfoException $exception) {
            $this->album = null;
        }

        return $this->isValid();
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->album !== null;
    }
}
