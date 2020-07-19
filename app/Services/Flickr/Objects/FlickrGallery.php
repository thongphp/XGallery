<?php

namespace App\Services\Flickr\Objects;

use App\Exceptions\Flickr\FlickrApiGalleryGetInfoException;
use App\Facades\FlickrClient;
use App\Facades\UserActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class FlickrGallery
 * @package App\Services\Flickr\Objects
 */
class FlickrGallery extends FlickrDownload
{
    private ?object $gallery;

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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->gallery->gallery->description ?? null;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->gallery !== null;
    }

    protected function notification()
    {
        // @todo Notification in even not job
        UserActivity::notify(
            '%s request %s gallery',
            Auth::user(),
            'download',
            [
                'object_id' => $this->getId(),
                'extra' => [
                    'title' => $this->getTitle(),
                    // Fields are displayed in a table on the message
                    'fields' => [
                        'ID' => $this->getId(),
                        'Photos' => $this->getPhotosCount(),
                        'Owner' => $this->getOwner(),
                        'Sync to Google' => $this->getTitle().' ['.$this->googleAlbum->id.']'
                    ],
                    'footer' => $this->getDescription(),
                ],
            ]
        );
    }
}
