<?php

namespace App\Exceptions\Flickr;

use Exception;

class FlickrApiAuthorizedUserGetFavouritePhotosException extends Exception
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct('Can not get Favourite Photos of Contact: '.$id);
    }
}
