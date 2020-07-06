<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers;

use App\Notifications\NotificationToSlack;
use App\Traits\Notifications\HasSlackNotification;
use Campo\UserAgent;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notifiable;
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
    use Notifiable, HasSlackNotification;

    protected ResponseInterface $response;

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

        try {
            $this->response = parent::request($method, $uri, array_merge($options, ['headers' => $this->getHeaders()]));
        } catch (GuzzleException $exception) {
            $this->notify(new NotificationToSlack($exception->getMessage()));
            return null;
        }

        switch ($this->response->getStatusCode()) {
            case Response::HTTP_OK:
                Cache::put($key, $this->response->getBody()->getContents(), 1800);
                break;
            default:
                Log::stack(['http'])->error($this->response->getStatusCode(), func_get_args());
                return null;
        }

        return Cache::get($key);
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
     * @param  string  $url
     * @param  string  $saveToFile
     * @return bool|string
     */
    protected function downloadRemoteFile(string $url, string $saveToFile)
    {
        $ch = curl_init($url);

        // Issue a HEAD request and follow any redirects.
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (!$data = curl_exec($ch)) {
            $this->notify(new NotificationToSlack('Can not get download data'));
            return false;
        }

        $status = curl_getinfo($ch);
        curl_close($ch);

        if ($status['http_code'] != Response::HTTP_OK
            && $status['http_code'] < Response::HTTP_MULTIPLE_CHOICES
            && $status['http_code'] > Response::HTTP_PERMANENTLY_REDIRECT) {
            Log::stack(['http'])->warning('Invalid response', [func_get_args(), $status]);
            return false;
        }

        if (!Storage::put($saveToFile, $data)) {
            Log::stack(['http'])->warning('Can not save to file', func_get_args());
            return false;
        }

        if ((int) $status['download_content_length'] < 0
            || (int) $status['download_content_length'] === Storage::size($saveToFile)
        ) {
            return $saveToFile;
        }

        Storage::delete($saveToFile);

        return false;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getHeaders(): array
    {
        return [
            'Accept-Encoding' => 'gzip, deflate',
            'User-Agent' => UserAgent::random([
                'device_type' => ['Desktop'],
            ]),
        ];
    }
}
