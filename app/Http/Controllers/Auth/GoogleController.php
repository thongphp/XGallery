<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Auth;

use App\Models\Oauth;
use App\Models\User;
use Google_Service_PhotosLibrary;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;
use function GuzzleHttp\Psr7\str;

/**
 * Class GoogleController
 * @package App\Http\Controllers\Auth
 */
class GoogleController extends AuthenticateController
{
    protected array $with = ['access_type' => 'offline', 'prompt' => 'consent select_account'];
    protected string $drive = 'google';
    protected array $scopes = ['https://www.googleapis.com/auth/drive', Google_Service_PhotosLibrary::PHOTOSLIBRARY];

    /**
     * @inheritDoc
     */
    protected function processOAuthData(
        \Laravel\Socialite\Contracts\User $socialiteUser,
        Request $request
    ): Oauth {
        $user = User::firstOrCreate(
            [
                User::NAME => $socialiteUser->getName(),
                User::EMAIL => $socialiteUser->getEmail(),
                User::AVATAR => $socialiteUser->getAvatar(),
            ]
        );

        Auth::login($user, true);

        $oauth = Oauth::firstOrCreate(
            [
                Oauth::USER_ID => $user->{User::ID},
                Oauth::SERVICE => strtolower($this->drive),
            ]
        );

        $oauth->{Oauth::SERVICE} = strtolower($this->drive);
        $oauth->{Oauth::CREDENTIAL} = $socialiteUser;
        $oauth->{Oauth::CREDENTIAL}->code = $request->get('code');
        $oauth->save();

        return $oauth;
    }
}
