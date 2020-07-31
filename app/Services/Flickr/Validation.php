<?php

namespace App\Services\Flickr;

/**
 * Class FlickrValidate
 * @package App\Services
 */
class Validation
{
    /**
     * @param string $nsid
     *
     * @return bool
     */
    public function validateNsId(string $nsid): bool
    {
        return strpos($nsid, '@') !== false;
    }
}
