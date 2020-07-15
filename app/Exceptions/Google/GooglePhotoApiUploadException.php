<?php

namespace App\Exceptions\Google;

use Exception;

class GooglePhotoApiUploadException extends Exception
{
    /**
     * @param  string  $file
     */
    public function __construct(string $file)
    {
        $message = 'Can not add uploading media. File: '.$file;
        parent::__construct($message);
    }
}
