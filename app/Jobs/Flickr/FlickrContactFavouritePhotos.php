<?php

namespace App\Jobs\Flickr;

use App\Facades\FlickrValidate;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\FlickrPhoto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class FlickrContactFavouritePhotos
 * @package App\Jobs\Flickr
 */
class FlickrContactFavouritePhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private \App\Models\Flickr\FlickrContact $flickrContact;

    /**
     * @param  \App\Models\Flickr\FlickrContact  $flickrContact
     */
    public function __construct(\App\Models\Flickr\FlickrContact $flickrContact)
    {
        $this->flickrContact = $flickrContact;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    public function handle(): void
    {
        if (!FlickrValidate::validateNsId($this->flickrContact->nsid)) {
            return;
        }

        $photos = $this->flickrContact->fetchFavoritePhotos();

        $owner = [];
        $photos->each(function ($photo) use ($owner) {
            FlickrPhoto::firstOrCreate(get_object_vars($photo));
            if (in_array($photo->owner, $owner)) {
                return;
            }

            $owner[] = $photo->owner;
            FlickrContact::dispatch($photo->owner);
        });
    }
}
