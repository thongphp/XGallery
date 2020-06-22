<?php

namespace App\Jobs\Flickr;

use App\Exceptions\Flickr\FlickrApiGetContactFavouritePhotosException;
use App\Exceptions\Flickr\FlickrApiGetPhotoSizesException;
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

class FlickrContactFavouritePhotos implements ShouldQueue
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
     * @throws FlickrApiGetContactFavouritePhotosException
     * @throws FlickrApiGetPhotoSizesException
     */
    public function handle(): void
    {
        if (!$photos = Flickr::getFavouritePhotosOfUser($this->nsid)) {
            throw new FlickrApiGetContactFavouritePhotosException($this->nsid);
        }

        $this->processGetSizesOfPhotos($photos->photos->photo, true);

        if ($photos->photos->pages === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            if (!$nextPhotos = Flickr::getFavouritePhotosOfUser($this->nsid, $page)) {
                continue;
            }

            $this->processGetSizesOfPhotos($nextPhotos->photos->photo, true);
        }
    }
}
