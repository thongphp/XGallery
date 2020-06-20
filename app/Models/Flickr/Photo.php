<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;

class Photo extends Mongodb
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
        return $this->belongsTo(Album::class, self::KEY_ALBUM_ID, Album::KEY_ID);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Contact::class, self::KEY_OWNER_ID, Contact::KEY_NSID);
    }

    /**
     * @return string
     */
    public function getCover(): string
    {
        return !$this->sizes ? '' : $this->sizes[0]['source'];
    }
}
