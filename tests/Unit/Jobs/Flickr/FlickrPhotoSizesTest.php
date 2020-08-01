<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrPhotoSizes;
use App\Models\Flickr\FlickrPhoto;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrPhotoSizesTest extends TestCase
{
    private const PHOTO_ID = '32740490058';

    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function testHandleWithWrongNsid(): void
    {
        FlickrClient::shouldReceive('getPhotoSizes')->never();
        $this->createJob('foo')->handle();
    }

    public function testHandle(): void
    {
        app(FlickrPhoto::class)
            ->fill([FlickrPhoto::KEY_ID => self::PHOTO_ID])
            ->save();

        $this->mockFlickrClientCommand('getPhotoSizes');
        $this->createJob(self::PHOTO_ID)->handle();

        $photo = FlickrPhoto::where([FlickrPhoto::KEY_ID => self::PHOTO_ID])->first();
        $this->assertNotNull($photo);
        $this->assertNotNull($photo->{FlickrPhoto::KEY_SIZES});
    }

    /**
     * @param string $nsid
     *
     * @return FlickrPhotoSizes
     */
    private function createJob(string $nsid): FlickrPhotoSizes
    {
        return new FlickrPhotoSizes($nsid);
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
