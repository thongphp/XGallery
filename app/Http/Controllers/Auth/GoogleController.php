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
use Symfony\Component\HttpFoundation\Request;

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
            ->with(['access_type' => 'offline', 'prompt' => 'consent select_account'])
            ->redirect();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        if (!$user = Socialite::driver('Google')->user()) {
            return redirect()->route('flickr.dashboard.view')->with('danger', 'Authenticate with Google fail.');
        }

        $code = $request->get('code');

        /** @var Oauth $model */
        $model = app(Oauth::class);

        foreach ($user as $key => $value) {
            $model->setAttribute($key, $value);
        }

        $model->setAttribute('name', 'google')
            ->setAttribute('code', $code)
            ->save();

        return redirect()->route('flickr.dashboard.view')->with('success', 'Authenticate with Google Successfully.');
    }
}
