<?php

namespace Tests\Feature\Console\Command\Flickr;

use App\Models\Flickr\FlickrContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrContactsTest extends TestCase
{
    use RefreshDatabase, FlickrClientMock, FlickrMongoDatabase;

    public function testExecuteGetAllContactOfCurrentUser(): void
    {
        $this->mockFlickrClientCommand('getContactsOfCurrentUser');
        $this->artisan('flickr:contacts')->assertExitCode(0);
        $this->assertEquals(5, FlickrContact::all()->count());
    }

    public function testExecuteGetAllContactOfCurrentUserWithMoreThan1Page(): void
    {
        $this->mockFlickrClientCommand('getContactsOfCurrentUser', 'getContactsOfCurrentUser_hasPage2');
        $this->artisan('flickr:contacts')->assertExitCode(0);
        self::assertEquals(5, FlickrContact::all()->count());
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
