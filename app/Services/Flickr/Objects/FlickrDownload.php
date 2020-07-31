<?php

namespace App\Services\Flickr\Objects;

use App\Facades\GooglePhotoClient;
use App\Facades\UserActivity;
use App\Jobs\Flickr\FlickrContact;
use App\Models\Flickr\FlickrDownloadXref;
use App\Models\Flickr\FlickrPhotoModel;
use App\Repositories\Flickr\ContactRepository;
use App\Services\Client\HttpClient;
use App\Services\Flickr\Url\FlickrUrlInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class FlickrDownload implements FlickrObjectInterface
{
    protected Collection $photos;

    /**
     * @var FlickrUrlInterface
     */
    private FlickrUrlInterface $flickrUrl;

    /**
     * FlickrAlbum constructor.
     * @param  FlickrUrlInterface  $flickrUrl
     */
    public function __construct(FlickrUrlInterface $flickrUrl)
    {
        $this->flickrUrl = $flickrUrl;
        $this->photos = collect([]);

        $this->load();
    }

    public function getType(): string
    {
        return $this->flickrUrl->getType();
    }

    public function download(): bool
    {
        // If owner is not exist, start new queue for getting this contact information.
        if (!app(ContactRepository::class)->isExist($this->getOwner())) {
            FlickrContact::dispatch($this->getOwner());
        }

        // Create download request
        $download = \App\Models\Flickr\FlickrDownload::firstOrCreate([
            'user_id' => Auth::user()->getAuthIdentifier(),
            'type' => $this->getType(),
            'photos_count' => $this->getPhotosCount(),
            'processed' => 0 // Init default value
        ]);

        /**
         * Extract photos to xref
         * Actually getPhotos would make multi request depends on number of page but assumed not too much
         */
        $photos = $this->getPhotos();
        $client = app(HttpClient::class);
        $photos->each(function ($photo) use ($download, $client) {
            /**
             * Send to queue FlickrDownload__construct($download, $photo)
             * Note:// It would be many queues if we have too many photos
             */

            /**
             * Try to save photo
             * Actually this step also used to "get" photo from database and use sizes if possible
             */
            $photo = FlickrPhotoModel::firstOrCreate([
                'id' => $photo->id,
                'secret' => $photo->secret,
                'server' => $photo->server,
                'farm' => $photo->farm,
                'title' => $photo->title,
            ]);

            /**
             * Use _id instead id because we are not use photo_id in Flickr is unique or not
             * Each _id must be linked with a download request
             * Purpose of this table for storing google_photo_token
             */
            $downloadXref = FlickrDownloadXref::firstOrCreate([
                'photo_id' => $photo->_id, // ObjectId
                'download_id' => $download->_id // ObjectId
            ]);

            /**
             * We'll use observer to catch download update event
             * - if processed === photos_count . It's mean all photos already process ( no matter succeed or failed ) then trigger batchMedia
             * Note:// Trigger event FlickrDownloadCompleteEvent and use listener to process.
             * Use batchMedia via queue where download_xrefs.google_photo_token IS NOT NULL && download_id=$download.id. Sent notification like "Synced x/y photos of Album XXX to Google Album XXX
             * Clean up these records
             * If batchMedia failed we'll send notification too
             * - if processed !== photos count . Do nothing
             */

            /**
             * Get size
             * Note:// This method used to make easier to get sizes directly via model. It's not related another thing
             */
            $size = $photo->getBestSize();

            if (!$size) {
                $download->processed++;
                $downloadXref->state = -1; // Download failed
            } elseif ($filePath = $client->download(
                $size['source'],
                'flickr/'.$this->getOwner().'/'.$this->getType().'/'.$this->getTitle()
            )) {

                /**
                 * Execute upload to Google
                 * Note:// We need catch exception here to update processed state
                 * Note:// Download always return related path. Please make sure it'll not break any exists download process
                 */
                try {
                    $downloadXref->google_photo_token = GooglePhotoClient::uploadMedia($filePath);
                } catch (\Exception $exception) {
                    $download->processed++; // Even it's failed but we already processed
                    /**
                     * @TODO Actually we can use job failed ( completely failed not retry ) event instead catch exception here
                     * Exception in this case also can use to make job fail and retry ( before completely fail )
                     */
                    $downloadXref->state = -2; // Upload failed
                    $downloadXref->save();
                    $download->save(); // Always save download after download xref
                    /**
                     * @TODO We save download after downloadXref to make sure everything completely processed
                     */
                    return;
                }

                $downloadXref->state = 1; // Succeed download & upload
            }

            $downloadXref->save();
            $download->save();
        });

        UserActivity::notify(
            '%s request %s '.$this->getType(),
            Auth::user(),
            'download',
            [
                'object_id' => $this->getId(),
                'extra' => [
                    'title' => $this->getTitle(),
                    'title_link' => $this->getUrl(),
                    'fields' => [
                        'ID' => $this->getId(),
                        'Photos' => $this->getPhotosCount(),
                        'Owner' => $this->getOwner(),
                        'Sync to Google' => $this->getTitle()
                    ],
                ],
            ]
        );

        return true;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->flickrUrl->getOwner();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->flickrUrl->getId();
    }

    public function getUrl(): string
    {
        return $this->flickrUrl->getUrl();
    }

    abstract protected function load(): bool;
}
