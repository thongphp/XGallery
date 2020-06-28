<?php

namespace App\Exceptions\Flickr;

use Exception;
use Illuminate\Support\Facades\Log;

class FlickrApiUrlLookupUserException extends Exception
{
    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $message = 'Can not lookup user from URL: ' . $url;
        Log::stack(['flickr'])->alert($message);

        parent::__construct($message);
    }
}
