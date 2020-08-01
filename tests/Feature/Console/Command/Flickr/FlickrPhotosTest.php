<?php

namespace Tests\Feature\Console\Command\Flickr;

use App\Jobs\Flickr\FlickrContactFavouritePhotos;
use App\Jobs\Flickr\FlickrContactPhotos;
use App\Models\Flickr\FlickrContact;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrPhotosTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockFlickrClientCommand('getContactsOfCurrentUser');
    }

    public function testExecuteGetSingleContactAndStartGetPhotosAndFavPhotosQueue(): void
    {
        self::markTestSkipped('Need to re-write tests');

        $this->artisan('flickr:contacts')->assertExitCode(0);

        $this->expectsJobs(FlickrContactPhotos::class);
        $this->expectsJobs(FlickrContactFavouritePhotos::class);
        $this->artisan('flickr:photos');
    }

    public function testExecuteGetSingleContactAndStartGetPhotosAndFavPhotosQueueWithResetStates(): void
    {
        self::markTestSkipped('Need to re-write tests');

        $this->artisan('flickr:contacts')->assertExitCode(0);

        FlickrContact::query()->update([FlickrContact::KEY_PHOTO_STATE => 1]);
        self::assertEquals(0, FlickrContact::where([FlickrContact::KEY_PHOTO_STATE => null])->get()->count());

        $this->expectsJobs(FlickrContactPhotos::class);
        $this->expectsJobs(FlickrContactFavouritePhotos::class);
        $this->artisan('flickr:photos');

        self::assertEquals(4, FlickrContact::where([FlickrContact::KEY_PHOTO_STATE => null])->get()->count());
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
