<?php

namespace Tests\Unit\Repositories\Flickr;

use App\Models\Flickr\FlickrContact;
use App\Repositories\Flickr\ContactRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FlickrMongoDatabase;

class ContactRepositoryTest extends TestCase
{
    use DatabaseMigrations, FlickrMongoDatabase;

    private ContactRepository $repository;
    private FlickrContact $model;

    public function testIsExist(): void
    {
        $this->assertFalse($this->repository->isExist('123456789@N01'));

        $this->createContact([FlickrContact::KEY_NSID => '123456789@N01']);

        $this->assertTrue($this->repository->isExist('123456789@N01'));
    }

    /**
     * @return array
     */
    public function getItemByConditionsProvider(): array
    {
        return [
            [
                'contact' => [],
                'conditions' => [],
                'expectedHasResult' => false,
            ],
            [
                'contact' => [FlickrContact::KEY_NSID => '123456789@N01', FlickrContact::KEY_STATE => 'Foo'],
                'conditions' => [FlickrContact::KEY_NSID => 'Foo'],
                'expectedHasResult' => false,
            ],
            [
                'contact' => [FlickrContact::KEY_NSID => '123456789@N01', FlickrContact::KEY_STATE => 'Foo'],
                'conditions' => [FlickrContact::KEY_NSID => '123456789@N01'],
                'expectedHasResult' => true,
            ],
            [
                'contact' => [FlickrContact::KEY_NSID => '333456789@N02', FlickrContact::KEY_STATE => 'Bar'],
                'conditions' => [FlickrContact::KEY_STATE => 'Bar'],
                'expectedHasResult' => true,
            ],
        ];
    }

    /**
     * @dataProvider getItemByConditionsProvider
     *
     * @param array $contact
     * @param array $conditions
     * @param bool $expectedHasResult
     */
    public function testGetItemByConditions(array $contact, array $conditions, bool $expectedHasResult): void
    {
        $this->createContact($contact);
        $result = $this->repository->getItemByConditions($conditions);
        $this->assertSame($expectedHasResult, null !== $result);
    }

    public function testResetStates(): void
    {
        $this->createContact([FlickrContact::KEY_NSID => '123456789@N01', FlickrContact::KEY_STATE => 'Foo']);
        $this->createContact([FlickrContact::KEY_NSID => '333456789@N02', FlickrContact::KEY_STATE => 'Bar']);

        $this->repository->resetStates();

        $this->assertNull($this->model->newModelQuery()->where([FlickrContact::KEY_NSID => '123456789@N01'])->first()->{FlickrContact::KEY_STATE});
        $this->assertNull($this->model->newModelQuery()->where([FlickrContact::KEY_NSID => '333456789@N02'])->first()->{FlickrContact::KEY_STATE});
    }

    public function testGetContactWithoutPhotos(): void
    {
        $foo = $this->createContact([FlickrContact::KEY_NSID => 'Foo@N01']);
        $bar = $this->createContact([FlickrContact::KEY_NSID => 'Bar@N02']);

        $this->assertNotNull($this->repository->getContactWithoutPhotos());

        $foo->{FlickrContact::KEY_PHOTO_STATE} = 1;
        $foo->save();

        $this->assertNotNull($this->repository->getContactWithoutPhotos());

        $bar->{FlickrContact::KEY_PHOTO_STATE} = 1;
        $bar->save();

        $this->assertNull($this->repository->getContactWithoutPhotos());
    }

    public function testFindOrCreateByNsId(): void
    {
        $contactModel = $this->repository->findOrCreateByNsId('nsid@N01');
        $this->assertNotNull($contactModel);
        $this->assertEquals('nsid@N01', $contactModel->nsid);
        $this->assertEquals(1, $this->model->newModelQuery()->where([FlickrContact::KEY_NSID => 'nsid@N01'])->count());

        $contactModelSecond = $this->repository->findOrCreateByNsId('nsid@N01');
        $this->assertNotNull($contactModelSecond);
        $this->assertEquals('nsid@N01', $contactModelSecond->nsid);
        $this->assertEquals(1, $this->model->newModelQuery()->where([FlickrContact::KEY_NSID => 'nsid@N01'])->count());
    }

    public function testResetPhotoStates(): void
    {
        $this->createContact([FlickrContact::KEY_NSID => '123456789@N01', FlickrContact::KEY_PHOTO_STATE => 1]);
        $this->createContact([FlickrContact::KEY_NSID => '333456789@N02', FlickrContact::KEY_PHOTO_STATE => 1]);

        $this->repository->resetPhotoStates();

        $this->assertNull(
            $this->model->newModelQuery()
                ->where([FlickrContact::KEY_NSID => '123456789@N01'])
                ->first()
                ->{FlickrContact::KEY_PHOTO_STATE}
        );
        $this->assertNull(
            $this->model->newModelQuery()
                ->where([FlickrContact::KEY_NSID => '333456789@N02'])
                ->first()
                ->{FlickrContact::KEY_PHOTO_STATE}
        );
    }

    public function testSave(): void
    {
        $contactModel = $this->repository->save([FlickrContact::KEY_NSID => 999999]);
        $this->assertNotNull($contactModel);
        $this->assertSame(999999, $contactModel->nsid);

        $contactModel = $this->repository->save([FlickrContact::KEY_NSID => 999999, 'foo' => 'bar']);
        $this->assertNotNull($contactModel);
        $this->assertSame(999999, $contactModel->nsid);
        $this->assertSame('bar', $contactModel->foo);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ContactRepository::class);
        $this->model = app(FlickrContact::class);
    }

    /**
     * @param array $contact
     *
     * @return FlickrContact|null
     */
    private function createContact(array $contact): ?FlickrContact
    {
        if (!isset($contact[FlickrContact::KEY_NSID])) {
            return null;
        }

        $model = app(FlickrContact::class);
        $model->fill($contact)->save();

        return $model;
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
