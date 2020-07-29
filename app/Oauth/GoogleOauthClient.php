<?php

namespace App\Oauth;

use App\Exceptions\OAuthClientException;
use App\Models\Oauth;
use App\Repositories\OAuthRepository;
use DateInterval;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use JsonException;
use kamermans\OAuth2\GrantType\NullGrantType;
use kamermans\OAuth2\OAuth2Middleware;

/**
 * Used to communicate with Google
 * @package App\Oauth
 */
class GoogleOauthClient
{
    /**
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $parameters
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
        $repository = app(OAuthRepository::class);

        if (!$oauth = $repository->findBy(['name' => 'google'])) {
            Log::stack(['oauth'])->warning('Google Oauth not found');

            return null;
        }

        if (!$accessToken = $this->getAccessToken($oauth)) {
            return null;
        }

        $stack = HandlerStack::create();
        $middleware = new OAuth2Middleware(new NullGrantType());
        $middleware->setAccessToken($accessToken);
        $stack->push($middleware);

        return new Client(['handler' => $stack, 'auth' => 'oauth']);
    }

    /**
     * @param  Oauth  $oauth
     *
     * @return string|null
     */
    private function getAccessToken(Oauth $oauth): ?string
    {
        try {
            $expiredDate = new DateTime($oauth->getAttributeValue($oauth->getUpdatedAtColumn()));
            $expiredDate->add(new DateInterval('PT'.$oauth->getAttributeValue('expiresIn').'S'));
            $current = new DateTime('now');
        } catch (Exception $exception) {
            Log::stack(['oauth'])->warning(
                'Google Oauth can not populate the expires date at '.$oauth->getAttributeValue('_id')
            );

            return null;
        }

        if ($current < $expiredDate) {
            return $oauth->getAttributeValue('token');
        }

        $response = (new Client())->post('https://oauth2.googleapis.com/token', [
            'headers' => ['Content-Type: application/x-www-form-urlencoded'],
            'form_params' => [
                'client_id' => config('auth.google.client_id'),
                'client_secret' => config('auth.google.client_secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $oauth->getAttributeValue('refreshToken'),
            ],
        ]);

        try {
            $response = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            Log::stack(['oauth'])->warning(
                'Google Oauth can not decode JSON from RefreshToken API '.$oauth->getAttributeValue('_id')
            );

            return null;
        }

        $oauth->setAttribute('token', $response['access_token']);
        $oauth->setAttribute('expiresIn', $response['expires_in']);
        $oauth->setAttribute('scope', $response['scope']);
        $oauth->setAttribute('tokenType', $response['token_type']);
        $oauth->setAttribute('idToken', $response['id_token']);
        $oauth->save();

        return $oauth->getAttributeValue('token');
    }
}
