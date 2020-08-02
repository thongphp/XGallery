<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrContact;
use App\Jobs\Flickr\FlickrContactFavouritePhotos;
use App\Models\Flickr\FlickrPhoto;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrContactFavouritePhotosTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function testHandleWithWrongNsid(): void
    {
        $this->createJob('foo')->handle();
        FlickrClient::shouldReceive('getFavouritePhotosOfUser')->never();
    }

    public function testHandle(): void
    {
        $this->mockFlickrClientCommand('getFavouritePhotosOfUser');
        $this->expectsJobs(FlickrContact::class);
        $this->createJob('26440281@N02')->handle();

        $photos = FlickrPhoto::all();
        $this->assertEquals(6, $photos->count());
    }

    public function testHandleWithMoreThanOnePage(): void
    {
        $this->mockFlickrClientCommand('getFavouritePhotosOfUser', 'getFavouritePhotosOfUser_Has_Page2');
        $this->expectsJobs(FlickrContact::class);
        $this->createJob('26440281@N02')->handle();

        $photos = FlickrPhoto::all();
        $this->assertEquals(6, $photos->count());
    }

    /**
     * @param string $nsid
     *
     * @return FlickrContactFavouritePhotos
     */
    private function createJob(string $nsid): FlickrContactFavouritePhotos
    {
        return new FlickrContactFavouritePhotos($nsid);
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
