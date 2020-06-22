<?php

namespace App\Jobs\Traits;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrContact;
use App\Repositories\Flickr\PhotoRepository;
use Laminas\Hydrator\ObjectPropertyHydrator;

trait HasPhotoSizes
{
    /**
     * @param array $photos
     * @param bool $shouldProcessOwner
     *
     * @throws \App\Exceptions\Flickr\FlickrApiPhotoGetSizesException
     */
    private function processGetSizesOfPhotos(array $photos, bool $shouldProcessOwner = false): void
    {
        $photoRepository = app(PhotoRepository::class);
        $hydrator = new ObjectPropertyHydrator();

        foreach ($photos as $photo) {
            $photoModel = $photoRepository->findOrCreateById($photo->id);

            if ($photoModel->hasSizes()) {
                continue;
            }

            $sizes = FlickrClient::getPhotoSizes($photoModel->id);
            $photo->sizes = $sizes->sizes->size;
            $photoModel->fill($hydrator->extract($photo))->save();

            if (false === $shouldProcessOwner) {
                continue;
            }

            FlickrContact::dispatch($photoModel->owner);
        }
    }
}
