<?php

namespace App\Exceptions\Google;

use Exception;

class GooglePhotoApiMediaCreateException extends Exception
{
    /**
     * @param  string  $uploadToken
     * @param  string  $albumId
     */
    public function __construct(string $uploadToken, string $albumId)
    {
        $message = sprintf('Can not create media with token [%s] in album [%s]', $uploadToken, $albumId);
        parent::__construct($message);
    }
}
