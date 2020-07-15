<?php

namespace App\Jobs\Flickr;

use App\Facades\GooglePhotoClient;
use App\Facades\UserActivity;
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

    private FlickrAlbum $album;

    /**
     * @param  FlickrAlbum  $album
     */
    public function __construct(FlickrAlbum $album)
    {
        $this->album = $album;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @throws \JsonException
     */
    public function handle(): void
    {
        $owner = $this->album->getOwner();

        $googleAlbum = GooglePhotoClient::createAlbum($this->album->getTitle());
        $googleAlbumId = $googleAlbum->id;

        // If owner is not exist, start new queue for getting this contact information.
        if (!app(ContactRepository::class)->isExist($owner)) {
            FlickrContact::dispatch($owner);
        }

        $photos = $this->album->getPhotos()->toArray();
        $this->syncPhotos($photos, $owner, $googleAlbumId);
        UserActivity::notify('%s %s '.count($photos).' photos', 'download');
    }
}
