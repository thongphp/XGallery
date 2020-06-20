<?php

namespace App\Repositories\Flickr;

use App\Models\Flickr\Album;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AlbumRepository
 * @package App\Repositories
 */
class AlbumRepository extends BaseRepository
{
    /**
     * @param \App\Models\Flickr\Album $model
     */
    public function __construct(Album $model)
    {
        parent::__construct($model);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Model|null|Album
     */
    public function findByAlbumId($id): ?Model
    {
        return $this->model->where(['id' => $id])->first();
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model|Album
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
