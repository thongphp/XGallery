<?php

namespace App\Services\Flickr\Objects;

use App\Facades\FlickrClient;
use App\Facades\UserActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class FlickrProfile
 * @package App\Services\Flickr\Objects
 */
class FlickrProfile extends \App\Services\Flickr\Objects\FlickrDownload
{
    private ?object $profile;

    /**
     * @return bool
     */
    public function load(): bool
    {
        $this->profile = $userInfo = FlickrClient::getPeopleInfo($this->id);
        return $this->isValid();
    }

    /**
     * @return int
     */
    public function getPhotosCount(): int
    {
        return (int) $this->profile->photos->count;
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
            $photos = FlickrClient::getPeoplePhotos($this->id, $page);
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
        return $this->profile->realname ?? $this->profile->username;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->profile !== null;
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function notification()
    {
        // @todo Notification in even not job
        UserActivity::notify(
            '%s request %s profile',
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
                ],
            ]
        );
    }
}
