<?php

namespace App\Services\Flickr\Objects;

use App\Events\FlickrDownloadRequest;
use App\Exceptions\Flickr\FlickrApiPhotoSetsGetInfoException;
use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrDownloadAlbum;
use App\Traits\Notifications\HasSlackNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * Class FlickrAlbum
 * @package App\Services\Flickr\Objects
 */
class FlickrAlbum
{
    use Notifiable, HasSlackNotification;

    private string $id;
    private ?object $album;
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
            'album `%s` ( id `%s` ) with `%d` photos',
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
            $this->album = FlickrClient::getPhotoSetInfo($this->id);
        } catch (FlickrApiPhotoSetsGetInfoException $exception) {
            $this->album = null;
        }

        return $this->isValid();
    }

    /**
     * @return int
     */
    public function getPhotosCount(): int
    {
        return (int) $this->album->photoset->count_photos;
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
     * @return string
     */
    public function getOwner(): string
    {
        return $this->album->photoset->owner;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->album !== null;
    }

    public function download(): void
    {
        FlickrDownloadAlbum::dispatch($this);
        event(new FlickrDownloadRequest('download', $this));
    }
}
