<?php

namespace App\Models;

use App\Database\Mongodb;

class FlickrPhoto extends Mongodb
{
    protected $collection = 'flickr_photos';
}
