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
}
