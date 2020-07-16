<?php

namespace App\Jobs\Traits;

use App\Jobs\Flickr\FlickrDownloadPhotoToLocal;
use App\Models\Flickr\FlickrPhotoModel;
use App\Models\User;
use App\Repositories\Flickr\PhotoRepository;
use Laminas\Hydrator\ObjectPropertyHydrator;

/**
 * Trait SyncPhotos
 * @property User $user
 * @package App\Jobs\Traits
 */
trait SyncPhotos
{
    /**
     * @param array $photos
     * @param string $owner
     * @param string $googleAlbumId
     */
    private function syncPhotos(array $photos, string $owner, string $googleAlbumId): void
    {
        $photoRepository = app(PhotoRepository::class);
        $hydrator = new ObjectPropertyHydrator();

        // Store photos into database and trigger download
        foreach ($photos as $photo) {
            $photoModel = $photoRepository->findOrCreateById($photo->id);
            $photoModel->fill($hydrator->extract($photo))
                ->setAttribute(FlickrPhotoModel::KEY_OWNER, $owner)
                ->save();

            FlickrDownloadPhotoToLocal::dispatch($photoModel->id, $googleAlbumId);
        }
    }
}
