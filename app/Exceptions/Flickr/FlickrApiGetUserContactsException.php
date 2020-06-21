<?php

namespace App\Exceptions\Flickr;

use Exception;
use Illuminate\Support\Facades\Log;

class FlickrApiGetUserContactsException extends Exception
{
    public function __construct(int $page = 1)
    {
        $message = 'Can not get contact list of current authorized user. Page: ' . $page;
        Log::stack(['flickr'])->alert($message);

        parent::__construct($message);
    }
}
