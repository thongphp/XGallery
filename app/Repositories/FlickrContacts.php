<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Repositories
 */
class FlickrContacts extends BaseRepository
{
    /**
     * @param  \App\Models\FlickrContacts  $model
     */
    public function __construct(\App\Models\FlickrContacts $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  string  $nsid
     *
     * @return mixed
     */
    public function getContactByNsid(string $nsid)
    {
        return $this->model->where(['nsid' => $nsid])->first();
    }

    /**
     * @param  array  $data
     *
     * @return Model
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
