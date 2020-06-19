<?php

namespace App\Models;

use App\Database\Mongodb;

class FlickrPhoto extends Mongodb
{
    protected $collection = 'flickr_photos';

    public const KEY_ALBUM_ID = 'album_id';
    public const KEY_OWNER_ID = 'owner_id';
    public const KEY_STATUS = 'status';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function album()
    {
        return $this->belongsTo(FlickrAlbum::class, self::KEY_ALBUM_ID, FlickrAlbum::KEY_ID);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(FlickrContact::class, self::KEY_OWNER_ID, FlickrContact::KEY_NSID);
    }

    /**
     * @return string
     */
    public function getCover(): string
    {
        return !$this->sizes ? '' : $this->sizes[0]['source'];
    }
}
