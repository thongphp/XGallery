<?php

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Exceptions\CurlDownloadFileException;
use App\Facades\FlickrClient;
use App\Jobs\Google\SyncPhotoToGooglePhoto;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\FlickrDownloadModel;
use App\Models\Flickr\FlickrPhotoModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Download Flickr photo and trigger sync with Google
 * @package App\Jobs\Flickr
 */
class FlickrDownloadPhotoToLocal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    /**
     * @var FlickrDownloadModel
     */
    private FlickrDownloadModel $download;

    /**
     * @param FlickrDownloadModel $download
     */
    public function __construct(FlickrDownloadModel $download)
    {
        $this->download = $download;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $photo = FlickrPhotoModel::firstOrCreate(['id' => $this->download->photo_id]);

        if (!$photo->hasSizes()) {
            $sizes = FlickrClient::getPhotoSizes($photo->id);
            $photo->sizes = $sizes->sizes->size;
            $photo->save();
        }

        $photo->touch();

        // If we can't get size then do not trigger download
        if (!$photo->hasSizes()) {
            return;
        }

        $this->download->local_path = $this->downloadPhoto($photo);
        $this->download->save();

        SyncPhotoToGooglePhoto::dispatch($this->download, $photo->title);
    }

    /**
     * @param FlickrPhotoModel $photo
     *
     * @return bool|string
     * @throws \Exception
     */
    private function downloadPhoto(FlickrPhotoModel $photo)
    {
        // Due to dynamic variable of MongoDB model, we can not do like this $sourceSize = end($photo-sizes);
        $photoSizes = $photo->sizes;
        $sourceSize = end($photoSizes);
        $httpClient = app(HttpClient::class);

        $source = is_array($sourceSize) ? $sourceSize['source'] : $sourceSize->source;

        if ($filePath = $httpClient->download($source, 'flickr/'.$photo->owner)) {
            return $filePath;
        }

        throw new CurlDownloadFileException('Can not download photo '.$this->download->photo_id.' '.$source);
    }
}
