<?php

namespace App\Oauth;

use App\Models\Oauth;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;
use kamermans\OAuth2\GrantType\NullGrantType;
use kamermans\OAuth2\OAuth2Middleware;

class GoogleOauthClient extends OauthClient
{
    /**
     * @return Client|null
     */
    protected function getClient(): ?Client
    {
        if (!$client = Oauth::where(['name' => 'google'])->get()->first()) {
            Log::stack(['oauth'])->warning('Google Oauth not found');
            return null;
        }

        $stack = HandlerStack::create();
        $middleware = new OAuth2Middleware(new NullGrantType());
        $middleware->setAccessToken($client->token);
        $stack->push($middleware);

        return new Client(['handler' => $stack, 'auth' => 'oauth']);
    }
}
