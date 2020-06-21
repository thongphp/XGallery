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
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FlickrController
 * @package App\Http\Controllers\Auth
 */
class FlickrController extends BaseController
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return RedirectResponse
     */
    public function login()
    {
        return Socialite::driver('flickr')->with(['perms' => 'read, write, delete'])->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     */
    public function callback(): RedirectResponse
    {
        if (!$user = Socialite::driver('flickr')->user()) {
            return redirect()->route('flickr.dashboard.view')->with('danger', 'Can not authenticate with Flickr');
        }

        $model = app(Oauth::class);
        $model->name = 'flickr';

        foreach ($user->accessTokenResponseBody as $key => $value) {
            $model->{$key} = $value;
        }

        $model->save();

        return redirect()->route('flickr.dashboard.view')->with('success', 'Authenticate with Flickr Successfully.');
    }
}
