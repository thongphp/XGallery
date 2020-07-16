<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\GooglePhotoClient;
use App\Jobs\Flickr\FlickrContact;
use App\Jobs\Flickr\FlickrDownloadAlbum;
use App\Jobs\Flickr\FlickrDownloadPhotoToLocal;
use App\Models\Flickr\FlickrPhotoModel;
use App\Services\Flickr\Objects\FlickrAlbum;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrDownloadAlbumTest extends TestCase
{
    private const ALBUM_ID = '72157625634880796';

    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function testHandleWithOwnerNotExist(): void
    {
        $this->mockFlickrClientCommand('getPhotoSetInfo');
        $this->mockFlickrClientCommand('getPhotoSetPhotos');
        GooglePhotoClient::shouldReceive('createAlbum')->andReturn(
            (object)[
                'id' => 'google-album-' . self::ALBUM_ID,
                'productUrl' => 'https://google.com'
            ]
        );

        $flickAlbum = new FlickrAlbum('123456');
        $this->assertTrue($flickAlbum->load());

        $owner = $flickAlbum->getOwner();

        $this->expectsJobs(FlickrContact::class);
        $this->expectsJobs(FlickrDownloadPhotoToLocal::class);

        $job = new FlickrDownloadAlbum($flickAlbum);
        $job->handle();

        $photos = FlickrPhotoModel::where([FlickrPhotoModel::KEY_OWNER => $owner])->get();
        $this->assertEquals($flickAlbum->getPhotosCount(), $photos->count());
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
