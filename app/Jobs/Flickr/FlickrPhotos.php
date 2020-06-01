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
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Fetch photos in a contact page
 * @package App\Jobs\Flickr
 */
class FlickrPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private int    $page;
    private object $contact;

    /**
     * Create a new job instance.
     *
     * @param $contact
     * @param  int  $page
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
    public function middleware()
    {
        return [new RateLimited('flickr')];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$photos = Flickr::get(
            'people.getPhotos',
            ['user_id' => $this->contact->nsid, 'page' => $this->page]
        )) {
            return;
        }

        foreach ($photos->photos->photo as $photo) {
            $model = app(\App\Models\FlickrPhotos::class);

            // Photo already exists
            if ($item = $model->where(['id' => $photo->id, 'owner' => $photo->owner])->first()) {
                continue;
            }

            $properties = get_object_vars($photo);

            foreach ($properties as $key => $value) {
                $model->{$key} = $value;
            }

            $model->save();
        }
    }
}
