<?php

namespace App\Http\Controllers\Auth;

use App\Facades\UserActivity;
use App\Facades\UserRole;
use App\Models\Oauth;
use App\Models\User as UserModel;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthenticateController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected string $redirectTo = RouteServiceProvider::HOME;

    protected array $with = [];
    protected string $drive = '';
    protected array $scopes = [];

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return RedirectResponse
     */
    public function oauth(): RedirectResponse
    {
        return Socialite::driver($this->drive)
            ->scopes($this->scopes)
            ->with($this->with)
            ->redirect();
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        if (!$socialiteUser = Socialite::driver($this->drive)->user()) {
            return redirect()
                ->route('dashboard.dashboard.view')
                ->with('danger', 'Authenticate with '.ucfirst($this->drive).' fail.');
        }

        $oauth = $this->processOAuthData($socialiteUser, $request);
        $this->notify($socialiteUser, $oauth);

        UserRole::checkAndAssignRole(Auth::user());

        return redirect()
            ->route('user.profile.view')
            ->with('success', 'Authenticated with '.ucfirst($this->drive).' successfully.');
    }

    /**
     * @SuppressWarnings("unused")
     *
     * @param User $socialiteUser
     * @param Request $request
     *
     * @return Oauth
     */
    protected function processOAuthData(
        User $socialiteUser,
        Request $request
    ): Oauth {
        $oauth = Oauth::updateOrCreate(
            [
                Oauth::USER_ID => Auth::user()->id,
                Oauth::SERVICE => strtolower($this->drive),
            ]
        );

        $oauth->{Oauth::SERVICE} = strtolower($this->drive);
        $oauth->{Oauth::CREDENTIAL} = $socialiteUser;
        $oauth->save();

        return $oauth;
    }

    /**
     * @param User $socialiteUser
     * @param Oauth $oauth
     */
    protected function notify(User $socialiteUser, Oauth $oauth): void
    {
        $user = Auth::user();

        UserActivity::notify(
            '%s has %s '.$this->drive,
            $user,
            'authorize',
            [
                \App\Models\Core\UserActivity::OBJECT_ID => $oauth->{Oauth::ID},
                \App\Models\Core\UserActivity::OBJECT_TABLE => $oauth->getTable(),
                \App\Models\Core\UserActivity::EXTRA => [
                    'title' => $user->{UserModel::NAME},
                    'fields' => [
                        'OAuthId' => $oauth->{Oauth::ID},
                        'Email' => $socialiteUser->getEmail(),
                    ],
                ],
            ]
        );
    }
}
