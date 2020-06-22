<?php

namespace App\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Facades\GooglePhotoClient;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Jobs\Traits\SyncPhotos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FlickrDownloadGallery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob, SyncPhotos;

    private object $gallery;

    /**
     * @param object $gallery
     */
    public function __construct(object $gallery)
    {
        $this->gallery = $gallery;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @return RateLimited[]
     */
    public function middleware(): array
    {
        return [new RateLimited('flickr')];
    }

    /**
     * @throws \App\Exceptions\Flickr\FlickrApiGalleryGetPhotosException
     * @throws \App\Exceptions\Google\GooglePhotoApiAlbumCreateException
     * @throws \JsonException
     */
    public function handle(): void
    {
        $photos = FlickrClient::getGalleryPhotos($this->gallery->id);
        $googleAlbum = GooglePhotoClient::createAlbum($this->gallery->title);
        $googleAlbumId = $googleAlbum->id;
        $owner = $this->gallery->owner;

        FlickrContact::dispatch($owner);

        $this->syncPhotos($photos->photos->photo, $owner, $googleAlbumId);

        if ($photos->photos->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            if (!$nextPhotos = FlickrClient::getGalleryPhotos($this->gallery->id, $page)) {
                continue;
            }

            $this->syncPhotos($nextPhotos->photos->photo, $owner, $googleAlbumId);
        }
    }
}
