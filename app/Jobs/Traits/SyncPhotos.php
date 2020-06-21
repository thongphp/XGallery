<?php

namespace App\Jobs\Traits;

use App\Jobs\Flickr\FlickrSyncToGooglePhoto;
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\PhotoRepository;
use Laminas\Hydrator\ObjectPropertyHydrator;

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

            if ($photoModel->isDone()) {
                continue;
            }

            $photoModel->touch();

            $photoModel->fill($hydrator->extract($photo))
                ->setAttribute(Photo::KEY_OWNER, $owner)
                ->save();

            FlickrSyncToGooglePhoto::dispatch($photoModel->id, $googleAlbumId);
        }
    }
}
