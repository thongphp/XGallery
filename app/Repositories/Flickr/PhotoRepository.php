<?php

namespace App\Repositories\Flickr;

use App\Models\Flickr\FlickrPhotoModel;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class PhotoRepository extends BaseRepository
{
    /**
     * @param FlickrPhotoModel $model
     */
    public function __construct(FlickrPhotoModel $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $limit
     *
     * @return mixed
     */
    public function getPhotosWithNoSizes(int $limit = 100)
    {
        return $this->getItems([FlickrPhotoModel::KEY_SIZES => null, 'per-page' => $limit, 'cache' => 0]);
    }

    /**
     * @param string $id
     *
     * @return FlickrPhotoModel
     * @deprecated
     */
    public function findOrCreateById(string $id): FlickrPhotoModel
    {
        return $this->model::firstOrCreate(['id' => $id]);
    }

    /**
     * @param array $data
     *
     * @return FlickrPhotoModel|null
     */
    public function findOrCreateByIdWithData(array $data): ?FlickrPhotoModel
    {
        if (empty($data) || empty($data['id'])) {
            return null;
        }

        return $this->model::firstOrCreate(['id' => $data['id']], $data);
    }

    /**
     * @param array $data
     *
     * @return FlickrPhotoModel|Model
     */
    public function save(array $data): FlickrPhotoModel
    {
        $model = clone($this->model);

        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $model->save();

        return $model;
    }
}
