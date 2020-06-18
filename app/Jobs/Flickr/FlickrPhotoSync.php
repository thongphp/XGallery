<?php

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Facades\Flickr;
use App\Facades\GooglePhoto;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\FlickrAlbum;
use App\Models\FlickrPhoto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FlickrPhotoSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private object $photo;
    private FlickrAlbum $flickrAlbum;

    /**
     * @param object $photo
     * @param FlickrAlbum $flickrAlbum
     */
    public function __construct(object $photo, FlickrAlbum $flickrAlbum)
    {
        $this->photo = $photo;
        $this->flickrAlbum = $flickrAlbum;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @return RateLimited[]
     */
    public function middleware(): array
    {
        return [new RateLimited('flickr')];
    }

    public function handle(): void
    {
        $sizes = Flickr::get('photos.getSizes', ['photo_id' => $this->photo->id]);

        if (!$sizes || $sizes->sizes->candownload !== 1) {
            return;
        }

        $photo = $this->getPhoto($sizes);
        $status = (bool) $photo->getAttributeValue('status');

        if ($status === true) {
            return;
        }

        $result = $this->downloadPhoto($photo->getAttributeValue('sizes'));

        if (is_string($result)) {
            // Download success. Upload to Google Photos
            GooglePhoto::uploadMedia($result, $photo, $this->flickrAlbum);
        }
    }

    /**
     * @param object $sizes
     *
     * @return \App\Models\FlickrPhoto
     */
    private function getPhoto(object $sizes): FlickrPhoto
    {
        /** @var \App\Repositories\FlickrPhotos $photoRepository */
        $photoRepository = app(\App\Repositories\FlickrPhotos::class);

        /** @var \App\Models\FlickrPhoto $photo */
        $photo = $photoRepository->findById($this->photo->id);

        if (!$photo) {
            $photo = $photoRepository->save((array) $this->photo);
        }

        $photoSizes = $photo->getAttributeValue('sizes');

        if (empty($photoSizes)) {
            $photo->setAttribute('sizes', $sizes->sizes->size);
            $photo->setAttribute('albumId', $this->flickrAlbum->getAttributeValue('id'));
            $photo->save();
        }

        return $photo;
    }

    /**
     * @param array $photoSizes
     *
     * @return bool|string
     */
    private function downloadPhoto(array $photoSizes)
    {
        $sourceSize = end($photoSizes);

        /** @var HttpClient $httpClient */
        $httpClient = app(HttpClient::class);

        return $httpClient->download(
            $sourceSize['source'],
            'flickr/'.$this->flickrAlbum->getAttributeValue('owner')
        );
    }
}
