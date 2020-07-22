<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Crawlers\HttpClient;
use App\Exceptions\CurlDownloadFileException;
use App\Exceptions\Flickr\FlickrApiPhotoGetSizesException;
use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrDownloadPhotoToLocal;
use App\Jobs\Google\SyncPhotoToGooglePhoto;
use App\Models\Flickr\FlickrDownloadModel;
use App\Models\Flickr\FlickrPhotoModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrDownloadPhotoToLocalTest extends TestCase
{
    private const PHOTO_ID = '32740490058';

    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function testHandleWithPhotoHasNoSizesAndException(): void
    {
        $this->createPhoto([FlickrPhotoModel::KEY_ID => self::PHOTO_ID]);
        FlickrClient::shouldReceive('getPhotoSizes')->andThrow(new FlickrApiPhotoGetSizesException(self::PHOTO_ID));

        $this->expectException(FlickrApiPhotoGetSizesException::class);
        $flickrDownload = FlickrDownloadModel::firstOrCreate(['photo_id' => self::PHOTO_ID, 'google_album_id' => 'foo']);

        (new FlickrDownloadPhotoToLocal($flickrDownload))->handle();
    }

    public function testHandleWithPhotoHasNoSizesAndCanNotDownloadFile(): void
    {
        $this->createPhoto([FlickrPhotoModel::KEY_ID => self::PHOTO_ID]);
        $this->mockFlickrClientCommand('getPhotoSizes');

        $this->mock(HttpClient::class, static function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('download')->andReturn(false);
        });

        $this->expectException(CurlDownloadFileException::class);
        $this->doesntExpectJobs(SyncPhotoToGooglePhoto::class);

        $flickrDownload = FlickrDownloadModel::firstOrCreate(['photo_id' => self::PHOTO_ID, 'google_album_id' => 'foo']);

        (new FlickrDownloadPhotoToLocal($flickrDownload))->handle();
    }

    public function testHandleWithSuccess(): void
    {
        $this->createPhoto([FlickrPhotoModel::KEY_ID => self::PHOTO_ID, 'title' => 'Test photo']);
        $this->mockFlickrClientCommand('getPhotoSizes');

        $this->mock(HttpClient::class, static function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('download')->andReturn('foo-file.png');
        });

        $flickrDownload = FlickrDownloadModel::firstOrCreate(['photo_id' => self::PHOTO_ID, 'google_album_id' => 'foo']);

        $this->expectsJobs(SyncPhotoToGooglePhoto::class);

        (new FlickrDownloadPhotoToLocal($flickrDownload))->handle();
    }

    /**
     * @param array $photo
     *
     * @return FlickrPhotoModel
     */
    private function createPhoto(array $photo): FlickrPhotoModel
    {
        $model = app(FlickrPhotoModel::class);
        $model->fill($photo)->save();

        return $model;
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
