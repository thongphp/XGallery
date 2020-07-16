<?php

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Exceptions\CurlDownloadFileException;
use App\Facades\FlickrClient;
use App\Jobs\Google\SyncPhotoToGooglePhoto;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\FlickrPhotoModel;
use App\Repositories\Flickr\PhotoRepository;
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

    private string $id;
    private string $googleAlbumId;

    /**
     * @param  string  $id
     * @param  string  $googleAlbumId
     */
    public function __construct(string $id, string $googleAlbumId)
    {
        $this->id = $id;
        $this->googleAlbumId = $googleAlbumId;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $photo = app(PhotoRepository::class)->findOrCreateById($this->id);

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

        SyncPhotoToGooglePhoto::dispatch(
            $this->downloadPhoto($photo),
            $photo->title,
            $this->googleAlbumId
        );
    }

    /**
     * @param  FlickrPhotoModel  $photo
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

        $filePath = $httpClient->download($source, 'flickr/'.$photo->owner);

        if (!$filePath) {
            throw new CurlDownloadFileException('Can not download photo '.$this->id.' '.$source);
        }

        return $filePath;
    }
}
