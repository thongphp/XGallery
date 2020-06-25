<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Oauth;

use App\Models\Oauth;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class OauthClient
 * @package App\OAuth
 */
class OauthClient
{
    /**
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param bool $force
     *
     * @return string|object
     */
    public function request(string $method, string $uri, array $parameters = [], bool $force = false)
    {
        $key = md5(serialize([$method, $uri, $parameters]));
        $isCached = Cache::has($key);

        if ($force) {
            Cache::delete($key);
            $isCached = false;
        }

        Log::stack(['oauth'])->info(
            $isCached ? 'Requesting '.$uri.' with CACHE'
                : 'Requesting '.$uri,
            [$method, $uri, $parameters]
        );

        if ($isCached) {
            return Cache::get($key);
        }

        if (!$client = $this->getClient()) {
            return null;
        }

        try {
            $response = $client->request($method, $uri, $parameters);
        } catch (Exception $exception) {
            Log::stack(['oauth'])->error($exception->getMessage());
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            Log::stack(['oauth'])->warning('Status code '.$response->getStatusCode());
            return null;
        }

        /**
         * @TODO Support decode content via event
         */
        $header = $response->getHeader('Content-Type')[0] ?? '';
        $content = (string) $response->getBody();

        if (strpos($header, 'application/json') === false) {
            return $content;
        }

        $content = json_decode($content);

        Cache::put($key, $content, 86400); // Day
        return Cache::get($key);
    }

    /**
     * @return Client|null
     */
    protected function getClient(): ?Client
    {
        if (!$client = Oauth::where(['name' => 'flickr'])->get()->first()) {
            Log::stack(['oauth'])->warning('Flickr Oauth not found');
            return null;
        }

        $stack = HandlerStack::create();
        $middleware = new Oauth1([
            'consumer_key' => config('auth.flickr.token'),
            'consumer_secret' => config('auth.flickr.token_secret'),
            'token' => $client->token,
            'token_secret' => $client->tokenSecret,
        ]);
        $stack->push($middleware);

        return new Client(['handler' => $stack, 'auth' => 'oauth']);
    }
}
