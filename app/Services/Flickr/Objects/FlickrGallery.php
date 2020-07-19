<?php

namespace App\Services\Flickr\Objects;

use App\Exceptions\Flickr\FlickrApiGalleryGetInfoException;
use App\Facades\FlickrClient;
use App\Facades\GooglePhotoClient;
use App\Facades\UserActivity;
use App\Jobs\Flickr\FlickrContact;
use App\Models\Flickr\FlickrDownload;
use App\Models\Flickr\FlickrPhotoModel;
use App\Repositories\Flickr\ContactRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class FlickrGallery
 * @todo Reduce duplicate code
 * @package App\Services\Flickr\Objects
 */
class FlickrGallery
{
    private string $id;
    private ?object $gallery;
    private Collection $photos;

    /**
     * FlickrGallery constructor.
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

    public function download()
    {
        $googleAlbum = GooglePhotoClient::createAlbum($this->getTitle());
        $googleAlbumId = $googleAlbum->id;

        // If owner is not exist, start new queue for getting this contact information.
        if (!app(ContactRepository::class)->isExist($this->getOwner())) {
            FlickrContact::dispatch($this->getOwner());
        }

        $this->getPhotos()->each(function ($photo) use ($googleAlbumId) {
            // Store photo
            $photo = FlickrPhotoModel::updateOrCreate(
                ['id' => $photo->id],
                array_merge(get_object_vars($photo), [FlickrPhotoModel::KEY_OWNER => $this->getOwner()])
            );

            FlickrDownload::firstOrCreate(
                array_merge(['user_id' => Auth::id()], ['photo_id' => $photo->id, 'google_album_id' => $googleAlbumId])
            );
        });

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
                        'Sync to Google' => $this->getTitle().' ['.$googleAlbumId.']'
                    ],
                    'footer' => $this->getDescription(),
                ],
            ]
        );
    }
}
