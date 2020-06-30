<?php

namespace App\Jobs\Flickr;

use App\Facades\GooglePhotoClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Jobs\Traits\SyncPhotos;
use App\Services\Flickr\Objects\FlickrGallery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FlickrDownloadGallery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob, SyncPhotos;

    private FlickrGallery $gallery;

    /**
     * @param  FlickrGallery  $gallery
     */
    public function __construct(FlickrGallery $gallery)
    {
        $this->gallery = $gallery;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    public function handle(): void
    {
        $googleAlbum = GooglePhotoClient::createAlbum($this->gallery->getTitle());
        $googleAlbumId = $googleAlbum->id;
        $owner = $this->gallery->getOwner();

        FlickrContact::dispatch($owner);

        $this->syncPhotos($this->gallery->getPhotos()->toArray(), $owner, $googleAlbumId);
    }
}
