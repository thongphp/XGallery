<?php

namespace Tests\Unit\Repositories\Flickr;

use App\Models\Flickr\FlickrPhoto;
use App\Repositories\Flickr\PhotoRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FlickrMongoDatabase;

class PhotoRepositoryTest extends TestCase
{
    use DatabaseMigrations, FlickrMongoDatabase;

    private PhotoRepository $repository;
    private FlickrPhoto $model;

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

    /**
     * @param array $photo
     *
     * @return FlickrPhoto
     */
    private function createPhoto(array $photo): FlickrPhoto
    {
        $model = app(FlickrPhoto::class);
        $model->fill($photo)->save();

        return $model;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(PhotoRepository::class);
        $this->model = app(FlickrPhoto::class);
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
