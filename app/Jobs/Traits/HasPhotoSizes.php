<?php

namespace App\Jobs\Traits;

use App\Exceptions\Flickr\FlickrApiGetPhotoSizesException;
use App\Facades\Flickr;
use App\Jobs\Flickr\FlickrContact;
use App\Repositories\Flickr\ContactRepository;
use App\Repositories\Flickr\PhotoRepository;
use Laminas\Hydrator\ObjectPropertyHydrator;

trait HasPhotoSizes
{
    /**
     * @param array $photos
     * @param bool $shouldProcessOwner
     *
     * @throws FlickrApiGetPhotoSizesException
     */
    private function processGetSizesOfPhotos(array $photos, bool $shouldProcessOwner = false): void
    {
        /** @var PhotoRepository $photoRepository */
        $photoRepository = app(PhotoRepository::class);
        $contactRepository = app(ContactRepository::class);
        $hydrator = new ObjectPropertyHydrator();

        foreach ($photos as $photo) {
            $photoModel = $photoRepository->findOrCreateById($photo->id);

            if ($photoModel->isDone() || $photoModel->hasSizes()) {
                continue;
            }

            $photoModel->touch();

            if (!$sizes = Flickr::getPhotoSizes($photoModel->id)) {
                throw new FlickrApiGetPhotoSizesException($photoModel->id);
            }

            $photo->sizes = $sizes->sizes->size;

            $photoModel->fill($hydrator->extract($photo))->save();

            if (false === $shouldProcessOwner
                || $contactRepository->findOrCreateByNsId($photoModel->owner)->isDone()) {
                continue;
            }

            FlickrContact::dispatch($photoModel->owner);
        }
    }
}
