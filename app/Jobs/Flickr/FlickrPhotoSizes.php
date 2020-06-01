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
 * Class FlickrPhotoSizes
 * @package App\Jobs\Flickr
 */
class FlickrPhotoSizes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private \App\Models\FlickrPhotos $photo;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\FlickrPhotos  $photo
     */
    public function __construct(\App\Models\FlickrPhotos $photo)
    {
        $this->photo = $photo;
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
        if (!$sizes = Flickr::get('photos.getSizes', ['photo_id' => $this->photo->id])) {
            return;
        }

        $this->photo->sizes = $sizes->sizes;
        $this->photo->save();
    }
}
