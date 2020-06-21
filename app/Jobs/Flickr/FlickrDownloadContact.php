<?php

namespace App\Jobs\Flickr;

use App\Facades\Flickr;
use App\Facades\GooglePhotoFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Jobs\Traits\SyncPhotos;
use App\Models\Flickr\Contact;
use App\Repositories\Flickr\ContactRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laminas\Hydrator\ObjectPropertyHydrator;

class FlickrDownloadContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob, SyncPhotos;

    private string $nsid;

    /**
     * @param string $nsid
     */
    public function __construct(string $nsid)
    {
        $this->nsid = $nsid;
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
     * @throws \Exception
     */
    public function handle(): void
    {
        $contactModel = app(ContactRepository::class)->findOrCreateByNsId($this->nsid);
        $contactModel->touch();

        if (!$contactModel->isDone()) {
            $userInfo = Flickr::getUserInfo($contactModel->nsid);

            if (!$userInfo) {
                throw new Exception('Can not get user information of: '.$contactModel->nsid);
            }

            $contactModel->fill((new ObjectPropertyHydrator())->extract($userInfo->person))
                ->setAttribute(Contact::KEY_STATUS, true)
                ->save();
        }

        if (!$photos = Flickr::getUserPhotos($contactModel->nsid)) {
            throw new Exception('Can not get photos of contact: '.$contactModel->nsid);
        }

        $googleAlbum = GooglePhotoFacade::createAlbum($contactModel->nsid);

        if (!$googleAlbum) {
            throw new Exception('Can not create Google FlickrAlbumDownloadQueue: '.$contactModel->nsid);
        }

        $this->syncPhotos($photos->photos->photo, $contactModel->nsid, $googleAlbum->id);

        if ($photos->photos->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            if (!$nextPhotos = Flickr::getUserPhotos($contactModel->nsid, $page)) {
                continue;
            }

            $this->syncPhotos($nextPhotos->photos->photo, $contactModel->nsid, $googleAlbum->id);
        }
    }
}
