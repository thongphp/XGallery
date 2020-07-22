<?php

namespace App\Exceptions\Google;

use Exception;

class GooglePhotoApiMediaCreateException extends Exception
{
    /**
     * @param string $albumId
     * @param mixed $response
     */
    public function __construct(string $albumId, $response)
    {
        $message = sprintf(
            'Can not process batch assign media in album [%s]. Response %s',
            $albumId,
            json_encode($response)
        );
        parent::__construct($message);
    }
}
