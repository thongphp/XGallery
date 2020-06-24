<?php

namespace App\Models\Flickr;

/**
 * @property string|null $id
 * @property string|null $owner
 * @property string|null $secret
 * @property string|null $server
 * @property int|null $farm
 * @property string|null $title
 * @property int|null $ispublic
 * @property int|null $isfriend
 * @property int|null $isfamily
 * @property array|null $sizes
 * @property \App\Models\Flickr\Contact|null $ref_owner;
 * @property string|null $google_album_id;
 * @property string|null $google_media_id;
 */
interface PhotoInterface
{
    /**
     * @return bool
     */
    public function hasSizes(): bool;
}
