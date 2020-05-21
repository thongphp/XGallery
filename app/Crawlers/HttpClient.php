<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers;

use App\Crawlers\Crawler\Traits\HasCurl;
use App\Crawlers\Traits\HasHeaders;
use App\Events\HttpResponded;
use App\Events\OrderShipped;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HttpClient
 * @package App\Crawlers
 */
class HttpClient extends Client
{
    use HasCurl;
    use HasHeaders;

    protected ResponseInterface $response;

    private ?int  $interval;
    private array $errors = [];

    /**
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $options
     * @return string|null
     */
    public function request($method, $uri = '', array $options = []): ?string
    {
        $key = $this->getKey([$method, $uri]);
        $isCached = Cache::has($key);
        Log::stack(['http'])
            ->info(
                $isCached
                    ? 'Request URI: '.urldecode($uri).' with CACHED key '.$key
                    : 'Request URI: '.urldecode($uri)
            );

        if ($isCached) {
            return Cache::get($key);
        }

        try {
            $this->response = parent::request($method, $uri, array_merge($options, ['headers' => $this->getHeaders()]));
        } catch (Exception $exception) {
            Log::stack(['http'])->error($exception->getMessage());
            $this->errors[$uri] = $exception->getMessage();
            return null;
        }

        switch ($this->response->getStatusCode()) {
            case Response::HTTP_OK:
                Cache::put($key, $this->response->getBody()->getContents(), 3600); // 1 hour
                break;
            default:
                Log::stack(['http'])->error($this->response->getStatusCode());
                return null;
        }

        return Cache::get($key);
    }

    /**
     * @param  array  $args
     * @return string
     */
    protected function getKey(array $args): string
    {
        return md5(serialize($args));
    }

    /**
     * @param  string  $url
     * @param  string  $saveTo
     * @return bool|string
     */
    public function download(string $url, string $saveTo)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        if (!Storage::exists($saveTo)) {
            Storage::makeDirectory($saveTo);
        }

        $fileName = basename($url);
        $saveToFile = $saveTo.DIRECTORY_SEPARATOR.$fileName;

        if (Storage::exists($saveToFile)) {
            /**
             * @TODO Verify local file
             */
            return $saveToFile;
        }

        return $this->downloadRemoteFile($url, $saveToFile);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
