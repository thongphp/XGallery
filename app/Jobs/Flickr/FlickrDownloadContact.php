<?php

namespace App\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Facades\GooglePhotoClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Jobs\Traits\SyncPhotos;
use App\Models\Flickr\FlickrContactModel;
use App\Repositories\Flickr\ContactRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laminas\Hydrator\ObjectPropertyHydrator;

/**
 * Class FlickrDownloadContact
 * @package App\Jobs\Flickr
 */
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

    public function handle(): void
    {
        $userInfo = FlickrClient::getPeopleInfo($this->nsid);
        $contactModel = app(ContactRepository::class)->findOrCreateByNsId($this->nsid);
        $contactModel->fill((new ObjectPropertyHydrator())->extract($userInfo))
            ->setAttribute(FlickrContactModel::KEY_STATE, FlickrContactModel::STATE_CONTACT_DETAIL)
            ->save();

        $photos = FlickrClient::getPeoplePhotos($contactModel->nsid);
        $googleAlbum = GooglePhotoClient::createAlbum($contactModel->nsid);

        $this->syncPhotos($photos->photos->photo, $contactModel->nsid, $googleAlbum->id);

        if ($photos->photos->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            if (!$nextPhotos = FlickrClient::getPeoplePhotos($contactModel->nsid, $page)) {
                continue;
            }

            $this->syncPhotos($nextPhotos->photos->photo, $contactModel->nsid, $googleAlbum->id);
        }
    }
}
