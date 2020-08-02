<?php

namespace App\Repositories\Flickr;

use App\Models\Flickr\FlickrPhoto;
use App\Repositories\BaseRepository;
use App\Repositories\ConfigRepository;
use Illuminate\Database\Eloquent\Model;

class PhotoRepository extends BaseRepository
{
    /**
     * @param FlickrPhoto $model
     */
    public function __construct(FlickrPhoto $model)
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
        return $this->getItems(
            [
                FlickrPhoto::KEY_SIZES => null,
                ConfigRepository::KEY_PER_PAGE => $limit,
                'cache' => 0,
            ]
        );
    }

    /**
     * @param string $id
     *
     * @return FlickrPhoto
     * @deprecated
     */
    public function findOrCreateById(string $id): FlickrPhoto
    {
        return $this->model::firstOrCreate(['id' => $id]);
    }

    /**
     * @param array $data
     *
     * @return FlickrPhoto|null
     */
    public function findOrCreateByIdWithData(array $data): ?FlickrPhoto
    {
        if (empty($data) || empty($data['id'])) {
            return null;
        }

        return $this->model::firstOrCreate(['id' => $data['id']], $data);
    }

    /**
     * @param array $data
     *
     * @return FlickrPhoto|Model
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
