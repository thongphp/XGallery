<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers;

use Campo\UserAgent;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HttpClient
 * @package App\Crawlers
 */
class HttpClient extends Client
{
    protected ResponseInterface $response;

    public function __construct(array $config = [])
    {
        $config['headers'] = [
            'Accept-Encoding' => 'gzip, deflate',
            'User-Agent' => UserAgent::random(['device_type' => ['Desktop']]),
        ];
        parent::__construct($config);
    }

    /**
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $options
     * @return string|null
     * @throws
     */
    public function request($method, $uri = '', array $options = []): ?string
    {
        $key = md5(serialize([$method, $uri]));
        $isCached = Cache::has($key);

        if ($isCached) {
            return Cache::get($key);
        }

        $this->response = parent::request($method, $uri, $options);

        switch ($this->response->getStatusCode()) {
            case Response::HTTP_OK:
                Cache::put($key, $this->response->getBody()->getContents(), 1800);
                break;
            default:
                return null;
        }

        return Cache::get($key);
    }

    /**
     * @param  string  $url
     * @param  string  $saveTo
     * @return bool|string
     * @throws Exception
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
     * @param  string  $url
     * @param  string  $saveToFile
     * @return bool|string
     * @throws Exception
     */
    protected function downloadRemoteFile(string $url, string $saveToFile)
    {
        $ch = curl_init($url);

        // Issue a HEAD request and follow any redirects.
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (!$data = curl_exec($ch)) {
            throw new Exception(curl_error($ch));
        }

        $status = curl_getinfo($ch);
        curl_close($ch);

        if ($status['http_code'] != Response::HTTP_OK
            && $status['http_code'] < Response::HTTP_MULTIPLE_CHOICES
            && $status['http_code'] > Response::HTTP_PERMANENTLY_REDIRECT) {
            throw new Exception('Unexpected response '.$status['http_code']);
        }

        if (!Storage::put($saveToFile, $data)) {
            throw new Exception('Can not save '.$url.' to '.$saveToFile);
        }

        if ((int) $status['download_content_length'] < 0
            || (int) $status['download_content_length'] === Storage::size($saveToFile)
        ) {
            return $saveToFile;
        }

        Storage::delete($saveToFile);

        return false;
    }
}
