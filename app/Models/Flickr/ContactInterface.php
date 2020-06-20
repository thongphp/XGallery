<?php

namespace App\Models\Flickr;

/**
 * @property string|null $nsid
 * @property int|null $ispro
 * @property int|null $can_buy_pro
 * @property string|null $iconserver
 * @property int|null $iconfarm
 * @property string|null $path_alias
 * @property string|null $has_stats
 * @property string|null $gender
 * @property int|null $ignored
 * @property int|null $contact
 * @property string|null $friend
 * @property string|null $family
 * @property int|null $revcontact
 * @property int|null $revfriend
 * @property int|null $revfamily
 * @property string|null $username
 * @property string|null $realname
 * @property string|null $mbox_sha1sum
 * @property string|null $location
 * @property object|null $timezone
 * @property string|null $description
 * @property string|null $photosurl
 * @property string|null $profileurl
 * @property string|null $mobileurl
 * @property object|null $photos
 * @property \App\Models\Flickr\Photo[]|null $ref_photos;
 */
interface ContactInterface
{
    /**
     * @return bool
     */
    public function isDone(): bool;
}
