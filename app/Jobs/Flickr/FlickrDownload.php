<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Facades\Flickr;
use App\Facades\GoogleDriveFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Class FlickrDownload
 * @package App\Jobs\Flickr
 */
class FlickrDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string    $owner;
    private object    $photo;

    /**
     * Create a new job instance.
     *
     * @param object $photo
     */
    public function __construct(string $owner, object $photo)
    {
        $this->owner = $owner;
        $this->photo = $photo;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @return RateLimited[]
     */
    public function middleware(): array
    {
        return [new RateLimited('flickr')];
    }

    public function handle(): void
    {
        $filePath = $this->download();

        // Flickr dir 1B_ii6zmyqMjnb37NBnU9DJjcRfb2iECm/18Wi-wHgjgp8JgTijv7rTgH0tAuqvPZM6
        $dirName = '1B_ii6zmyqMjnb37NBnU9DJjcRfb2iECm/18Wi-wHgjgp8JgTijv7rTgH0tAuqvPZM6';

        // Create owner dir if needed
        if (!$ownerDir = GoogleDriveFacade::dirExists($dirName, $this->owner)) {
            Storage::cloud()->createDir($dirName.'/'.$this->owner);
        }

        // Get ID
        $ownerDir = GoogleDriveFacade::dirExists($dirName, $this->owner);

        GoogleDriveFacade::put($ownerDir['path'], storage_path('app/'.$filePath));
        Storage::delete($filePath);
    }

    /**
     * @return bool
     */
    private function download(): bool
    {
        /** @var Flickr $client */
        $client = app(Flickr::class);
        /** @var HttpClient $httpClient */
        $httpClient = app(HttpClient::class);

        if (!$sizes = $client->get('photos.getSizes', ['photo_id' => $this->photo->id])) {
            return false;
        }

        $size = end($sizes->sizes->size);
        return $httpClient->download($size->source, 'flickr'.DIRECTORY_SEPARATOR.$this->owner);
    }
}
