<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Auth;

/**
 * Class FlickrController
 * @package App\Http\Controllers\Auth
 */
class FlickrController extends AuthenticateController
{
    protected array $with = ['perms' => 'read, write, delete'];
    protected string $drive = 'flickr';
    protected array $scopes = [];
}
