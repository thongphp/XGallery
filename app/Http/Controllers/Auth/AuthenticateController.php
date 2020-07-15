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
    protected $redirectTo = RouteServiceProvider::HOME;

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
        if (!$user = Socialite::driver($this->drive)->user()) {
            return redirect()
                ->route('dashboard.dashboard.view')
                ->with('danger', 'Authenticate with '.ucfirst($this->drive).' fail.');
        }

        $code = $request->get('code');
        $oauth = Oauth::updateOrCreate(['id' => $user->getId()]);

        foreach ($user as $key => $value) {
            if ($key === 'accessTokenResponseBody') {
                continue;
            }
            $oauth->setAttribute($key, $value);
        }

        $oauth->setAttribute('name', strtolower($this->drive))
            ->setAttribute('code', $code)
            ->save();

        $user = User::updateOrCreate([
            'oauth_id' => $oauth->id, 'name' => $oauth->user['name'], 'email' => $oauth->user['email']
        ]);

        Auth::login($user, true);

        return redirect()
            ->route('dashboard.dashboard.view')
            ->with('success', 'Authenticated with '.ucfirst($this->drive).' successfully.');
    }
}
