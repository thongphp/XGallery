<?php

namespace App\Services\Flickr\Url;

abstract class AbstractFlickrUrl implements FlickrUrlInterface
{
    public const TYPE = '';

    private string $url;
    private string $owner;
    private string $id;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->url = $data[self::KEY_URL] ?? '';
        $this->owner = $data[self::KEY_OWNER] ?? '';
        $this->id = $data[self::KEY_ID] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
