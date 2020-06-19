<?php

namespace App\Models;

use App\Database\Mongodb;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlickrAlbum extends Mongodb
{
    protected $collection = 'flickr_albums';

    public const KEY_ID = 'id';
    public const KEY_OWNER = 'owner';

    /**
     * Return photos collection of this album
     * @return HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(FlickrPhoto::class, FlickrPhoto::KEY_ALBUM_ID, self::KEY_ID);
    }
}
