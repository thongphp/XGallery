<?php

namespace App\Jobs\Flickr;

use App\Facades\Flickr;
use App\Facades\GooglePhoto;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Repositories\FlickrAlbums;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FlickrAlbum implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

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

    public function handle(): void
    {
        $photos = Flickr::get('photosets.getPhotos', ['photoset_id' => $this->album->id]);

        if (!$photos) {
            return;
        }

        /** @var FlickrAlbums $repository */
        $repository = app(FlickrAlbums::class);
        $flickrAlbum = $repository->findByAlbumId($this->album->id);

        if (!$flickrAlbum) {
            $flickrAlbum = $repository->save((array) $this->album);
        }

        $googleAlbum = GooglePhoto::createAlbum($this->album->title->_content);

        $flickrAlbum->setAttribute('googleRef', $googleAlbum);
        $flickrAlbum->save();

//        foreach ($photos->photoset->photo as $photo) {
//            FlickrDownload::dispatch($photos->photoset->owner, $photo);
//        }
//
//        if ($photos->photoset->page === 1) {
//            return;
//        }
//
//        for ($page = 2; $page <= $photos->photoset->pages; $page++) {
//            $photos = Flickr::get('photosets.getPhotos', ['photoset_id' => $this->album->id, 'page' => $page]);
//            foreach ($photos->photoset->photo as $photo) {
//                FlickrDownload::dispatch($photos->photoset->owner, $photo);
//            }
//        }
    }
}
