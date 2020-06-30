<?php

namespace Tests\Unit\Repositories\Flickr;

use App\Models\Flickr\FlickrContactModel;
use App\Models\Flickr\FlickrPhotoModel;
use App\Repositories\Flickr\ContactRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ContactRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    private ContactRepository $repository;
    private FlickrContactModel $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ContactRepository::class);
        $this->model = app(FlickrContactModel::class);
    }

    public function testIsExist(): void
    {
        $this->assertFalse($this->repository->isExist('123456789@N01'));

        $this->createContact([FlickrContactModel::KEY_NSID => '123456789@N01']);

        $this->assertTrue($this->repository->isExist('123456789@N01'));
    }

    public function testGetItemByConditions(): void
    {

    }

    public function testResetStates(): void
    {
        $this->createContact([FlickrContactModel::KEY_NSID => '123456789@N01', FlickrContactModel::KEY_STATE => 'Foo']);
        $this->createContact([FlickrContactModel::KEY_NSID => '333456789@N02', FlickrContactModel::KEY_STATE => 'Bar']);

        $this->repository->resetStates();

        $this->assertNull($this->model->newModelQuery()->where([FlickrContactModel::KEY_NSID => '123456789@N01'])->first()->{FlickrContactModel::KEY_STATE});
        $this->assertNull($this->model->newModelQuery()->where([FlickrContactModel::KEY_NSID => '333456789@N02'])->first()->{FlickrContactModel::KEY_STATE});
    }

    public function testGetContactWithoutPhotos(): void
    {

    }

    public function testFindOrCreateByNsId(): void
    {

    }

    public function testResetPhotoStates(): void
    {

    }

    public function testSave(): void
    {

    }

    /**
     * @param array $contact
     *
     * @return FlickrContactModel
     */
    private function createContact(array $contact): FlickrContactModel
    {
        $model = app(FlickrContactModel::class);
        $model->fill($contact)->save();

        return $model;
    }

    protected function tearDown(): void
    {
        app(FlickrContactModel::class)->newModelQuery()->forceDelete();
        app(FlickrPhotoModel::class)->newModelQuery()->forceDelete();

        parent::tearDown();
    }
}
