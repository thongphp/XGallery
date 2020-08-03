<?php

namespace App\Http\Controllers\Auth;

use App\Models\Oauth;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
    public function oauth()
    {
        return Socialite::driver($this->drive)
            ->scopes($this->scopes)
            ->with($this->with)
            ->redirect();
    }

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        if (!$oauthUser = Socialite::driver($this->drive)->user()) {
            return redirect()
                ->route('dashboard.dashboard.view')
                ->with('danger', 'Authenticate with '.ucfirst($this->drive).' fail.');
        }

        if ($this->drive === 'google') {
            $user = User::firstOrCreate(['name' => $oauthUser->getName(), 'email' => $oauthUser->getEmail()]);
            Auth::login($user, true);
        } else {
            $user = Auth::user();
        }

        $oauth = Oauth::updateOrCreate(['user_id' => $user->id, 'service' => strtolower($this->drive)]);
        $oauth->credential = $oauthUser;
        $oauth->credential->code = $request->get('code');
        $oauth->service = strtolower($this->drive);
        $oauth->save();

        return redirect()
            ->route('user.profile.view')
            ->with('success', 'Authenticated with '.ucfirst($this->drive).' successfully.');
    }
}
