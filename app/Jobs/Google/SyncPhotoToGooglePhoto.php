<?php

namespace App\Jobs\Google;

use App\Facades\GooglePhotoClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\FlickrDownloadModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Spatie\RateLimitedMiddleware\RateLimited;

/**
 * @package App\Jobs\Google
 */
class SyncPhotoToGooglePhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private FlickrDownloadModel $flickrDownload;

    /**
     * @param FlickrDownloadModel $flickrDownload
     */
    public function __construct(FlickrDownloadModel $flickrDownload)
    {
        $this->flickrDownload = $flickrDownload;
        $this->onQueue(Queues::QUEUE_GOOGLE);
    }

    public function middleware(): array
    {
        return [
            (new RateLimited())
                ->allow(5)
                ->everySeconds(1)
                ->releaseAfterSeconds(30),
        ];
    }

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        if (!Storage::exists($this->flickrDownload->local_path)) {
            throw new FileNotFoundException('File not found '.$this->flickrDownload);
        }

        GooglePhotoClient::uploadMedia($this->flickrDownload);
    }
}
