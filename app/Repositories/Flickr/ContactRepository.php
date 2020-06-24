<?php

namespace App\Repositories\Flickr;

use App\Jobs\Flickr\FlickrContact;
use App\Models\Flickr\Contact;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactRepository
 * @package App\Repositories\Flickr
 */
class ContactRepository extends BaseRepository
{
    /**
     * @param  Contact  $model
     */
    public function __construct(Contact $model)
    {
        parent::__construct($model);
    }

    public function getContactWithoutPhotos(): ?Contact
    {
        return $this->model
            ->where(['photo_state' => null])
            ->first();
    }

    /**
     * @param array $filter
     *
     * @return FlickrContact|null
     */
    public function getItemByConditions(array $filter = []): ?Contact
    {
        return $this->getItems($filter)->first();
    }

    /**
     * @param string $nsId
     *
     * @return Contact
     */
    public function findOrCreateByNsId(string $nsId): Contact
    {
        return $this->model::firstOrCreate(['nsid' => $nsId]);
    }

    /**
     * @param array $data
     *
     * @return Model|Contact
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
