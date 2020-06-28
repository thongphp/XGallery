<?php

namespace App\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Facades\GooglePhotoClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Jobs\Traits\SyncPhotos;
use App\Repositories\Flickr\ContactRepository;
use App\Services\Flickr\Objects\FlickrAlbum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class FlickrDownloadAlbum
 * @package App\Jobs\Flickr
 */
class FlickrDownloadAlbum implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob, SyncPhotos;

    private object $album;

    /**
     * @param  FlickrAlbum  $album
     */
    public function __construct(FlickrAlbum $album)
    {
        $this->album = $album;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @throws \App\Exceptions\Google\GooglePhotoApiAlbumCreateException
     * @throws \JsonException
     */
    public function handle(): void
    {
        $photos = $this->album->getPhotos();
        $googleAlbum = GooglePhotoClient::createAlbum($this->album->getTitle());

        $googleAlbumId = $googleAlbum->id;
        $owner = $this->album->getOwner();

        // If owner is not exist, start new queue for getting this contact information.
        if (!app(ContactRepository::class)->isExist($owner)) {
            FlickrContact::dispatch($owner);
        }

        $this->syncPhotos($photos->photoset->photo, $owner, $googleAlbumId);

        if ($photos->photoset->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photoset->pages; $page++) {
            if (!$nextPhotos = FlickrClient::getPhotoSetPhotos($this->album->getId(), $page)) {
                continue;
            }

            $this->syncPhotos($nextPhotos->photoset->photo, $owner, $googleAlbumId);
        }
    }
}
