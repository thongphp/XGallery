<?php

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Exceptions\CurlDownloadFileException;
use App\Facades\FlickrClient;
use App\Jobs\Google\SyncPhotoToGooglePhoto;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\PhotoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FlickrDownloadPhotoToLocal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $id;
    private string $googleAlbumId;

    /**
     * @param string $id
     * @param string $googleAlbumId
     */
    public function __construct(string $id, string $googleAlbumId)
    {
        $this->id = $id;
        $this->googleAlbumId = $googleAlbumId;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @throws CurlDownloadFileException
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

        if (!$filePath = $this->downloadPhoto($photo)) {
            throw new CurlDownloadFileException('Can not download photo: '.$this->id);
        }

        SyncPhotoToGooglePhoto::dispatch($filePath, $photo->title, $this->googleAlbumId);
    }

    /**
     * @param Photo $photo
     *
     * @return bool|string
     */
    private function downloadPhoto(Photo $photo)
    {
        // Due to dynamic variable of MongoDB model, we can not do like this $sourceSize = end($photo-sizes);
        $photoSizes = $photo->sizes;
        $sourceSize = end($photoSizes);
        $httpClient = app(HttpClient::class);

        return $httpClient->download($sourceSize['source'], 'flickr/'.$photo->owner);
    }
}
