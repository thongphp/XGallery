<?php

namespace Tests\Unit\Models\Flickr;

use App\Models\Flickr\FlickrContact;
use App\Models\Flickr\FlickrPhotoModel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FlickrMongoDatabase;

class FlickrContactModelTest extends TestCase
{
    use DatabaseMigrations, FlickrMongoDatabase;

    /**
     * @dataProvider flickrPhotosProvider
     *
     * @param string $nsid
     * @param array $photos
     * @param int $expected
     */
    public function testFlickrPhotos(string $nsid, array $photos, int $expected): void
    {
        $contact = $this->createContact($nsid);
        $this->assertSame($nsid, $contact->nsid);

        foreach ($photos as $photo) {
            $this->createPhoto($photo['id'], $photo['owner']);
        }

        $this->assertCount($expected, $contact->flickrphotos);
    }

    /**
     * @param string $nsid
     *
     * @return FlickrContact
     */
    private function createContact(string $nsid): FlickrContact
    {
        $model = app(FlickrContact::class);
        $model->fill(['nsid' => $nsid])->save();

        return $model;
    }

    /**
     * @param string $id
     * @param string $owner
     */
    private function createPhoto(string $id, string $owner): void
    {
        $model = app(FlickrPhotoModel::class);
        $model->fill(['id' => $id, 'owner' => $owner])->save();
    }

    /**
     * @return array|array[]
     */
    public function flickrPhotosProvider(): array
    {
        return [
            [
                'nsid' => 'foo@Bar',
                'photos' => [],
                'expected' => 0,
            ],
            [
                'nsid' => 'foo@Bar',
                'photos' => [
                    [
                        'id' => '123456',
                        'owner' => 'foo@Bar',
                    ],
                    [
                        'id' => '198954',
                        'owner' => 'foo@Bar',
                    ],
                ],
                'expected' => 2,
            ],
        ];
    }

    protected function tearDown(): void
    {
        $this->cleanUpFlickrMongoDb();

        parent::tearDown();
    }
}
