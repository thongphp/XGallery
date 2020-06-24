<?php

namespace App\Exceptions\Flickr;

use Exception;

class FlickrApiPhotoSetsGetInfoException extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct('Can not photosets information: '.$id);
    }
}
