<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\GooglePhotoClient;
use App\Jobs\Flickr\FlickrContact;
use App\Jobs\Flickr\FlickrDownloadGallery;
use App\Jobs\Flickr\FlickrDownloadPhotoToLocal;
use App\Models\Flickr\FlickrPhotoModel;
use App\Services\Flickr\Objects\FlickrGallery;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrDownloadGalleryTest extends TestCase
{
    private const GALLERY_ID = '72157714109067522';

    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockFlickrClientCommand('getPeopleInfo');
    }

    public function testHandle(): void
    {
        $this->mockFlickrClientCommand('getGalleryInformation');
        $this->mockFlickrClientCommand('getGalleryPhotos');
        GooglePhotoClient::shouldReceive('createAlbum')->andReturn((object)['id' => 'google-album-' . self::GALLERY_ID]);

        $flickrGallery = new FlickrGallery(self::GALLERY_ID);
        $this->assertTrue($flickrGallery->load());

        $owner = $flickrGallery->getOwner();

        $this->expectsJobs(FlickrContact::class);
        $this->expectsJobs(FlickrDownloadPhotoToLocal::class);

        (new FlickrDownloadGallery($flickrGallery))->handle();

        $photos = FlickrPhotoModel::where([FlickrPhotoModel::KEY_OWNER => $owner])->get();
        $this->assertEquals($flickrGallery->getPhotosCount(), $photos->count());
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
