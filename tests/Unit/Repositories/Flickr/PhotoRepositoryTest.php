<?php

namespace Tests\Unit\Repositories\Flickr;

use App\Models\Flickr\FlickrContactModel;
use App\Models\Flickr\FlickrPhotoModel;
use App\Repositories\Flickr\PhotoRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PhotoRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    private PhotoRepository $repository;
    private FlickrPhotoModel $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(PhotoRepository::class);
        $this->model = app(FlickrPhotoModel::class);
    }

    public function testGetPhotosWithNoSizes(): void
    {
        $firstPhoto = $this->createPhoto(['id' => 'photo1']);
        $secondPhoto = $this->createPhoto(['id' => 'photo2']);
        $thirdPhoto = $this->createPhoto(['id' => 'photo3']);

        $this->assertSame(3, $this->repository->getPhotosWithNoSizes()->count());
        $firstPhoto->setAttribute('sizes', ['foo' => 'bar'])->save();
        $this->assertSame(2, $this->repository->getPhotosWithNoSizes()->count());
        $secondPhoto->setAttribute('sizes', [])->save();
        $this->assertSame(1, $this->repository->getPhotosWithNoSizes()->count());
        $thirdPhoto->setAttribute('sizes', false)->save();
        $this->assertSame(0, $this->repository->getPhotosWithNoSizes()->count());
    }

    public function testFindOrCreateById(): void
    {
        $photo = $this->repository->findOrCreateById('photo-foo');
        $this->assertNotNull($photo);
        $this->assertEquals('photo-foo', $photo->id);
        $this->assertEquals(1, $this->model->newModelQuery()->where(['id' => 'photo-foo'])->count());

        $photo2 = $this->repository->findOrCreateById('photo-foo');
        $this->assertNotNull($photo2);
        $this->assertEquals('photo-foo', $photo2->id);
        $this->assertEquals(1, $this->model->newModelQuery()->where(['id' => 'photo-foo'])->count());
    }

    public function testFindOrCreateByIdWithData(): void
    {
        $this->assertEquals(null, $this->repository->findOrCreateByIdWithData([]));
        $this->assertEquals(null, $this->repository->findOrCreateByIdWithData(['id']));
        $this->assertEquals(null, $this->repository->findOrCreateByIdWithData(['id' => false]));
        $this->assertEquals(null, $this->repository->findOrCreateByIdWithData(['id' => []]));

        $photo = $this->repository->findOrCreateByIdWithData(['id' => 999999]);
        $this->assertNotNull($photo);
        $this->assertSame(999999, $photo->id);
        $this->assertEquals(1, $this->model->newModelQuery()->where(['id' => 999999])->count());

        $photo = $this->repository->findOrCreateByIdWithData(['id' => 999999, 'foo' => 'bar']);
        $this->assertNotNull($photo);
        $this->assertSame(999999, $photo->id);
        $this->assertNull($photo->foo);
        $this->assertEquals(1, $this->model->newModelQuery()->where(['id' => 999999])->count());
    }

    public function testSave(): void
    {
        $photo = $this->repository->save(['id' => 999999]);
        $this->assertNotNull($photo);
        $this->assertSame(999999, $photo->id);

        $photo = $this->repository->save(['id' => 999999, 'foo' => 'bar']);
        $this->assertNotNull($photo);
        $this->assertSame(999999, $photo->id);
        $this->assertSame('bar', $photo->foo);
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

    protected function tearDown(): void
    {
        app(FlickrContactModel::class)->newModelQuery()->forceDelete();
        app(FlickrPhotoModel::class)->newModelQuery()->forceDelete();

        parent::tearDown();
    }
}
