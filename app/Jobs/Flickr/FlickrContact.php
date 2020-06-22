<?php

namespace App\Jobs\Flickr;

use App\Exceptions\Flickr\FlickrApiPeopleGetInfoInvalidUserException;
use App\Exceptions\Flickr\FlickrApiPeopleGetInfoUserDeletedException;
use App\Facades\FlickrClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\Contact;
use App\Repositories\Flickr\ContactRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laminas\Hydrator\ObjectPropertyHydrator;

/**
 * Class FlickrContact
 * @package App\Jobs\Flickr
 */
class FlickrContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

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
     * @throws \App\Exceptions\Flickr\FlickrApiPeopleGetInfoException
     */
    public function handle(): void
    {
        if (!FlickrClient::validateNsId($this->nsid)) {
            return;
        }

        $contactModel = app(ContactRepository::class)->findOrCreateByNsId($this->nsid);

        try {
            $userInfo = FlickrClient::getPeopleInfo($contactModel->nsid);
        } catch (FlickrApiPeopleGetInfoInvalidUserException $exception) {
            $contactModel->delete();
            return;
        } catch (FlickrApiPeopleGetInfoUserDeletedException $exception) {
            $contactModel->delete();

            return;
        }

        $contactModel->fill((new ObjectPropertyHydrator())->extract($userInfo))
            ->save();
    }
}
