<?php

namespace App\Services\Flickr\Url;

interface FlickrUrlInterface
{
    public const KEY_URL = 'url';
    public const KEY_OWNER = 'owner';
    public const KEY_ID = 'id';

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
