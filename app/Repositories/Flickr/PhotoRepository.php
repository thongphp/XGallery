<?php

namespace App\Repositories\Flickr;

use App\Models\Flickr\Photo;
use App\Repositories\BaseRepository;

class PhotoRepository extends BaseRepository
{
    /**
     * @param \App\Models\Flickr\Photo $model
     */
    public function __construct(Photo $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $id
     *
     * @return \App\Models\Flickr\Photo
     */
    public function findOrCreateById(string $id): Photo
    {
        return $this->model::firstOrCreate(['id' => $id]);
    }

    /**
     * @param array $data
     *
     * @return \App\Models\Flickr\Photo|\Illuminate\Database\Eloquent\Model
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
