<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrPhotoSizes;
use App\Models\Flickr\FlickrPhotoModel;
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
        app(FlickrPhotoModel::class)
            ->fill([FlickrPhotoModel::KEY_ID => self::PHOTO_ID])
            ->save();

        $this->mockFlickrClientCommand('getPhotoSizes');
        $this->createJob(self::PHOTO_ID)->handle();

        $photo = FlickrPhotoModel::where([FlickrPhotoModel::KEY_ID => self::PHOTO_ID])->first();
        $this->assertNotNull($photo);
        $this->assertNotNull($photo->{FlickrPhotoModel::KEY_SIZES});
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
