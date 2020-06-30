<?php

namespace Tests\Unit\Jobs\Flickr;

use App\Facades\GooglePhotoClient;
use App\Jobs\Flickr\FlickrDownloadContact;
use App\Jobs\Flickr\FlickrDownloadPhotoToLocal;
use App\Models\Flickr\FlickrContactModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrDownloadContactTest extends TestCase
{
    private const CONTACT_NSID = '26440281@N02';

    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function testHandle(): void
    {
        GooglePhotoClient::shouldReceive('createAlbum')
            ->andReturn((object)['id' => 'google-album-' . self::CONTACT_NSID]);
        $this->mockFlickrClientCommand('getPeopleInfo');
        $this->mockFlickrClientCommand('getPeoplePhotos');

        $this->expectsJobs(FlickrDownloadPhotoToLocal::class);

        (new FlickrDownloadContact(self::CONTACT_NSID))->handle();

        $contact = FlickrContactModel::where([FlickrContactModel::KEY_NSID => self::CONTACT_NSID])->first();

        $this->assertNotNull($contact);
        $this->assertSame('-- Joe\'s photos --', $contact->username);
        $this->assertSame(FlickrContactModel::STATE_CONTACT_DETAIL, $contact->{FlickrContactModel::KEY_STATE});
    }

    public function testHandleWithMorePages(): void
    {
        GooglePhotoClient::shouldReceive('createAlbum')
            ->andReturn((object)['id' => 'google-album-' . self::CONTACT_NSID]);
        $this->mockFlickrClientCommand('getPeopleInfo');
        $this->mockFlickrClientCommand('getPeoplePhotos', 'getPeoplePhotos_Has_Page2');

        $this->expectsJobs(FlickrDownloadPhotoToLocal::class);

        (new FlickrDownloadContact(self::CONTACT_NSID))->handle();

        $contact = FlickrContactModel::where([FlickrContactModel::KEY_NSID => self::CONTACT_NSID])->first();

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
