<?php

namespace App\Repositories\Flickr;

use App\Models\Flickr\Photo;
use App\Repositories\BaseRepository;

class PhotoRepository extends BaseRepository
{
    /**
     * @param Photo $model
     */
    public function __construct(Photo $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getPhotosWithNoSizes(int $limit = 100)
    {
        return $this->getItems(['sizes' => null, 'per-page' => $limit, 'cache' =>0]);
    }

    /**
     * @param string $id
     *
     * @return Photo
     * @deprecated
     */
    public function findOrCreateById(string $id): Photo
    {
        return $this->model::firstOrCreate(['id' => $id]);
    }

    /**
     * @param array $data
     * @return Photo
     */
    public function findOrCreateByIdWithData(array $data): Photo
    {
        return $this->model::firstOrCreate(['id' => $data['id']], $data);
    }

    /**
     * @param array $data
     *
     * @return Photo|\Illuminate\Database\Eloquent\Model
     */
    public function save(array $data): Photo
    {
        $model = clone($this->model);

        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $model->save();

        return $model;
    }
}
