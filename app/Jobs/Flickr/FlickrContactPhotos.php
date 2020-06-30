<?php

namespace App\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Facades\FlickrValidate;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Repositories\Flickr\PhotoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class FlickrContactPhotos
 * @package App\Jobs\Flickr
 */
class FlickrContactPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

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
     */
    public function handle(): void
    {
        if (!FlickrValidate::validateNsId($this->nsid)) {
            return;
        }

        $photos = FlickrClient::getPeoplePhotos($this->nsid);
        $this->storePhotos($photos->photos->photo);

        if ($photos->photos->pages === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            $nextPhotos = FlickrClient::getPeoplePhotos($this->nsid, $page);
            $this->storePhotos($nextPhotos->photos->photo);
        }
    }

    /**
     * @param array $photos
     */
    private function storePhotos(array $photos)
    {
        $repository = app(PhotoRepository::class);
        foreach ($photos as $photo) {
            $repository->findOrCreateByIdWithData(get_object_vars($photo));
        }
    }
}
