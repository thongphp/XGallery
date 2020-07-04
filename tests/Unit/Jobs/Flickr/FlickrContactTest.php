<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\FlickrClient;
use App\Jobs\Flickr\FlickrContact;
use App\Models\Flickr\FlickrContactModel;
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
        $this->createJob('26440281@N02')->handle();

        $contact = FlickrContactModel::where([FlickrContactModel::KEY_NSID => '26440281@N02'])->first();

        $this->assertNotNull($contact);
        $this->assertSame('-- Joe\'s photos --', $contact->username);
        $this->assertSame(FlickrContactModel::STATE_CONTACT_DETAIL, $contact->{FlickrContactModel::KEY_STATE});
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
