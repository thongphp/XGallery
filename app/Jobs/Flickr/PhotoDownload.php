<?php

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Facades\Flickr;
use App\Facades\GooglePhotoFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\FlickrAlbum;
use App\Models\FlickrPhoto;
use App\Repositories\FlickrPhotoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;

class PhotoDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $photoId;

    /**
     * @param string $photoId
     */
    public function __construct(string $photoId)
    {
        $this->photoId = $photoId;
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
        $photo = app(FlickrPhotoRepository::class)->findById($this->photoId);

        if (!$photo) {
            return;
        }

        $status = (bool) $photo->getAttributeValue('status');

        if ($status === true) {
            return;
        }

        $filePath = $this->downloadPhoto($this->getPhotoSizes($photo), $photo->{FlickrPhoto::KEY_OWNER_ID});

        if (!$filePath) {
            return;
        }

        $result = GooglePhotoFacade::uploadAndCreateMedia($filePath, $photo->id);

        if (!$result) {
            return;
        }

        Storage::delete($filePath);

        if ($photo->album) {
            $this->calculateDownloadedPhotos($photo->album);
        }
    }

    /**
     * @param array $photoSizes
     * @param string $owner
     *
     * @return bool|string
     */
    private function downloadPhoto(array $photoSizes, string $owner)
    {
        $sourceSize = end($photoSizes);
        $httpClient = app(HttpClient::class);

        return $httpClient->download(
            $sourceSize['source'],
            'flickr/'.$owner
        );
    }

    private function getPhotoSizes(FlickrPhoto $photo): array
    {
        $photoSizes = $photo->getAttributeValue('sizes');

        if (!empty($photoSizes)) {
            return $photoSizes;
        }

        $sizes = Flickr::getPhotoSizes($photo->getAttributeValue('id'));

        if (!$sizes) {
            return [];
        }

        $photo->setAttribute('sizes', $sizes->sizes->size);
        $photo->save();

        return $photo->getAttributeValue('sizes');
    }

    /**
     * @param \App\Models\FlickrAlbum $album
     *
     * @return void
     */
    private function calculateDownloadedPhotos(FlickrAlbum $album): void
    {
        $album->increment('download_photos', 1);

        if ($album->getAttributeValue('download_photos') !== $album->getAttributeValue('count_photos')) {
            return;
        }

        $album->setAttribute('status', true);
        $album->save();
    }
}
