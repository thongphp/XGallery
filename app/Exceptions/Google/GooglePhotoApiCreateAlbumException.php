<?php

namespace App\Exceptions\Google;

use Exception;
use Illuminate\Support\Facades\Log;

class GooglePhotoApiCreateAlbumException extends Exception
{
    /**
     * @param string $albumTitle
     */
    public function __construct(string $albumTitle)
    {
        $message = 'Can not create album with title: '.$albumTitle;
        Log::stack(['google'])->alert($message);

        parent::__construct($message);
    }
}
