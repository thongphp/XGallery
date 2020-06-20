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
class Contact extends Mongodb implements ContactInterface
{
    public const KEY_NSID = 'nsid';

    protected $collection = 'flickr_contacts';
    protected $fillable = [
        'nsid',
        'ispro',
        'can_buy_pro',
        'iconserver',
        'iconfarm',
        'path_alias',
        'has_stats',
        'gender',
        'ignored',
        'contact',
        'friend',
        'family',
        'revcontact',
        'revfriend',
        'revfamily',
        'username',
        'realname',
        'mbox_sha1sum',
        'location',
        'timezone',
        'description',
        'photosurl',
        'profileurl',
        'mobileurl',
        'photos',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function refPhotos()
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

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return !empty($this->mbox_sha1sum);
    }

}
