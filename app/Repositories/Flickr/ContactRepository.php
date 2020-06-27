<?php

namespace App\Repositories\Flickr;

use App\Models\Flickr\FlickrContactModel;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactRepository
 * @package App\Repositories\Flickr
 */
class ContactRepository extends BaseRepository
{
    /**
     * @param FlickrContactModel $model
     */
    public function __construct(FlickrContactModel $model)
    {
        parent::__construct($model);
    }

    /**
     * @return FlickrContactModel|null
     */
    public function getContactWithoutPhotos(): ?FlickrContactModel
    {
        return $this->model
            ->where([FlickrContactModel::KEY_PHOTO_STATE => null])
            ->first();
    }

    /**
     * @param array $filter
     *
     * @return FlickrContactModel|null
     */
    public function getItemByConditions(array $filter = []): ?FlickrContactModel
    {
        return $this->getItems($filter)->first();
    }

    /**
     * @param string $nsId
     *
     * @return FlickrContactModel
     */
    public function findOrCreateByNsId(string $nsId): FlickrContactModel
    {
        return $this->model::firstOrCreate([FlickrContactModel::KEY_NSID => $nsId]);
    }

    /**
     * @param string $nsId
     *
     * @return bool
     */
    public function isExist(string $nsId): bool
    {
        return null !== $this->model::where([FlickrContactModel::KEY_NSID => $nsId])->first();
    }

    public function resetStates(): void
    {
        $this->model::where([])->update([FlickrContactModel::KEY_STATE => null]);
    }

    public function resetPhotoStates(): void
    {
        $this->model::where([])->update([FlickrContactModel::KEY_PHOTO_STATE => null]);
    }

    /**
     * @param array $data
     *
     * @return Model|FlickrContactModel
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
