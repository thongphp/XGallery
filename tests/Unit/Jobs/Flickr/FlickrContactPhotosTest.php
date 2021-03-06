<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrContactPhotos;
use App\Models\Flickr\FlickrPhoto;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrContactPhotosTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function testHandleWithWrongNsid(): void
    {
        self::markTestSkipped('Re-write test');
        FlickrClient::shouldReceive('getPeoplePhotos')->never();
        $this->createJob('foo')->handle();
    }

    public function testHandle(): void
    {
        self::markTestSkipped('Re-write test');
        $this->mockFlickrClientCommand('getPeoplePhotos');
        $this->createJob('26440281@N02')->handle();

        $photos = FlickrPhoto::where([FlickrPhoto::KEY_OWNER => '26440281@N02'])->get();
        $this->assertEquals(5, $photos->count());
    }

    public function testHandleWithMoreThanOnePage(): void
    {
        self::markTestSkipped('Re-write test');
        $this->mockFlickrClientCommand('getPeoplePhotos', 'getPeoplePhotos_Has_Page2');
        $this->createJob('26440281@N02')->handle();

        $photos = FlickrPhoto::where([FlickrPhoto::KEY_OWNER => '26440281@N02'])->get();
        $this->assertEquals(5, $photos->count());
    }

    /**
     * @param string $nsid
     *
     * @return FlickrContactPhotos
     */
    private function createJob(string $nsid): FlickrContactPhotos
    {
        return new FlickrContactPhotos($nsid);
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
