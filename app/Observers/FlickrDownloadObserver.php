<?php

namespace App\Observers;

use App\Facades\GooglePhotoClient;
use App\Models\Flickr\FlickrDownload;
use App\Models\Flickr\FlickrDownloadXref;
use Illuminate\Support\Collection;

class FlickrDownloadObserver
{
    /**
     * Handle the flickr download "created" event.
     *
     * @SuppressWarnings("unused")
     *
     * @param FlickrDownload $flickrDownload
     *
     * @return void
     */
    public function created(FlickrDownload $flickrDownload): void
    {
    }

    /**
     * Handle the flickr download "updated" event.
     *
     * @param FlickrDownload $flickrDownload
     *
     * @return void
     */
    public function updated(FlickrDownload $flickrDownload): void
    {
        if ($flickrDownload->processed !== $flickrDownload->photos_count) {
            return;
        }

        /**
         * Show time
         * Get all linked download WITH google_photo_token
         * @var $downloadedPhotos Collection
         */
        $downloadedPhotos = FlickrDownloadXref::where(
            [
                'download_id' => $flickrDownload->_id,
            ]
        )->whereNotNull('google_photo_token')->get();

        if ($downloadedPhotos->isEmpty()) {
            return;
        }

        // Create Google Album

        $googleAlbumToken = GooglePhotoClient::createAlbum($flickrDownload->name);
        GooglePhotoClient::uploadAndCreateMedias($downloadedPhotos, $googleAlbumToken);

        // @TODO Implement Activity Stream
    }

    /**
     * Handle the flickr download "deleted" event.
     *
     * @SuppressWarnings("unused")
     *
     * @param FlickrDownload $flickrDownload
     *
     * @return void
     */
    public function deleted(FlickrDownload $flickrDownload): void
    {
    }

    /**
     * Handle the flickr download "restored" event.
     *
     * @SuppressWarnings("unused")
     *
     * @param FlickrDownload $flickrDownload
     *
     * @return void
     */
    public function restored(FlickrDownload $flickrDownload): void
    {
    }

    /**
     * Handle the flickr download "force deleted" event.
     *
     * @SuppressWarnings("unused")
     *
     * @param FlickrDownload $flickrDownload
     *
     * @return void
     */
    public function forceDeleted(FlickrDownload $flickrDownload): void
    {
    }
}
