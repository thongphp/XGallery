<?php

namespace App\Jobs\Flickr;

use App\Facades\FlickrClient;
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
     * @throws \App\Exceptions\Flickr\FlickrApiPeopleGetPhotosException
     * @throws \App\Exceptions\Flickr\FlickrApiPhotoGetSizesException
     */
    public function handle(): void
    {
        if (!FlickrClient::validateNsId($this->nsid)) {
            return;
        }

        $photos = FlickrClient::getPeoplePhotos($this->nsid);

        $this->processGetSizesOfPhotos($photos->photos->photo);

        if ($photos->photos->pages === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            if (!$nextPhotos = FlickrClient::getPeoplePhotos($this->nsid, $page)) {
                continue;
            }

            $this->processGetSizesOfPhotos($nextPhotos->photos->photo);
        }
    }
}
