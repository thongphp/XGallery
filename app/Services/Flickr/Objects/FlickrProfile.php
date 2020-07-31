<?php

namespace App\Services\Flickr\Objects;

use App\Facades\FlickrClient;
use Illuminate\Support\Collection;

/**
 * Class FlickrProfile
 * @package App\Services\Flickr\Objects
 */
class FlickrProfile extends FlickrDownload
{
    private ?object $profile;

    /**
     * @return int
     */
    public function getPhotosCount(): int
    {
        return (int) $this->profile->photos->count;
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
            $photos = FlickrClient::getPeoplePhotos($this->getId(), $page);
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

    public function getDescription(): ?string
    {
        return null;
    }

    /**
     * @return bool
     */
    protected function load(): bool
    {
        $this->profile = FlickrClient::getPeopleInfo($this->getId());

        return $this->isValid();
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->profile !== null;
    }
}
