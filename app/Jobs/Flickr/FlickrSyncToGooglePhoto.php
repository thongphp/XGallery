<?php

namespace App\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Exceptions\CurlDownloadFileException;
use App\Exceptions\Flickr\FlickrApiGetPhotoSizesException;
use App\Facades\Flickr;
use App\Facades\GooglePhotoFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\PhotoRepository;
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
     * @throws \App\Exceptions\CurlDownloadFileException
     * @throws \App\Exceptions\Flickr\FlickrApiGetPhotoSizesException
     * @throws \App\Exceptions\Google\GooglePhotoApiMediaCreateException
     * @throws \App\Exceptions\Google\GooglePhotoApiUploadException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \JsonException
     */
    public function handle(): void
    {
        /** @var Photo $photo */
        $photo = app(PhotoRepository::class)->findOrCreateById($this->id);
        $photo->touch();

        if (!$photo->hasSizes()) {
            if (!$sizes = Flickr::getPhotoSizes($photo->id)) {
                throw new FlickrApiGetPhotoSizesException($this->id);
            }

            $photo->fill(['sizes' => $sizes->sizes->size])
                ->save();
        }

        if (!$filePath = $this->downloadPhoto($photo)) {
            throw new CurlDownloadFileException('Can not download photo: '.$this->id);
        }

        GooglePhotoFacade::uploadAndCreateMedia($filePath, $photo->id, $this->googleAlbumId);
        Storage::delete($filePath);
    }

    /**
     * @param \App\Models\Flickr\Photo $photo
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
