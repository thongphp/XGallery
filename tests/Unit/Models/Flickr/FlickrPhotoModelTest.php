<?php

namespace Tests\Unit\Models\Flickr;

use App\Models\Flickr\FlickrContact;
use App\Models\Flickr\FlickrPhotoModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FlickrMongoDatabase;

class FlickrPhotoModelTest extends TestCase
{
    use DatabaseMigrations, FlickrMongoDatabase;

    /**
     * @dataProvider flickrContactProvider
     *
     * @param array $contact
     * @param array $photo
     * @param bool $expectedContactNotNull
     */
    public function testFlickrContact(array $contact, array $photo, bool $expectedContactNotNull): void
    {
        $this->createContact($contact);
        $photo = $this->createPhoto($photo);

        $this->assertSame($expectedContactNotNull, null !== $photo->flickrcontact);
    }

    /**
     * @param array $contact
     *
     * @return FlickrContact
     */
    private function createContact(array $contact): FlickrContact
    {
        $model = app(FlickrContact::class);
        $model->fill($contact)->save();

        return $model;
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

    /**
     * @dataProvider getCoverProvider
     *
     * @param array $photo
     * @param string $expected
     */
    public function testGetCover(array $photo, string $expected): void
    {
        $photo = $this->createPhoto($photo);
        $this->assertEquals($expected, $photo->getCover());
    }

    /**
     * @dataProvider hasSizesProvider
     *
     * @param array $photo
     * @param bool $expected
     */
    public function testHasSizes(array $photo, bool $expected): void
    {
        $photo = $this->createPhoto($photo);
        $this->assertEquals($expected, $photo->hasSizes());
    }

    /**
     * @return array|array[]
     */
    public function flickrContactProvider(): array
    {
        return [
            [
                'contact' => [
                    'nsid' => 'foo@Bar',
                ],
                'photo' => [
                    'id' => '123456',
                    'owner' => null,
                ],
                'expectedContactNotNull' => false,
            ],
            [
                'contact' => [
                    'nsid' => 'foo@Bar',
                ],
                'photo' => [
                    'id' => '123456',
                    'owner' => 'bar@foo',
                ],
                'expectedContactNotNull' => false,
            ],
            [
                'contact' => [
                    'nsid' => 'foo@Bar',
                ],
                'photo' => [
                    'id' => '123456',
                    'owner' => 'foo@Bar',
                ],
                'expectedContactNotNull' => true,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getCoverProvider(): array
    {
        return [
            [
                'photo' => [
                    'id' => '123456',
                ],
                'expected' => 'https://via.placeholder.com/150',
            ],
            [
                'photo' => [
                    'id' => '654321',
                    'sizes' => [
                        [
                            'source' => 'source-foo',
                        ],
                    ],
                ],
                'expected' => 'source-foo',
            ],
        ];
    }

    /**
     * @return array
     */
    public function hasSizesProvider(): array
    {
        return [
            [
                'photo' => [
                    'id' => '123456',
                ],
                'expected' => false,
            ],
            [
                'photo' => [
                    'id' => '654321',
                    'sizes' => [
                        [
                            'foo' => 'bar',
                        ],
                        [
                            'bar' => 'foo',
                        ],
                    ],
                ],
                'expected' => true,
            ],
        ];
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
