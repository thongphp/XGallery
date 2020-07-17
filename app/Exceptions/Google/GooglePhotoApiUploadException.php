<?php

namespace App\Exceptions\Google;

use Exception;
use Illuminate\Support\Facades\Storage;

class GooglePhotoApiUploadException extends Exception
{
    /**
     * @param  string  $file
     * @param $response
     */
    public function __construct(string $file, $response)
    {
        Storage::delete($file);
        $message = 'Can not add uploading media. File '.$file. '. Response ' . json_encode($response);
        parent::__construct($message);
    }
}
