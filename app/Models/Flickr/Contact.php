<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Flickr;

use App\Database\Mongodb;

/**
 * @package App\Models
 */
class Contact extends Mongodb
{
    protected $collection = 'flickr_contacts';

    public const KEY_NSID = 'nsid';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photo::class, Photo::KEY_OWNER_ID, self::KEY_NSID);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function albums()
    {
        return $this->hasMany(Album::class, Album::KEY_OWNER, self::KEY_NSID);
    }
}
