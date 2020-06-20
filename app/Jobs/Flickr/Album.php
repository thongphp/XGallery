<?php

namespace App\Jobs\Flickr;

use App\Facades\Flickr;
use App\Facades\GooglePhotoFacade;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\AlbumRepository;
use App\Repositories\Flickr\PhotoRepository;
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
        $repository = app(AlbumRepository::class);

        if (!$albumModel = $repository->findByAlbumId($this->album->id)) {
            $albumData = (array) $this->album;
            $albumData['status'] = false;
            $albumData['download_photos'] = 0;
            $albumModel = $repository->save($albumData);
        }

        if ($albumModel->status) {
            return;
        }

        if (!$photos = Flickr::getAlbumPhotos($albumModel->id)) {
            return;
        }

        if (!$albumModel->googleRef) {
            $googleAlbum = GooglePhotoFacade::createAlbum($albumModel->title);
            $albumModel->setAttribute('googleRef', $googleAlbum);
            $albumModel->save();
        }

        $this->startDownloadPhotos($photos->photoset->photo, $albumModel->id, $albumModel->owner);

        if ($photos->photoset->page === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photoset->pages; $page++) {
            if (!$photos = Flickr::getAlbumPhotos($albumModel->id, $page)) {
                continue;
            }

            $this->startDownloadPhotos($photos->photoset->photo, $albumModel->id, $albumModel->owner);
        }
    }

    /**
     * @param array $photos
     * @param string $albumId
     * @param string $owner
     */
    private function startDownloadPhotos(array $photos, string $albumId, string $owner): void
    {
        $photoRepository = app(PhotoRepository::class);

        foreach ($photos as $photo) {
            if (!$photoRepository->findById($photo->id)) {
                $photoData = (array) $photo;
                $photoData[Photo::KEY_ALBUM_ID] = $albumId;
                $photoData[Photo::KEY_OWNER] = $owner;
                $photoData[Photo::KEY_STATUS] = false;
                unset($photoData['owner']);
                $photo = $photoRepository->save($photoData);
            }

            PhotoDownload::dispatch($photo->id);
        }
    }
}
