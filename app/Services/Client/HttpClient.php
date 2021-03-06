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

    /**
     * @SuppressWarnings("PHPMD.NPathComplexity")
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     *
     * @param  string  $url
     * @param  string  $saveTo
     * @param  array  $options
     *
     * @return false|string // False if not succeed and related path if succeed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function download(string $url, string $saveTo, array $options = [])
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        // Check file local already exists
        $fileName = basename($url);
        $saveToFile = $saveTo.DIRECTORY_SEPARATOR.$fileName;

        if (Storage::exists($saveToFile)) {
            return $saveToFile;
        }

        $hasResources = isset($options['sink']);
        $resource = $hasResources ? $options['sink'] : fopen('php://temp', 'r+');
        $extraOptions = $hasResources ? [] : ['sink' => $resource];

        $response = $this->client->request(
            'GET',
            $url,
            // @TODO Implement curl options to config
            array_merge($extraOptions, [
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

        if ($hasResources) {
            return true;
        }

        if (!Storage::exists($saveTo)) {
            Storage::makeDirectory($saveTo);
        }

        rewind($resource);
        file_put_contents(Storage::path($saveToFile), stream_get_contents($resource));
        fclose($resource);

        return $saveToFile;
    }
}
