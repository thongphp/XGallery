<?php

namespace App\Jobs\Flickr;

use App\Exceptions\Flickr\FlickrApiPeopleGetInfoInvalidUserException;
use App\Exceptions\Flickr\FlickrApiPeopleGetInfoUserDeletedException;
use App\Facades\FlickrClient;
use App\Facades\FlickrValidate;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
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
        if (!FlickrValidate::validateNsId($this->nsid)) {
            return;
        }

        try {
            $userInfo = FlickrClient::getPeopleInfo($this->nsid);
            $contactModel = app(ContactRepository::class)->findOrCreateByNsId($this->nsid);
            $contactModel->fill((new ObjectPropertyHydrator())->extract($userInfo))->save();
            $contactModel->{\App\Models\Flickr\FlickrContact::KEY_STATE} = \App\Models\Flickr\FlickrContact::STATE_CONTACT_DETAIL;
            $contactModel->save();
        } catch (FlickrApiPeopleGetInfoInvalidUserException $exception) {
            // This user is not valid than do nothing
            return;
        } catch (FlickrApiPeopleGetInfoUserDeletedException $exception) {
            // Do delete deleted user
            if ($model = \App\Models\Flickr\FlickrContact::where([\App\Models\Flickr\FlickrContact::KEY_NSID => $this->nsid])->first()) {
                $model->delete();
            }
            return;
        }
    }
}
