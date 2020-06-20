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
 * @property bool|null $status
 * @property \App\Models\Flickr\Contact|null $ref_owner;
 */
interface PhotoInterface
{
    /**
     * @return bool
     */
    public function hasSizes(): bool;

    /**
     * @return bool
     */
    public function isDone(): bool;
}
