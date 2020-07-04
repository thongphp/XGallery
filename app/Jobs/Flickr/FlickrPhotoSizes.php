<?php

namespace App\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\FlickrPhotoModel;
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

    private string $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    public function handle(): void
    {
        $photo = FlickrPhotoModel::where(['id' => $this->id])->first();

        if (!$photo) {
            return;
        }

        try {
            $photo->{FlickrPhotoModel::KEY_SIZES} = FlickrClient::getPhotoSizes($this->id)->sizes->size;
            $photo->save();
        } catch (\Exception $exception) {
            $photo->delete();
        }
    }
}
