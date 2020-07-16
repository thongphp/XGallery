<?php

namespace App\Jobs\Flickr;

use App\Facades\GooglePhotoClient;
use App\Facades\UserActivity;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Jobs\Traits\SyncPhotos;
use App\Models\User;
use App\Repositories\Flickr\ContactRepository;
use App\Services\Flickr\Objects\FlickrAlbum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class FlickrDownloadAlbum
 * @package App\Jobs\Flickr
 */
class FlickrDownloadAlbum implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob, SyncPhotos;

    private FlickrAlbum $album;
    private User $user;

    /**
     * @param  FlickrAlbum  $album
     * @param  User  $user
     */
    public function __construct(FlickrAlbum $album, User $user)
    {
        $this->album = $album;
        $this->user = $user;
        $this->onQueue(Queues::QUEUE_FLICKR);
    }

    /**
     * @throws \JsonException
     */
    public function handle(): void
    {
        $owner = $this->album->getOwner();

        $googleAlbum = GooglePhotoClient::createAlbum($this->album->getTitle());
        $googleAlbumId = $googleAlbum->id;

        // If owner is not exist, start new queue for getting this contact information.
        if (!app(ContactRepository::class)->isExist($owner)) {
            FlickrContact::dispatch($owner);
        }

        // @todo Notification in even not job
        UserActivity::notify(
            '%s request %s album',
            $this->user,
            'download',
            [
                'object_id' => $this->album->getId(),
                'extra' => [
                    'title' => $this->album->getTitle(),
                    'title_link' => 'https://www.flickr.com/photos/'.$this->album->getOwner().'/albums/'.$this->album->getId(),
                    // Fields are displayed in a table on the message
                    'fields' => [
                        'ID' => $this->album->getId(),
                        'Photos' => $this->album->getPhotosCount(),
                        'Owner' => $this->album->getOwner(),
                        'Sync to Google' => $this->album->getTitle().' ['.$googleAlbumId.']'
                    ],
                    'footer' => $this->album->getDescription(),
                    'action' => [
                        'Check on Google',
                        $googleAlbum->productUrl
                    ]
                ],
            ]
        );

        $download = $this->album->getDownload();
        $download->google_album_id = $googleAlbumId;
        $download->save();

        $this->syncPhotos($this->album->getPhotos()->toArray(), $owner, $googleAlbumId);
    }
}
