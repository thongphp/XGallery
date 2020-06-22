<?php

namespace App\Exceptions\Flickr;

use Exception;

class FlickrApiPeopleGetInfoException extends Exception
{
    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct('Can not get user information for: '.$id);
    }
}
