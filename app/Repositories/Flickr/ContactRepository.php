<?php

namespace App\Repositories\Flickr;

use App\Models\Flickr\FlickrContact;
use App\Repositories\BaseRepository;
use App\Repositories\ConfigRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactRepository
 * @package App\Repositories\Flickr
 */
class ContactRepository extends BaseRepository
{
    protected string $primaryKey = 'nsid';

    /**
     * @param FlickrContact $model
     */
    public function __construct(FlickrContact $model)
    {
        parent::__construct($model);
    }

    /**
     * @return FlickrContact|Model|null
     */
    public function getContactWithoutPhotos(): ?FlickrContact
    {
        return $this->model
            ->where([FlickrContact::KEY_PHOTO_STATE => null])
            ->first();
    }

    /**
     * @SuppressWarnings("unused")
     *
     * @param null $state
     *
     * @return FlickrContact|null
     */
    public function getOldestContact($state = null): ?FlickrContact
    {
        return $this->getItems(
            [
                ConfigRepository::KEY_SORT_BY => 'updated_at',
                FlickrContact::KEY_STATE => null, 'cache' => 0,
            ]
        )->first();
    }

    /**
     * @param array $filter
     *
     * @return FlickrContact|Model|null
     */
    public function getItemByConditions(array $filter = []): ?FlickrContact
    {
        return $this->getItems($filter)->first();
    }

    /**
     * @param string $nsId
     *
     * @return FlickrContact|Model
     */
    public function findOrCreateByNsId(string $nsId): FlickrContact
    {
        return $this->model::firstOrCreate([FlickrContact::KEY_NSID => $nsId]);
    }

    /**
     * @param string $nsId
     *
     * @return bool
     */
    public function isExist(string $nsId): bool
    {
        return null !== $this->model::where([FlickrContact::KEY_NSID => $nsId])->first();
    }

    public function resetStates(): void
    {
        $this->model::query()->update([FlickrContact::KEY_STATE => null]);
    }

    public function resetPhotoStates(): void
    {
        $this->model::query()->update([FlickrContact::KEY_PHOTO_STATE => null]);
    }

    /**
     * @param array $data
     *
     * @return Model|FlickrContact
     */
    public function save(array $data)
    {
        $model = clone($this->model);

        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }

        $model->save();

        return $model;
    }
}
