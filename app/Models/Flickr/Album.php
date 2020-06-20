<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Mongodb
{
    public const KEY_ID = 'id';
    public const KEY_OWNER = 'owner';

    protected $collection = 'flickr_albums';

    /**
     * Return photos collection of this album
     * @return HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photo::class, Photo::KEY_ALBUM_ID, self::KEY_ID);
    }
}
