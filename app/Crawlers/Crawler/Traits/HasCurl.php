<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

trait HasCurl
{
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
            Log::error('Can not get download data', func_get_args());
            return false;
        }

        $status = curl_getinfo($ch);
        curl_close($ch);

        if ($status['http_code'] != Response::HTTP_OK
            && $status['http_code'] < Response::HTTP_MULTIPLE_CHOICES
            && $status['http_code'] > Response::HTTP_PERMANENTLY_REDIRECT) {
            Log::error('Invalid response', [func_get_args(), $status]);
            return false;
        }

        if (!Storage::put($saveToFile, $data)) {
            Log::error('Can not save to file', func_get_args());
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
}
