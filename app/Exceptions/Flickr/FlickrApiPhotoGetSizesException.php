<?php

namespace App\Exceptions\Flickr;

use Exception;

class FlickrApiPhotoGetSizesException extends Exception
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct('Can not get sizes of photo: '.$id);
    }
}
