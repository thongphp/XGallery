<?php

namespace App\Jobs\Flickr;

use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\FlickrPhoto;
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

    /**
     * @var FlickrPhoto
     */
    private FlickrPhoto $flickrPhotoModel;

    /**
     * FlickrPhotoSizes constructor.
     * @param  FlickrPhoto  $flickrPhotoModel
     */
    public function __construct(FlickrPhoto $flickrPhotoModel)
    {
        $this->flickrPhotoModel = $flickrPhotoModel;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    public function handle(): void
    {
        try {
            $this->flickrPhotoModel->getSizes();
        } catch (\Exception $exception) {
            // @TODO Depend on right exception we'll delete but not all exceptions
            // $photo->delete();
        }
    }
}
