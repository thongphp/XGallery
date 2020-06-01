<?php

namespace App\Repositories;

/**
 * Class FlickrContacts
 * @package App\Repositories
 */
class FlickrContacts extends BaseRepository
{
    public function __construct(\App\Models\FlickrContacts $model)
    {
        parent::__construct($model);
    }

    public function getContactByNsid(string $nsid)
    {
        return $this->model->where(['nsid' => $nsid])->first();
    }

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
