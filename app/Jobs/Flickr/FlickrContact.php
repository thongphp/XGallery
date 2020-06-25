<?php

namespace App\Jobs\Flickr;

use App\Exceptions\Flickr\FlickrApiPeopleGetInfoInvalidUserException;
use App\Exceptions\Flickr\FlickrApiPeopleGetInfoUserDeletedException;
use App\Facades\FlickrClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\Contact;
use App\Repositories\Flickr\ContactRepository;
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
     * @param  string  $nsid
     */
    public function __construct(string $nsid)
    {
        $this->nsid = $nsid;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    public function handle(): void
    {
        // @todo Create FlickrValidate as facade instead FlickrClient
        if (!FlickrClient::validateNsId($this->nsid)) {
            return;
        }

        try {
            $userInfo = FlickrClient::getPeopleInfo($this->nsid);
            $contactModel = app(ContactRepository::class)->findOrCreateByNsId($this->nsid);
            $contactModel->fill((new ObjectPropertyHydrator())->extract($userInfo))->save();
            $contactModel->state = Contact::STATE_CONTACT_DETAIL;
            $contactModel->save();
        } catch (FlickrApiPeopleGetInfoInvalidUserException $exception) {
            // This user is not valid than do nothing
            return;
        } catch (FlickrApiPeopleGetInfoUserDeletedException $exception) {
            // Do delete deleted user
            Contact::where(['nsid' => $this->nsid])->first()->delete();
            return;
        }
    }
}
