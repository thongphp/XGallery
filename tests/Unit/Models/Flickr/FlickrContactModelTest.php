<?php

namespace Tests\Unit\Models\Flickr;

use App\Models\Flickr\FlickrContactModel;
use App\Models\Flickr\FlickrPhotoModel;
use App\Repositories\Flickr\ContactRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FlickrContactModelTest extends TestCase
{
    use DatabaseMigrations;

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
            ]
        ];
    }

    /**
     * @param string $nsid
     *
     * @return FlickrContactModel
     */
    private function createContact(string $nsid): FlickrContactModel
    {
        $model = app(FlickrContactModel::class);
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

    protected function tearDown(): void
    {
        app(FlickrContactModel::class)->newModelQuery()->forceDelete();
        app(FlickrPhotoModel::class)->newModelQuery()->forceDelete();

        parent::tearDown();
    }
}
