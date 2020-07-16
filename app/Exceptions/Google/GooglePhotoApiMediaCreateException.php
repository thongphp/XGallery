<?php

namespace App\Exceptions\Google;

use Exception;

class GooglePhotoApiMediaCreateException extends Exception
{
    /**
     * @param  string  $uploadToken
     * @param  string  $albumId
     */
    public function __construct(string $uploadToken, string $albumId, $response)
    {
        $message = sprintf(
            'Can not create media with token [%s] in album [%s]. Response %s',
            $uploadToken,
            $albumId,
            json_encode($response)
        );
        parent::__construct($message);
    }
}
