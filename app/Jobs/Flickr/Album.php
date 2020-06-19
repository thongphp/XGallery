<?php

namespace App\Jobs\Flickr;

use App\Facades\Flickr;
use App\Facades\GooglePhotoFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\FlickrPhoto;
use App\Repositories\FlickrAlbumRepository;
use App\Repositories\FlickrPhotoRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Album implements ShouldQueue
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

    public function handle(): void
    {
        $repository = app(FlickrAlbumRepository::class);
        $albumModel = $repository->findByAlbumId($this->album->id);

        if (!$albumModel) {
            $albumData = (array) $this->album;
            $albumData['status'] = false;
            $albumData['download_photos'] = 0;
            $albumModel = $repository->save($albumData);
        }

        if ($albumModel->status) {
            return;
        }

        $photos = Flickr::getAlbumPhotos($albumModel->id);

        if (!$photos) {
            return;
        }

        $googleRef = $albumModel->getAttributeValue('googleRef');

        if (!$googleRef) {
            $googleAlbum = GooglePhotoFacade::createAlbum($albumModel->title);
            $albumModel->setAttribute('googleRef', $googleAlbum);
            $albumModel->save();
        }

        $albumId = $albumModel->getAttributeValue('id');
        $owner = $albumModel->getAttributeValue('owner');

        $this->startDownloadPhotos($photos->photoset->photo, $albumId, $owner);

        if ($photos->photoset->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photoset->pages; $page++) {
            $photos = Flickr::getAlbumPhotos($albumModel->id, $page);
            $this->startDownloadPhotos($photos->photoset->photo, $albumId, $owner);
        }
    }

    /**
     * @param array $photos
     * @param string $albumId
     * @param string $owner
     */
    private function startDownloadPhotos(array $photos, string $albumId, string $owner): void
    {
        $photoRepository = app(FlickrPhotoRepository::class);

        foreach ($photos as $photo) {
            $photoModel = $photoRepository->findById($photo->id);

            if (!$photoModel) {
                $photoData = (array) $photo;
                $photoData[FlickrPhoto::KEY_ALBUM_ID] = $albumId;
                $photoData[FlickrPhoto::KEY_OWNER_ID] = $owner;
                $photoData[FlickrPhoto::KEY_STATUS] = false;
                unset($photoData['owner']);
                $photo = $photoRepository->save($photoData);
            }

            PhotoDownload::dispatch($photo->id);
        }
    }
}
