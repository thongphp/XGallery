<?php

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Facades\Flickr;
use App\Facades\GooglePhotoFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Repositories\Flickr\PhotoRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FlickrSyncToGooglePhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $id;
    private string $googleAlbumId;

    /**
     * FlickrSyncToGooglePhoto constructor.
     *
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
     * @return RateLimited[]
     */
    public function middleware(): array
    {
        return [new RateLimited('flickr')];
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $photo = app(PhotoRepository::class)->findOrCreateById($this->id);

        if (!$photo->hasSizes()) {
            if (!$sizes = Flickr::getPhotoSizes($photo->id)) {
                throw new Exception('Can not get photoSizes: '.$this->id);
            }

            $photo->sizes = $sizes;
            $photo->save();
        }

        if (!$filePath = $this->downloadPhoto($photo->sizes, $photo->owner)) {
            throw new Exception('Can not download photo: '.$this->id);
        }

        if (!GooglePhotoFacade::uploadAndCreateMedia($filePath, $photo->id, $this->googleAlbumId)) {
            throw new Exception('Can not sync photo to Google Photo: '.$this->id);
        }

        Storage::delete($filePath);
    }

    /**
     * @param array $photoSizes
     * @param string $owner
     *
     * @return bool|string
     */
    private function downloadPhoto(array $photoSizes, string $owner)
    {
        $sourceSize = end($photoSizes);
        $httpClient = app(HttpClient::class);

        return $httpClient->download($sourceSize['source'], 'flickr/'.$owner);
    }
}
