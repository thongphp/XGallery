<?php

namespace App\Jobs\Flickr;

use App\Exceptions\Flickr\FlickrApiGetAlbumPhotosException;
use App\Facades\Flickr;
use App\Facades\GooglePhotoFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Jobs\Traits\SyncPhotos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FlickrDownloadAlbum implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob, SyncPhotos;

    private object $album;

    /**
     * @param object $album
     */
    public function __construct(object $album)
    {
        $this->album = $album;
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
     * @throws \App\Exceptions\Flickr\FlickrApiGetAlbumPhotosException
     * @throws \App\Exceptions\Google\GooglePhotoApiCreateAlbumException
     * @throws \JsonException
     */
    public function handle(): void
    {
        if (!$photos = Flickr::getAlbumPhotos($this->album->id)) {
            throw new FlickrApiGetAlbumPhotosException($this->album->id);
        }

        $googleAlbum = GooglePhotoFacade::createAlbum($this->album->title);
        $googleAlbumId = $googleAlbum->id;
        $owner = $this->album->owner;

        FlickrContact::dispatch($owner);

        $this->syncPhotos($photos->photoset->photo, $owner, $googleAlbumId);

        if ($photos->photoset->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photoset->pages; $page++) {
            if (!$nextPhotos = Flickr::getAlbumPhotos($this->album->id, $page)) {
                continue;
            }

            $this->syncPhotos($nextPhotos->photoset->photo, $owner, $googleAlbumId);
        }
    }
}
