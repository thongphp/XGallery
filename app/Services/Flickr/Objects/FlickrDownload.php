<?php

namespace App\Services\Flickr\Objects;

use App\Facades\GooglePhotoClient;
use App\Jobs\Flickr\FlickrContact;
use App\Models\Flickr\FlickrPhotoModel;
use App\Repositories\Flickr\ContactRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class FlickrDownload
{
    protected string $id;
    protected Collection $photos;
    protected $googleAlbum;

    /**
     * FlickrAlbum constructor.
     * @param  string  $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->photos = collect([]);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function download()
    {
        $this->googleAlbum = $this->createGoogleAlbum();

        // If owner is not exist, start new queue for getting this contact information.
        if (!app(ContactRepository::class)->isExist($this->getOwner())) {
            FlickrContact::dispatch($this->getOwner());
        }

        $this->getPhotos()->each(function ($photo) {
            // Store photo
            $photo = FlickrPhotoModel::updateOrCreate(
                ['id' => $photo->id],
                array_merge(get_object_vars($photo), [FlickrPhotoModel::KEY_OWNER => $this->getOwner()])
            );

            \App\Models\Flickr\FlickrDownload::firstOrCreate(
                array_merge(['user_id' => Auth::id()], ['photo_id' => $photo->id, 'google_album_id' => $this->googleAlbum->id])
            );
        });

        $this->notification();
    }

    /**
     * @return mixed
     * @throws \JsonException
     * @todo Check if Album already exists
     */
    private function createGoogleAlbum(): object
    {
        return GooglePhotoClient::createAlbum($this->getTitle());
    }

    abstract public function load(): bool;
    abstract public function isValid(): bool;
    abstract public function getPhotosCount(): int;
    abstract public function getPhotos(): ?Collection;
    abstract public function getTitle(): string;
    abstract public function getOwner(): string;
    abstract public function getDescription(): ?string;
    abstract protected function notification();
}
