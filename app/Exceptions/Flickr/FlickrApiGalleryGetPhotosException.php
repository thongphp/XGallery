<?php

namespace App\Exceptions\Flickr;

use Exception;

class FlickrApiGalleryGetPhotosException extends Exception
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct('Can not get Photos of Gallery: ' . $id);
    }
}
