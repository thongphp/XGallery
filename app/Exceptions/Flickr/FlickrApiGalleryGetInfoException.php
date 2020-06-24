<?php

namespace App\Exceptions\Flickr;

use Exception;

class FlickrApiGalleryGetInfoException extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct('Can not gallery information: '.$id);
    }
}
