<?php

namespace Tests\Feature\Console\Command\Flickr;

use App\Models\Flickr\FlickrContact;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrContactTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

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

        FlickrContact::query()->update([FlickrContact::KEY_STATE => 1]);
        $this->assertEquals(0, FlickrContact::where([FlickrContact::KEY_STATE => null])->get()->count());

        $this->expectsJobs(FlickrContact::class);
        $this->artisan('flickr:contact');

        $this->assertEquals(5, FlickrContact::where([FlickrContact::KEY_STATE => null])->get()->count());
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
