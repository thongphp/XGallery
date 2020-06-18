<?php

namespace App\Models;

use App\Database\Mongodb;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlickrAlbum extends Mongodb
{
    protected $collection = 'flickr_albums';

    /**
     * Return photos collection of this album
     * @return HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(FlickrPhotos::class, 'albumId', 'id');
    }
}
