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
 * Process get all fave photos => get owner of this photos => Store list of contacts
 * @package App\Jobs\Flickr
 */
class FavesPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $owner;

    /**
     * @param string $owner
     */
    public function __construct(string $owner)
    {
        $this->owner = $owner;
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
        if (!$result = Flickr::getFavouritePhotosOfUser($this->owner)) {
            return;
        }

        $repository = app(ContactRepository::class);

        $this->saveContacts($result->photos->photo, $repository);

        if ($result->photos->page === 1) {
            return;
        }

        for ($page = 2; $page <= $result->photos->pages; $page++) {
            $result = Flickr::getFavouritePhotosOfUser($this->owner, $page);
            $this->saveContacts($result->photos->photo, $repository);
        }
    }

    /**
     * @param array $photos
     * @param ContactRepository $repository
     */
    private function saveContacts(array $photos, ContactRepository $repository): void
    {
        foreach ($photos as $photo) {
            if ($repository->getContactByNsid($photo->owner)) {
                continue;
            }

            $userInfo = Flickr::getUserInfo($photo->owner);

            if (!$userInfo) {
                continue;
            }

            $repository->save((array) $userInfo->person);
        }
    }
}
