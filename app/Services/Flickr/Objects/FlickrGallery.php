<?php

namespace App\Services\Flickr\Objects;

use App\Events\FlickrDownloadRequest;
use App\Exceptions\Flickr\FlickrApiGalleryGetInfoException;
use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrDownloadGallery;
use Illuminate\Support\Collection;

/**
 * Class FlickrGallery
 * @package App\Services\Flickr\Objects
 */
class FlickrGallery
{
    private string $id;
    private ?object $gallery;
    private Collection $photos;

    /**
     * FlickrAlbum constructor.
     * @param  string  $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->photos = collect([]);
    }

    public function __toString(): string
    {
        return sprintf(
            'gallery `%s` ( id `%s` ) with `%d` photos',
            $this->getTitle(),
            $this->getId(),
            $this->getPhotosCount()
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function load(): bool
    {
        try {
            $this->gallery = FlickrClient::getGalleryInformation($this->id);
        } catch (FlickrApiGalleryGetInfoException $exception) {
            $this->gallery = null;
        }

        return $this->isValid();
    }

    /**
     * @return int
     */
    public function getPhotosCount(): int
    {
        return (int) $this->gallery->gallery->count_photos;
    }

    /**
     * @return object|null
     */
    public function getPhotos(): ?Collection
    {
        if (!$this->isValid()) {
            return null;
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
     * @return string
     */
    public function getOwner(): string
    {
        return $this->gallery->gallery->owner;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->gallery !== null;
    }

    public function download()
    {
        FlickrDownloadGallery::dispatch($this);
        event(new FlickrDownloadRequest('download', $this));
    }
}
