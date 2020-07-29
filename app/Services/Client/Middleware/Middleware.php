<?php

namespace App\Services\Client\Middleware;

use Campo\UserAgent;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Middleware
{
    public static function prepareBody()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request = $request->withHeader('Accept-Encoding', 'gzip, deflate');
                $request = $request->withHeader('User-Agent', UserAgent::random(['device_type' => ['Desktop']]));

                /**
                 * @var PromiseInterface $promise
                 */
                $promise = $handler($request, $options);

                return $promise->then(
                    function (ResponseInterface $response) {
                        return $response;
                    }
                );
            };
        };
    }
}
