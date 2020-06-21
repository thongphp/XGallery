<?php

namespace App\Jobs\Flickr;

use App\Exceptions\Flickr\FlickrApiGetContactInfoException;
use App\Facades\Flickr;
use App\Jobs\Middleware\RateLimited;
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

class FlickrContact implements ShouldQueue
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
     * @return RateLimited[]
     */
    public function middleware(): array
    {
        return [new RateLimited('flickr')];
    }

    /**
     * @throws \App\Exceptions\Flickr\FlickrApiGetContactInfoException
     */
    public function handle(): void
    {
        $contactModel = app(ContactRepository::class)->findOrCreateByNsId($this->nsid);

        if ($contactModel->isDone()) {
            return;
        }

        if (!$userInfo = Flickr::getUserInfo($contactModel->nsid)) {
            throw new FlickrApiGetContactInfoException($contactModel->nsid);
        }

        $contactModel->touch();

        $hydrator = new ObjectPropertyHydrator();
        $userInfo = $hydrator->extract($userInfo->person);

        $contactModel->fill($userInfo)
            ->setAttribute(Contact::KEY_STATUS, true)
            ->save();
    }
}
