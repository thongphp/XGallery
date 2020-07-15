<?php

namespace App\Exceptions\Google;

use Exception;

class GooglePhotoApiAlbumCreateException extends Exception
{
    /**
     * @param  string  $albumTitle
     */
    public function __construct(string $albumTitle)
    {
        $message = 'Can not create album with title: '.$albumTitle;
        parent::__construct($message);
    }
}
