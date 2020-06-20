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
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\PhotoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Fetch photos in a contact page
 * @package App\Jobs\Flickr
 */
class ContactPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private int    $page;
    private object $contact;

    /**
     * Create a new job instance.
     *
     * @param $contact
     * @param int $page
     */
    public function __construct(object $contact, int $page)
    {
        $this->contact = $contact;
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
        if (!$photos = Flickr::getUserPhotos($this->contact->nsid, $this->page)) {
            return;
        }

        $photoRepository = app(PhotoRepository::class);

        foreach ($photos->photos->photo as $photo) {
            $photoModel = $photoRepository->findById($photo->id);

            if (!$photoModel) {
                $photoData = (array) $photo;
                $photoData[Photo::KEY_OWNER_ID] = $photo->owner;
                $photoData[Photo::KEY_STATUS] = false;
                unset($photoData['owner']);
                $photoModel = $photoRepository->save($photoData);
            }

            if ((bool) $photoModel->{Photo::KEY_STATUS} === true) {
                continue;
            }

//            PhotoDownload::dispatch($photo->id);
        }
    }
}
