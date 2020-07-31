<?php

namespace App\Observers;

use App\Facades\GooglePhotoClient;
use App\Models\Flickr\FlickrDownload;
use App\Models\Flickr\FlickrDownloadXref;

class FlickrDownloadObserver
{
    /**
     * Handle the flickr download "created" event.
     *
     * @param  FlickrDownload  $flickrDownload
     * @return void
     */
    public function created(FlickrDownload $flickrDownload)
    {
    }

    /**
     * Handle the flickr download "updated" event.
     *
     * @param  FlickrDownload  $flickrDownload
     * @return void
     * @throws \JsonException
     */
    public function updated(FlickrDownload $flickrDownload)
    {
        if ($flickrDownload->processed !== $flickrDownload->photos_count) {
            return;
        }

        /**
         * Show time
         * Get all linked download WITH google_photo_token
         * @var $downloadedPhotos \Illuminate\Support\Collection
         */

        $downloadedPhotos = FlickrDownloadXref::where([
            'download_id' => $flickrDownload->_id
        ])->whereNotNull('google_photo_token')->get();

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
     * @param  FlickrDownload  $flickrDownload
     * @return void
     */
    public function deleted(FlickrDownload $flickrDownload)
    {
        //
    }

    /**
     * Handle the flickr download "restored" event.
     *
     * @param  FlickrDownload  $flickrDownload
     * @return void
     */
    public function restored(FlickrDownload $flickrDownload)
    {
        //
    }

    /**
     * Handle the flickr download "force deleted" event.
     *
     * @param  FlickrDownload  $flickrDownload
     * @return void
     */
    public function forceDeleted(FlickrDownload $flickrDownload)
    {
        //
    }
}
