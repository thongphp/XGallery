<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrContactPhotos;
use App\Jobs\Flickr\FlickrPhotoSizes;
use App\Models\Flickr\FlickrPhotoModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrPhotoSizesTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function testHandleWithWrongNsid(): void
    {
        FlickrClient::shouldReceive('getPhotoSizes')->never();
        $this->createJob('foo')->handle();
    }

    public function testHandle(): void
    {
        app(FlickrPhotoModel::class)
            ->fill([FlickrPhotoModel::KEY_ID => '32740490058'])
            ->save();

        $this->mockFlickrClientCommand('getPhotoSizes');
        $this->createJob('32740490058')->handle();

        $photo = FlickrPhotoModel::where([FlickrPhotoModel::KEY_ID => '32740490058'])->first();
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
