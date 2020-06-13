<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\Oauth;
use Google_Service_PhotosLibrary;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class GoogleController
 * @package App\Http\Controllers\Auth
 */
class GoogleController extends BaseController
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return RedirectResponse
     */
    public function login()
    {
        return Socialite::driver('Google')
            ->scopes(['https://www.googleapis.com/auth/drive', Google_Service_PhotosLibrary::PHOTOSLIBRARY])
            ->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     */
    public function callback()
    {
        if (!$user = Socialite::driver('Google')->user()) {
            return;
        }

        /** @var Oauth $model */
        $model = app(Oauth::class);

        foreach ($user as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $model->setAttribute('name', 'google');

        $model->save();
    }
}
