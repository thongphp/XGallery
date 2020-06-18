<?php

namespace App\Services;

use App\Oauth\OauthClient;
use Illuminate\Support\Facades\Log;

class Flickr extends OauthClient
{
    public const REST_ENDPOINT = 'https://api.flickr.com/services/rest';

    /**
     * @param  string  $method
     * @param  array  $parameters
     * @return object|null
     */
    public function get(string $method, array $parameters = []): ?object
    {
        $content = $this->request(
            'GET',
            static::REST_ENDPOINT,
            [
                'query' => array_merge(
                    ['method' => 'flickr.'.$method],
                    $this->getDefaultFlickrParameters(),
                    $parameters
                )
            ]
        );

        if (!$content) {
            Log::stack(['oauth'])->warning('Request responded with no content');
            return null;
        }

        if ($content->stat !== 'ok') {
            Log::stack(['oauth'])->warning('Flickr request failed');
            return null;
        }

        return $content;
    }

    /**
     * Default parameters for all requests
     *
     * @return array
     */
    private function getDefaultFlickrParameters(): array
    {
        return ['format' => 'json', 'nojsoncallback' => 1, 'api_key' => config('auth.flickr.token')];
    }
}
