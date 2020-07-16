<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;

class FlickrDownload extends Mongodb
{
    protected $fillable = ['user_id', 'album_id', 'photos_count'];
}
