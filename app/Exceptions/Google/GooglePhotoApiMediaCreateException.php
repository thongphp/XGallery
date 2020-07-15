<?php

namespace App\Exceptions\Google;

use Exception;
use Illuminate\Support\Facades\Storage;

class GooglePhotoApiMediaCreateException extends Exception
{
    /**
     * @param  string  $uploadToken
     * @param  string  $albumId
     * @param  mixed  $response
     * @param  string  $file
     */
    public function __construct(string $uploadToken, string $albumId, $response, string $file)
    {
        Storage::delete($file);

        $message = sprintf(
            'Can not create media with token [%s] in album [%s]. Response %s',
            $uploadToken,
            $albumId,
            json_encode($response)
        );
        parent::__construct($message);
    }
}
