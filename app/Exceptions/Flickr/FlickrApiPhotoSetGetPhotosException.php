<?php

namespace App\Exceptions\Flickr;

use Exception;

class FlickrApiPhotoSetGetPhotosException extends Exception
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct('Can not get Photos of photoset: '.$id);
    }
}
