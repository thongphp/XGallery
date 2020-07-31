<?php

namespace App\Jobs\Flickr;

use App\Facades\GooglePhotoClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\FlickrDownload;
use App\Models\Flickr\FlickrDownloadXref;
use App\Models\Flickr\FlickrPhotoModel;
use App\Services\Client\HttpClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Download Flickr photo and trigger sync with Google
 * @package App\Jobs\Flickr
 */
class FlickrDownloadPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    /**
     * @var FlickrDownload
     */
    private FlickrDownload $download;
    private object $photo;
    private \App\Services\Flickr\Objects\FlickrDownload $downloadRequest;

    /**
     * FlickrDownloadPhotoToLocal constructor.
     * @param  \App\Services\Flickr\Objects\FlickrDownload  $downloadRequest
     * @param  FlickrDownload  $download
     * @param $photo
     */
    public function __construct(
        \App\Services\Flickr\Objects\FlickrDownload $downloadRequest,
        FlickrDownload $download,
        $photo
    ) {
        $this->downloadRequest = $downloadRequest;
        $this->download = $download;
        $this->photo = $photo;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(): void
    {
        /**
         * Try to save photo
         * Actually this step also used to "get" photo from database and use sizes if possible
         * @var FlickrPhotoModel $photo
         */
        $photo = FlickrPhotoModel::firstOrCreate([
            'id' => $this->photo->id,
            'secret' => $this->photo->secret,
            'server' => $this->photo->server,
            'farm' => $this->photo->farm,
        ], [
            'title' => $this->photo->title,
            'owner' => $this->downloadRequest->getOwner()
        ]);

        $client = app(HttpClient::class);

        /**
         * Use _id instead id because we are not use photo_id in Flickr is unique or not
         * Each _id must be linked with a download request
         * Purpose of this table for storing google_photo_token
         */
        $downloadXref = FlickrDownloadXref::firstOrCreate([
            'photo_id' => $photo->_id, // ObjectId
            'download_id' => $this->download->_id // ObjectId
        ]);

        if (!$size = $photo->getBestSize()) {
            $downloadXref->state = -1; // No size
            $downloadXref->save();

            /**
             * Can not get size but we already processed
             */
            $this->download->incProcessed();

            return;
        }

        $filePath = $client->download(
            $size['source'],
            'flickr/'.$this->downloadRequest->getOwner().'/'.$this->downloadRequest->getType().'/'.$this->downloadRequest->getTitle()
        );

        if (!$filePath) {
            $downloadXref->state = -2; // Download failed
            $downloadXref->save();

            /**
             * Can not get size but we already processed
             */
            $this->download->incProcessed();
        }

        /**
         * @TODO Move to event
         * Downloaded now upload to Google
         */
        $downloadXref->google_photo_token = GooglePhotoClient::uploadMedia($filePath);
        $downloadXref->file = $filePath;
        $downloadXref->title = $photo->title;
        $downloadXref->save();
        $this->download->incProcessed();
    }
}
