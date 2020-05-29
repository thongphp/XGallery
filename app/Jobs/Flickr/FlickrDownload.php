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
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Oauth\Services\Flickr\Flickr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
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
     * @param  object  $photo
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
    public function middleware()
    {
        return [new RateLimited('flickr')];
    }

    /**
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $filePath = $this->download();

        // Flickr dir 1B_ii6zmyqMjnb37NBnU9DJjcRfb2iECm/18Wi-wHgjgp8JgTijv7rTgH0tAuqvPZM6
        $dirName = '1B_ii6zmyqMjnb37NBnU9DJjcRfb2iECm/18Wi-wHgjgp8JgTijv7rTgH0tAuqvPZM6';

        // Create owner dir if needed
        if (!$ownerDir = collect(Storage::cloud()->listContents($dirName))
            ->where('type', '=', 'dir')
            ->where('name', '=', $this->owner)
            ->first()) {
            Storage::cloud()->createDir($dirName.'/'.$this->owner);
        }

        // Get ID
        $ownerDir = collect(Storage::cloud()->listContents($dirName))
            ->where('type', '=', 'dir')
            ->where('name', '=', $this->owner)
            ->first();

        $fileName = basename($filePath);

        // Check if filename exists
        if (collect(Storage::cloud()->listContents($ownerDir['path']))
            ->where('type', '=', 'file')
            ->where('name', '=', $fileName)
            ->first()) {
            Storage::delete($filePath);
            return;
        }

        Storage::cloud()->put($ownerDir['path'].'/'.$fileName, File::get(storage_path('app/'.$filePath)));
        Storage::delete($filePath);

        return;
    }

    private function download()
    {
        $client = app(Flickr::class);
        $httpClient = app(HttpClient::class);

        if (!$sizes = $client->get('photos.getSizes', ['photo_id' => $this->photo->id])) {
            return;
        }

        $size = end($sizes->sizes->size);
        return $httpClient->download($size->source, 'flickr'.DIRECTORY_SEPARATOR.$this->owner);
    }
}
