<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Jobs\Flickr;

use App\Facades\Flickr;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Repositories\Flickr\ContactRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Process get all contacts
 * @package App\Jobs\Flickr
 */
class Contact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private int $page;

    /**
     * Create a new job instance.
     *
     * @param int $page
     */
    public function __construct(int $page)
    {
        $this->page = $page;
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
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (!$contacts = Flickr::getCurrentContacts($this->page)) {
            return;
        }

        $repository = app(ContactRepository::class);

        foreach ($contacts->contacts->contact as $contact) {
            if ($item = $repository->getContactByNsid($contact->nsid)) {
                continue;
            }

            $userInfo = Flickr::getUserInfo($contact->nsid);

            if (!$userInfo) {
                continue;
            }

            $repository->save((array) $userInfo->person);

            FavesPhoto::dispatch($contact->nsid);
        }
    }
}
