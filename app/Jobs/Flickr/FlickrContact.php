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
        // @TODO Validate is valid nsid
        $this->nsid = $nsid;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @TODO RateLimit in this case is useless
     * @return RateLimited[]
     */
    public function middleware(): array
    {
        return [new RateLimited('flickr')];
    }

    /**
     * @throws FlickrApiGetContactInfoException
     */
    public function handle(): void
    {
        $contactModel = app(ContactRepository::class)->findOrCreateByNsId($this->nsid);

        if ($contactModel->isDone()) {
            return;
        }

        /**
         * @TODO If user is disabled / deleted than keep job succeed but do not store
         */
        if (!$userInfo = Flickr::getUserInfo($contactModel->nsid)) {
            throw new FlickrApiGetContactInfoException($contactModel->nsid);
        }

        $contactModel->touch();
        /**
         * @TODO What's status purpose
         * @link https://softwareengineering.stackexchange.com/questions/219351/state-or-status-when-should-a-variable-name-contain-the-word-state-and-w
         */
        $contactModel->fill((new ObjectPropertyHydrator())->extract($userInfo->person))
            ->setAttribute(Contact::KEY_STATUS, true)
            ->save();
    }
}
