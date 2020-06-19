<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models;

use App\Database\Mongodb;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @package App\Models
 */
class FlickrContact extends Mongodb
{
    protected $collection = 'flickr_contacts';

    public const KEY_NSID = 'nsid';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(FlickrPhoto::class, FlickrPhoto::KEY_OWNER_ID, self::KEY_NSID);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function albums()
    {
        return $this->hasMany(FlickrAlbum::class, FlickrAlbum::KEY_OWNER, self::KEY_NSID);
    }
}
