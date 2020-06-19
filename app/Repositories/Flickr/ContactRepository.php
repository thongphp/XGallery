<?php

namespace App\Repositories\Flickr;

use App\Models\FlickrContact;
use App\Repositories\BaseRepository;

class ContactRepository extends BaseRepository
{
    /**
     * @param \App\Models\FlickrContact $model
     */
    public function __construct(FlickrContact $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $nsid
     *
     * @return null|FlickrContact
     */
    public function getContactByNsid(string $nsid): ?FlickrContact
    {
        return $this->model->where(['nsid' => $nsid])->first();
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model|FlickrContact
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
