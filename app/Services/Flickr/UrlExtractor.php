<?php

namespace App\Services\Flickr;

use App\Exceptions\Flickr\FlickrApiUrlLookupUserException;
use App\Facades\FlickrClient;
use App\Services\Flickr\Url\FlickrUrl;
use App\Services\Flickr\Url\FlickrUrlInterface;

class UrlExtractor
{
    private const MAPPER = 'mapper';
    private const REGEX = 'regex';

    private const REGEX_DOMAIN = '(?:https?:\/\/)?(?:www\.)?flickr\.com\/';

    public const DETECTOR = [
        FlickrUrlInterface::TYPE_ALBUM => [
            self::REGEX => '/' . self::REGEX_DOMAIN . 'photos\/(\d+@[\w-]{3}|\w+[.-]?\w+)\/albums\/(?:albumId\/)?(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
        ],
        FlickrUrlInterface::TYPE_PHOTO => [
            self::REGEX => '/' . self::REGEX_DOMAIN . 'photos\/(\d+@[\w-]{3}|\w+[.-]?\w+)\/(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
        ],
        FlickrUrlInterface::TYPE_GALLERY => [
            self::REGEX => '/' . self::REGEX_DOMAIN . 'photos\/(\d+@[\w-]{3}|\w+[.-]?\w+)\/galleries\/(\d+)/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_OWNER => 1,
                FlickrUrlInterface::KEY_ID => 2,
            ],
        ],
        FlickrUrlInterface::TYPE_PROFILE => [
            self::REGEX => '/' . self::REGEX_DOMAIN . '\w+[.-]?\w+\/(\d+@[\w-]{3}|\w+([.-]?\w+))/i',
            self::MAPPER => [
                FlickrUrlInterface::KEY_ID => 1,
                FlickrUrlInterface::KEY_OWNER => 1,
            ],
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
     * @param  string  $url
     * @param  array  $detectors
     *
     * @return FlickrUrlInterface|null
     */
    private function detect(string $url, array $detectors): ?FlickrUrlInterface
    {
        if (empty($url)) {
            return null;
        }

        foreach ($detectors as $type => $detector) {
            if (!$result = $this->check($url, $type, $detector)) {
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
     * @return FlickrUrlInterface|null
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

        $owner = $this->getNsId($result[FlickrUrlInterface::KEY_OWNER], $url);

        if (null === $owner) {
            return null;
        }

        $result[FlickrUrlInterface::KEY_OWNER] = $owner;

        return new FlickrUrl($result);
    }

    /**
     * @param string $owner
     * @param string $url
     *
     * @return string|null
     */
    private function getNsId(string $owner, string $url): ?string
    {
        if (strpos($owner, '@') !== false) {
            return $owner;
        }

        try {
            $result = FlickrClient::lookUpUser($url);

            return $result->user->id;
        } catch (FlickrApiUrlLookupUserException $e) {
            return null;
        }
    }
}
