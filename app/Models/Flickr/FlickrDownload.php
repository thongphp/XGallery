<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;

/**
 * Class FlickrDownload
 * @property int $user_id
 * @property string $photo_id
 * @property string $google_album_id
 * @package App\Models\Flickr
 */
class FlickrDownload extends Mongodb
{
    protected $fillable = [
        'user_id',
        'photo_id',
        'google_album_id',
    ];
}
