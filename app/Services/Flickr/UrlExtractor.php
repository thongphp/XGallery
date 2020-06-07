<?php

namespace App\Services\Flickr;

use App\Services\Flickr\Url\FlickrUrl;
use App\Services\Flickr\Url\FlickrUrlInterface;

class UrlExtractor
{
    private const MAPPER = 'mapper';
    private const REGEX = 'regex';

    public const DETECTOR = [
        FlickrUrlInterface::TYPE_ALBUM => [
            self::REGEX => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/(\w+)\/albums\/(?:albumId\/)?(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
        ],
        FlickrUrlInterface::TYPE_PHOTO => [
            self::REGEX => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/(\w+)\/(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
        ],
        FlickrUrlInterface::TYPE_GALLERY => [
            self::REGEX => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/(\w+)\/galleries\/(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
        ],
        FlickrUrlInterface::TYPE_PROFILE => [
            self::REGEX => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/people\/(\w+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_ID => 1,
                FlickrUrlInterface::KEY_OWNER => 1,
            ],
        ],
    ];

    /**
     * @param  string  $url
     *
     * @return \App\Services\Flickr\Url\FlickrUrlInterface|null
     */
    public function extract(string $url): ?FlickrUrlInterface
    {
        return $this->detect($url, self::DETECTOR);
    }

    /**
     * @param  string  $url
     * @param  array  $detectors
     *
     * @return \App\Services\Flickr\Url\FlickrUrlInterface|null
     */
    private function detect(string $url, array $detectors): ?FlickrUrlInterface
    {
        if (empty($url)) {
            return null;
        }

        foreach ($detectors as $type => $detector) {
            $result = $this->check($url, $type, $detector);

            if ($result === null) {
                continue;
            }

            return $result;
        }

        return null;
    }

    /**
     * @param  string  $url
     * @param  string  $type
     * @param  array  $detector
     *
     * @return \App\Services\Flickr\Url\FlickrUrlInterface|null
     */
    private function check(string $url, string $type, array $detector): ?FlickrUrlInterface
    {
        if (!preg_match($detector[self::REGEX], $url, $matches)) {
            return null;
        }

        $result = [
            FlickrUrlInterface::KEY_URL => $url,
            FlickrUrlInterface::KEY_TYPE => $type,
        ];

        foreach ($detector[self::MAPPER] as $key => $index) {
            $result[$key] = $matches[$index];
        }

        return new FlickrUrl($result);
    }
}
