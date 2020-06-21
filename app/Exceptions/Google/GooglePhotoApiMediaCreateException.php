<?php

namespace App\Exceptions\Google;

use Exception;
use Illuminate\Support\Facades\Log;

class GooglePhotoApiMediaCreateException extends Exception
{
    /**
     * @param string $uploadToken
     * @param string $albumId
     */
    public function __construct(string $uploadToken, string $albumId)
    {
        $message = sprintf('Can not create media with token [%s] in album [%s]', $uploadToken, $albumId);
        Log::stack(['google'])->alert($message);

        parent::__construct($message);
    }
}
