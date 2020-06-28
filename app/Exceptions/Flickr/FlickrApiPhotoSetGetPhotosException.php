<?php

namespace App\Exceptions\Flickr;

use Exception;
use Illuminate\Support\Facades\Log;

class FlickrApiPhotoSetGetPhotosException extends Exception
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $msg = 'Can not get Photos of photoset: '.$id;
        Log::stack(['slack', 'flickr'])->alert($msg);

        parent::__construct($msg);
    }
}
