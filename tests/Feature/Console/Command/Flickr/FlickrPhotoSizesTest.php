<?php

namespace Tests\Feature\Console\Command\Flickr;

use App\Jobs\Flickr\FlickrPhotoSizes;
use App\Models\Flickr\FlickrPhotoModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrClientMock;
use Tests\Traits\FlickrMongoDatabase;

class FlickrPhotoSizesTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, FlickrClientMock, FlickrMongoDatabase;

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
        $this->createPhoto([FlickrPhotoModel::KEY_ID => '1']);

        $this->expectsJobs(FlickrPhotoSizes::class);
        $this->artisan('flickr:photossizes')->assertExitCode(0);
    }

    /**
     * @param array $photo
     *
     * @return FlickrPhotoModel
     */
    private function createPhoto(array $photo): FlickrPhotoModel
    {
        $model = app(FlickrPhotoModel::class);
        $model->fill($photo)->save();

        return $model;
    }

    public function testExecuteGetAllPhotosAndStartGetPhotoSizesWithNoEmptySizesPhoto(): void
    {
        $this->createPhoto([FlickrPhotoModel::KEY_ID => '1', FlickrPhotoModel::KEY_SIZES => ['foo' => 'bar']]);
        $this->createPhoto([FlickrPhotoModel::KEY_ID => '2', FlickrPhotoModel::KEY_SIZES => ['bar' => 'foo']]);

        $this->doesntExpectJobs(FlickrPhotoSizes::class);
        $this->artisan('flickr:photossizes')->assertExitCode(0);
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
