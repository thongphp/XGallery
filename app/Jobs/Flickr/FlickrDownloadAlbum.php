<?php

namespace App\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Facades\GooglePhotoClient;
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
     */
    public function handle(): void
    {
        $photos = FlickrClient::getPhotoSetPhotos($this->album->id);
        $googleAlbum = GooglePhotoClient::createAlbum($this->album->title);
        $googleAlbumId = $googleAlbum->id;
        $owner = $this->album->owner;

        FlickrContact::dispatch($owner);

        $this->syncPhotos($photos->photoset->photo, $owner, $googleAlbumId);

        if ($photos->photoset->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photoset->pages; $page++) {
            if (!$nextPhotos = FlickrClient::getPhotoSetPhotos($this->album->id, $page)) {
                continue;
            }

            $this->syncPhotos($nextPhotos->photoset->photo, $owner, $googleAlbumId);
        }
    }
}
