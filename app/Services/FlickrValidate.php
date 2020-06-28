<?php

namespace App\Services;

/**
 * Class FlickrValidate
 * @package App\Services
 */
class FlickrValidate
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
