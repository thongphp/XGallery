<?php

namespace App\Repositories\Flickr;

use App\Models\Flickr\Contact;
use App\Repositories\BaseRepository;

class ContactRepository extends BaseRepository
{
    /**
     * @param \App\Models\Flickr\Contact $model
     */
    public function __construct(Contact $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $nsid
     *
     * @return null|Contact
     */
    public function getContactByNsid(string $nsid): ?Contact
    {
        return $this->model->where(['nsid' => $nsid])->first();
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model|\App\Models\Flickr\Contact
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
