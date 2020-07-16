<?php

namespace App\Exceptions\Google;

use Exception;

class GooglePhotoApiUploadException extends Exception
{
    /**
     * @param  string  $file
     * @param $response
     */
    public function __construct(string $file, $response)
    {
        $message = 'Can not add uploading media. File '.$file. '. Response ' . json_encode($response);
        parent::__construct($message);
    }
}
