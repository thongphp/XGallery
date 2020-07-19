<?php

namespace App\Services\Flickr\Objects;

use App\Exceptions\Flickr\FlickrApiPhotoSetsGetInfoException;
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
 * Class FlickrAlbum
 * @todo Reduce duplicate code
 * @package App\Services\Flickr\Objects
 */
class FlickrAlbum
{
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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->album->photoset->description ?? null;
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
            '%s request %s album',
            Auth::user(),
            'download',
            [
                'object_id' => $this->getId(),
                'extra' => [
                    'title' => $this->getTitle(),
                    'title_link' => 'https://www.flickr.com/photos/'.$this->getOwner().'/albums/'.$this->getId(),
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
