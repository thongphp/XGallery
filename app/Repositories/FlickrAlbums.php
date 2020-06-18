<?php

namespace App\Repositories;

use App\Models\FlickrAlbum;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FlickrAlbums
 * @package App\Repositories
 */
class FlickrAlbums extends BaseRepository
{
    public function __construct(FlickrAlbum $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Model|null|FlickrAlbum
     */
    public function findByAlbumId($id): ?Model
    {
        return $this->model->where(['id' => $id])->first();
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model|FlickrAlbum
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
