<?php

namespace Tests\Unit\Jobs\Flickr;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrDownloadPhotoToLocalTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockFlickrClientCommand('getPeopleInfo');
    }

    public function testHandleWithWrongNsid(): void
    {
    }

    public function testHandle(): void
    {

    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
