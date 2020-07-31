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
 *
 * @property \App\Models\Flickr\FlickrContact|null $flickrcontact;
 */
interface FlickrPhotoInterface
{
    /**
     * @return bool
     */
    public function hasSizes(): bool;
}
