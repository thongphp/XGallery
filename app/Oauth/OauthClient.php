<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Oauth;

use App\Exceptions\OAuthClientException;
use App\Repositories\OAuthRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;

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
     *
     * @return mixed|string|null
     * @throws GuzzleException|OAuthClientException
     */
    public function request(string $method, string $uri, array $parameters = [])
    {
        $key = md5(serialize([$method, $uri, $parameters]));
        $isCached = Cache::has($key);

        if ($isCached) {
            return Cache::get($key);
        }

        if (!$client = $this->getClient()) {
            return null;
        }

        $response = $client->request($method, $uri, $parameters);

        if ($response->getStatusCode() !== 200) {
            throw new OAuthClientException((string) $response->getBody(), $response->getStatusCode());
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
        if (!$client = app(OAuthRepository::class)->findBy(['name' => 'flickr'])) {
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
