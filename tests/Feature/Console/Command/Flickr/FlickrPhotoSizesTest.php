<?php

namespace Tests\Feature\Console\Command\Flickr;

use App\Jobs\Flickr\FlickrPhotoSizes;
use App\Models\Flickr\FlickrPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrPhotoSizesTest extends TestCase
{
    use RefreshDatabase, FlickrClientMock, FlickrMongoDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockFlickrClientCommand('getContactsOfCurrentUser');
    }

    public function testExecuteWithNoPhotos(): void
    {
        $this->doesntExpectJobs(FlickrPhotoSizes::class);
        $this->artisan('flickr:photossizes')->assertExitCode(0);
    }

    public function testExecuteGetAllPhotosAndStartGetPhotoSizes(): void
    {
        $this->createPhoto([FlickrPhoto::KEY_ID => '1']);

        $this->expectsJobs(FlickrPhotoSizes::class);
        $this->artisan('flickr:photossizes')->assertExitCode(0);
    }

    /**
     * @param  array  $photo
     *
     * @return FlickrPhoto
     */
    private function createPhoto(array $photo): FlickrPhoto
    {
        $model = app(FlickrPhoto::class);
        $model->fill($photo)->save();

        return $model;
    }

    public function testExecuteGetAllPhotosAndStartGetPhotoSizesWithNoEmptySizesPhoto(): void
    {
        $this->createPhoto([FlickrPhoto::KEY_ID => '1', FlickrPhoto::KEY_SIZES => ['foo' => 'bar']]);
        $this->createPhoto([FlickrPhoto::KEY_ID => '2', FlickrPhoto::KEY_SIZES => ['bar' => 'foo']]);

        $this->doesntExpectJobs(FlickrPhotoSizes::class);
        $this->artisan('flickr:photossizes')->assertExitCode(0);
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
