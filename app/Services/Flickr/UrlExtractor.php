<?php

namespace App\Services\Flickr;

use App\Services\Flickr\Url\FlickrAlbumUrl;
use App\Services\Flickr\Url\FlickrGalleryUrl;
use App\Services\Flickr\Url\FlickrPhotoUrl;
use App\Services\Flickr\Url\FlickrProfileUrl;
use App\Services\Flickr\Url\FlickrUrlInterface;

class UrlExtractor
{
    private const MAPPER = 'mapper';
    private const REGEX = 'regex';
    private const RESULT = 'result';

    public const DETECTOR = [
        FlickrAlbumUrl::TYPE => [
            self::REGEX => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/(\w+)\/albums\/(?:albumId\/)?(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
            self::RESULT => FlickrAlbumUrl::class,
        ],
        FlickrPhotoUrl::TYPE => [
            self::REGEX => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/(\w+)\/(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
            self::RESULT => FlickrPhotoUrl::class,
        ],
        FlickrGalleryUrl::TYPE => [
            self::REGEX => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/(\w+)\/galleries\/(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
            self::RESULT => FlickrGalleryUrl::class,
        ],
        FlickrProfileUrl::TYPE => [
            self::REGEX => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/people\/(\w+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_ID => 1,
                FlickrUrlInterface::KEY_OWNER => 1,
            ],
            self::RESULT => FlickrProfileUrl::class,
        ],
    ];

    /**
     * @param string $url
     *
     * @return \App\Services\Flickr\Url\FlickrUrlInterface|null
     */
    public function extract(string $url): ?FlickrUrlInterface
    {
        return $this->detect($url, self::DETECTOR);
    }

    /**
     * @param string $url
     * @param array $detectors
     *
     * @return \App\Services\Flickr\Url\FlickrUrlInterface|null
     */
    private function detect(string $url, array $detectors): ?FlickrUrlInterface
    {
        if (empty($url)) {
            return null;
        }

        foreach ($detectors as $type => $detector) {
            $result = $this->check($url, $detector);

            if ($result === null) {
                continue;
            }

            return $result;
        }

        return null;
    }

    /**
     * @param string $url
     * @param array $detector
     *
     * @return \App\Services\Flickr\Url\FlickrUrlInterface|null
     */
    private function check(string $url, array $detector): ?FlickrUrlInterface
    {
        if (!preg_match($detector[self::REGEX], $url, $matches)) {
            return null;
        }

        $result = [
            FlickrUrlInterface::KEY_URL => $url,
        ];

        foreach ($detector[self::MAPPER] as $key => $index) {
            $result[$key] = $matches[$index];
        }

        return new $detector[self::RESULT]($result);
    }
}
