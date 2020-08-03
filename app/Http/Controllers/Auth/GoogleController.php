<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Auth;

use Google_Service_PhotosLibrary;

/**
 * Class GoogleController
 * @package App\Http\Controllers\Auth
 */
class GoogleController extends AuthenticateController
{
    protected array $with = ['access_type' => 'offline', 'prompt' => 'consent select_account'];
    protected string $drive = 'google';
    protected array $scopes = ['https://www.googleapis.com/auth/drive', Google_Service_PhotosLibrary::PHOTOSLIBRARY];
}
