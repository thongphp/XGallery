<?php

namespace App\Jobs\Flickr;

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
use Laminas\Hydrator\ObjectPropertyHydrator;

class FlickrDownloadAlbum implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private object $album;

    /**
     * @param object $album
     */
    public function __construct(object $album)
    {
        $this->album = $album;
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
        if (!$photos = Flickr::getAlbumPhotos($this->album->id)) {
            return;
        }

        $googleAlbum = GooglePhotoFacade::createAlbum($this->album->title);

        if (!$googleAlbum) {
            throw new Exception('Can not create Google FlickrAlbumDownloadQueue: '.$this->album->id);
        }

        $this->processPhotos($photos->photoset->photo, $googleAlbum->id);

        if ($photos->photoset->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photoset->pages; $page++) {
            if (!$photos = Flickr::getAlbumPhotos($this->album->id, $page)) {
                continue;
            }

            $this->processPhotos($photos->photoset->photo, $googleAlbum->id);
        }
    }

    /**
     * @param array $photos
     * @param string $googleAlbumId
     */
    private function processPhotos(array $photos, string $googleAlbumId): void
    {
        $photoRepository = app(PhotoRepository::class);
        $hydrator = new ObjectPropertyHydrator();

        foreach ($photos as $photo) {
            /** @var \App\Models\Flickr\PhotoInterface $photo */
            $photoModel = $photoRepository->findOrCreateById($photo->id);

            if ($photoModel->isDone()) {
                continue;
            }

            $photoModel->fill($hydrator->extract($photo))
                ->save();

            FlickrSyncToGooglePhoto::dispatch($photoModel->id, $googleAlbumId);
        }
    }
}
