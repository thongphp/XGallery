<?php

namespace App\Jobs\Flickr;

use App\Facades\Flickr;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Repositories\Flickr\PhotoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class FlickrPhoto implements ShouldQueue
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
        $photoModel = app(PhotoRepository::class)->findOrCreateById($this->id);

        if ($photoModel->isDone() || $photoModel->hasSizes()) {
            return;
        }

        if (!$sizes = Flickr::getPhotoSizes($photoModel->id)) {
            throw new RuntimeException('Can not get sizes of photo: '.$photoModel->id);
        }

        $photoModel->touch();
        $photoModel->sizes = $sizes->sizes->size;
        $photoModel->save();
    }
}
