<?php

namespace App\Exceptions\Google;

use Exception;
use Illuminate\Support\Facades\Log;

class GooglePhotoApiUploadException extends Exception
{
    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $message = 'Can not add uploading media. File: '.$file;
        Log::stack(['google'])->alert($message);

        parent::__construct($message);
    }
}
