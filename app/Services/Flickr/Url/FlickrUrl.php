<?php

namespace App\Services\Flickr\Url;

class FlickrUrl implements FlickrUrlInterface
{
    private string $url;
    private string $owner;
    private string $id;
    private string $type;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->url = $data[self::KEY_URL] ?? '';
        $this->owner = $data[self::KEY_OWNER] ?? '';
        $this->id = $data[self::KEY_ID] ?? '';
        $this->type = $data[self::KEY_TYPE] ?? '';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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
