<?php

namespace App\Services\Flickr\Url;

/**
 * Interface FlickrUrlInterface
 * @package App\Services\Flickr\Url
 */
interface FlickrUrlInterface
{
    public const KEY_URL = 'url';
    public const KEY_OWNER = 'owner';
    public const KEY_ID = 'id';
    public const KEY_TYPE = 'type';

    public const TYPE_ALBUM = 'album';
    public const TYPE_PHOTO = 'photo';
    public const TYPE_GALLERY = 'gallery';
    public const TYPE_PROFILE = 'profile';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @return string
     */
    public function getOwner(): string;

    /**
     * @return string
     */
    public function getId(): string;
}
