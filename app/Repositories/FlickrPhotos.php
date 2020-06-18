<?php

namespace App\Repositories;

use App\Models\FlickrPhoto;

class FlickrPhotos extends BaseRepository
{
    public function __construct(FlickrPhoto $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $id
     *
     * @return FlickrPhoto|null|\Illuminate\Database\Eloquent\Model
     */
    public function findById(string $id): ?FlickrPhoto
    {
        return $this->model->where(['id' => $id])->first();
    }

    /**
     * @param array $data
     *
     * @return \App\Models\FlickrPhoto|\Illuminate\Database\Eloquent\Model
     */
    public function save(array $data): FlickrPhoto
    {
        $model = clone($this->model);

        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $model->save();

        return $model;
    }
}
