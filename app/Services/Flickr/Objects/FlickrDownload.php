<?php

namespace App\Services\Flickr\Objects;

use App\Jobs\Flickr\FlickrContact;
use App\Jobs\Flickr\FlickrDownloadPhoto;
use App\Repositories\Flickr\ContactRepository;
use App\Services\Flickr\Url\FlickrUrlInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class FlickrDownload implements FlickrObjectInterface
{
    protected Collection $photos;

    /**
     * @var FlickrUrlInterface
     */
    private FlickrUrlInterface $flickrUrl;

    /**
     * FlickrAlbum constructor.
     * @param  FlickrUrlInterface  $flickrUrl
     */
    public function __construct(FlickrUrlInterface $flickrUrl)
    {
        $this->flickrUrl = $flickrUrl;
        $this->photos = collect([]);

        $this->load();
    }

    public function getType(): string
    {
        return $this->flickrUrl->getType();
    }

    public function download(): bool
    {
        // If owner is not exist, start new queue for getting this contact information.
        if (!app(ContactRepository::class)->isExist($this->getOwner())) {
            FlickrContact::dispatch($this->getOwner());
        }

        // Create download request
        $download = \App\Models\Flickr\FlickrDownload::firstOrCreate([
            'user_id' => Auth::user()->getAuthIdentifier(),
            'type' => $this->getType(),
            'name' => $this->getTitle(),
            'photos_count' => $this->getPhotosCount(),
            'processed' => 0 // Init default value
        ]);

        /**
         * Extract photos to xref
         * Actually getPhotos would make multi request depends on number of page but assumed not too much
         */
        $photos = $this->getPhotos();

        $photos->each(function ($photo) use ($download) {
            FlickrDownloadPhoto::dispatch($this, $download, $photo);
        });

        return true;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->flickrUrl->getOwner();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->flickrUrl->getId();
    }

    public function getUrl(): string
    {
        return $this->flickrUrl->getUrl();
    }

    abstract protected function load(): bool;
}
