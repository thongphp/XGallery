<?php

namespace App\Repositories;

use App\Models\Oauth;
use Illuminate\Database\Eloquent\Model;

class OAuthRepository extends BaseRepository
{
    public function __construct(Oauth $model)
    {
        parent::__construct($model);
    }

    // @TODO: [Thong] Create method for get OAuth information by type, email, etc... when have OAuth Manager for User

    /**
     * @param array $data
     *
     * @return Model|null|Oauth
     */
    public function findBy(array $data): ?Model
    {
        return $this->model->where($data)->first();
    }

    /**
     * @param array $data
     *
     * @return Model|Oauth
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
