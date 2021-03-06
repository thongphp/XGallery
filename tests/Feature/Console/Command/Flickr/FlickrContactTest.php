<?php

namespace Tests\Feature\Console\Command\Flickr;

use App\Jobs\Flickr\FlickrContact;
use App\Models\Flickr\FlickrContact as FlickrContactModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrContactTest extends TestCase
{
    use RefreshDatabase, FlickrClientMock, FlickrMongoDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockFlickrClientCommand('getContactsOfCurrentUser');
    }

    public function testExecuteGetSingleContactAndStartGetContactInfoQueue(): void
    {
        $this->artisan('flickr:contacts')->assertExitCode(0);
        $this->expectsJobs(FlickrContact::class);
        $this->artisan('flickr:contact');
    }

    public function testExecuteGetSingleContactAndStartGetContactInfoQueueWithResetStates(): void
    {
        $this->artisan('flickr:contacts')->assertExitCode(0);

        FlickrContactModel::query()->update([FlickrContactModel::KEY_STATE => 1]);
        self::assertEquals(0, FlickrContactModel::where([FlickrContactModel::KEY_STATE => null])->get()->count());

        $this->expectsJobs(FlickrContact::class);
        $this->artisan('flickr:contact');

        self::assertEquals(5, FlickrContactModel::where([FlickrContactModel::KEY_STATE => null])->get()->count());
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
