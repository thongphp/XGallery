<?php

namespace App\Jobs\Traits;

use App\Jobs\Flickr\FlickrDownloadPhotoToLocal;
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\PhotoRepository;
use Laminas\Hydrator\ObjectPropertyHydrator;

/**
 * Trait SyncPhotos
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

        foreach ($photos as $photo) {
            $photoModel = $photoRepository->findOrCreateById($photo->id);
            $photoModel->fill($hydrator->extract($photo))
                ->setAttribute(Photo::KEY_OWNER, $owner)
                ->save();

            FlickrDownloadPhotoToLocal::dispatch($photoModel->id, $googleAlbumId);
        }
    }
}
