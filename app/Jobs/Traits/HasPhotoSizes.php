<?php

namespace App\Jobs\Traits;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrContact;
use App\Models\Flickr\FlickrPhoto;
use Laminas\Hydrator\ObjectPropertyHydrator;

trait HasPhotoSizes
{
    /**
     * @param array $photos
     * @param bool $shouldProcessOwner
     */
    private function processGetSizesOfPhotos(array $photos, bool $shouldProcessOwner = false): void
    {
        $hydrator = new ObjectPropertyHydrator();

        foreach ($photos as $photo) {
            $photoModel = FlickrPhoto::firstOrCreate([FlickrPhoto::KEY_ID => $photo->id]);

            // Photo already has sizes
            if ($photoModel->hasSizes()) {
                continue;
            }

            $photo->sizes = FlickrClient::getPhotoSizes($photoModel->id)->sizes->size;
            $photoModel->fill($hydrator->extract($photo))->save();

            if (false === $shouldProcessOwner) {
                continue;
            }

            FlickrContact::dispatch($photoModel->owner);
        }
    }
}
