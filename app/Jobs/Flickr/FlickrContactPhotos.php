<?php

namespace App\Jobs\Flickr;

use App\Exceptions\Flickr\FlickrApiGetContactPhotosException;
use App\Facades\Flickr;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Jobs\Traits\HasPhotoSizes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FlickrContactPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob, HasPhotoSizes;

    private string $nsid;

    /**
     * @param string $nsId
     */
    public function __construct(string $nsId)
    {
        $this->nsid = $nsId;
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
     * @throws \App\Exceptions\Flickr\FlickrApiGetContactPhotosException
     * @throws \App\Exceptions\Flickr\FlickrApiGetPhotoSizesException
     */
    public function handle(): void
    {
        if (!$photos = Flickr::getUserPhotos($this->nsid)) {
            throw new FlickrApiGetContactPhotosException($this->nsid);
        }

        $this->processGetSizesOfPhotos($photos->photos->photo);

        if ($photos->photos->pages === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            if (!$nextPhotos = Flickr::getUserPhotos($this->nsid, $page)) {
                continue;
            }

            $this->processGetSizesOfPhotos($nextPhotos->photos->photo);
        }
    }
}
