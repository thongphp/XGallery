<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrContact;
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

        $this->mockFlickrClientCommand('getPeopleInfo');
    }

    public function testHandleWithWrongNsid(): void
    {
        self::markTestSkipped('Re-write test');
        FlickrClient::shouldReceive('getPeopleInfo')->never();
        $this->createJob('foo')->handle();
    }

    /**
     * @param string $nsid
     *
     * @return FlickrContact
     */
    private function createJob(string $nsid): FlickrContact
    {
        return new FlickrContact($nsid);
    }

    public function testHandle(): void
    {
        self::markTestSkipped('Re-write test');
        $this->createJob('26440281@N02')->handle();

        $contact = FlickrContact::where([FlickrContact::KEY_NSID => '26440281@N02'])->first();

        $this->assertNotNull($contact);
        $this->assertSame('-- Joe\'s photos --', $contact->username);
        $this->assertSame(FlickrContact::STATE_CONTACT_DETAIL, $contact->{FlickrContact::KEY_STATE});
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
