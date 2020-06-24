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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * @package App\Models
 */
class Contact extends Mongodb implements ContactInterface
{
    use SoftDeletes;

    public const KEY_NSID = 'nsid';
    public const STATE_CONTACT_DETAIL = 1;

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
        'state'
    ];

    /**
     * @todo Use photos() instead
     * @return HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photo::class, Photo::KEY_OWNER, self::KEY_NSID);
    }
}
