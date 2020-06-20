<?php

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Facades\Flickr;
use App\Facades\GooglePhotoFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\Album;
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\PhotoRepository;
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
        if (!$photo = app(PhotoRepository::class)->findById($this->photoId)) {
            return;
        }

        $status = (bool) $photo->getAttributeValue('status');

        if ($status === true) {
            return;
        }

        if (!$filePath = $this->downloadPhoto($this->getPhotoSizes($photo), $photo->{Photo::KEY_OWNER_ID})) {
            return;
        }

        if (!$result = GooglePhotoFacade::uploadAndCreateMedia($filePath, $photo->id)) {
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

    private function getPhotoSizes(Photo $photo): array
    {
        if (!empty($photo->sizes)) {
            return $photo->sizes;
        }

        if (!$sizes = Flickr::getPhotoSizes($photo->getAttributeValue('id'))) {
            return [];
        }

        $photo->setAttribute('sizes', $sizes->sizes->size);
        $photo->save();

        return $photo->sizes;
    }

    /**
     * @param \App\Models\Flickr\Album $album
     *
     * @return void
     */
    private function calculateDownloadedPhotos(Album $album): void
    {
        $album->increment('download_photos', 1);

        if ($album->getAttributeValue('download_photos') !== $album->getAttributeValue('count_photos')) {
            return;
        }

        $album->setAttribute('status', true);
        $album->save();
    }
}
