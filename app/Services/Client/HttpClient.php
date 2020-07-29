<?php

namespace App\Services\Client;

use App\Services\Client\Middleware\Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpClient
{
    /**
     * @var Client
     */
    private Client $client;
    private ResponseInterface $response;

    public function __construct(array $options = [], array $middlewares = [])
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::prepareBody(), 'prepare_body');

        foreach ($middlewares as $middleware) {
            $stack->push($middleware);
        }

        $this->client = new Client(array_merge(['handler' => $stack], $options, config('services.httpclient', [])));
    }

    public function request($method, $uri = '', array $options = [])
    {
        $useCache = false;

        if (strtoupper($method) === Request::METHOD_POST) {
            $useCache = true;
        }

        $key = md5(serialize([$method, $uri]));
        $isCached = Cache::has($key);

        if ($useCache && $isCached) {
            return Cache::get($key);
        }

        $this->response = $this->client->request($method, $uri, $options);

        switch ($this->response->getStatusCode()) {
            case Response::HTTP_OK:
                $content = $this->response->getBody()->getContents();
                $contentType = $this->response->getHeader('Content-Type');
                $contentType = $contentType ? $contentType[0] : null;

                if ($contentType && $contentType === 'application/json') {
                    $content = json_decode($content);
                }
                Cache::put($key, $content, 1800);
                break;
            default:
                return null;
        }

        return Cache::get($key);
    }

    public function download(string $url, string $saveTo, array $options = [])
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $resource = fopen('php://temp', 'r+');
        $response = $this->client->request(
            'GET',
            $url,
            array_merge(['sink' => $resource], [
                'curl' => [
                    CURLOPT_NOBODY => false,
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]
            ], $options)
        );

        if ($response->getStatusCode() !== Response::HTTP_OK
            && $response->getStatusCode() < Response::HTTP_MULTIPLE_CHOICES
            && $response->getStatusCode() > Response::HTTP_PERMANENTLY_REDIRECT
        ) {
            return false;
        }

        $realFileSize = $response->getHeader('Content-Length');
        $realFileSize = $realFileSize ? (int) $realFileSize[0] : null;

        if ($realFileSize !== null && (fstat($resource)['size'] !== $realFileSize)) {
            return false;
        }

        if (!Storage::exists($saveTo)) {
            Storage::makeDirectory($saveTo);
        }

        $fileName = basename($url);
        $saveToFile = Storage::path($saveTo.DIRECTORY_SEPARATOR.$fileName);
        rewind($resource);

        file_put_contents($saveToFile, stream_get_contents($resource));
        fclose($resource);

        return $saveToFile;
    }
}
