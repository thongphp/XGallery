<?php

namespace App\Services;

class UrlDetect
{
    public const DETECTOR = [
        'album' => [
            'regex' => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/flickr\/albums\/(?:albumId\/)?(\d+)/i',
            'result' => [
                1 => 'albumId',
            ],
        ],
        'faves' => [
            'regex' => '/(?:https?:\/\/)?(?:www\.)?flickr\.com\/photos\/(\w+)\/(\d+)\/in\/faves-(.*\/)/i',
            'result' => [
                2 => 'photoId',
            ],
        ],
    ];

    /**
     * @param string $url
     *
     * @return array|null
     */
    public function flickrDetect(string $url): ?array
    {
        return $this->detect($url, self::DETECTOR);
    }

    /**
     * @param string $url
     * @param array $detectors
     *
     * @return array|null
     */
    private function detect(string $url, array $detectors): ?array
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
     * @param string $url
     * @param string $type
     * @param array $detector
     *
     * @return array|null
     */
    private function check(string $url, string $type, array $detector): ?array
    {
        if (!preg_match($detector['regex'], $url, $matches)) {
            return null;
        }

        $result = [];
        foreach ($detector['result'] as $key => $name) {
            $result[$name] = $matches[$key];
        }

        return [
            'type' => $type,
            'entity' => $result,
        ];
    }
}
