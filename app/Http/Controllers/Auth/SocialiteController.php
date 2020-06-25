<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\Oauth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SocialiteController extends BaseController
{
    protected array $with = [];
    protected string $drive = '';
    protected array $scopes = [];

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return RedirectResponse
     */
    public function login()
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
        $model = app(Oauth::class);

        foreach ($user as $key => $value) {
            if ($key === 'accessTokenResponseBody') {
                continue;
            }
            $model->setAttribute($key, $value);
        }

        $model->setAttribute('name', strtolower($this->drive))
            ->setAttribute('code', $code)
            ->save();

        return redirect()
            ->route('dashboard.dashboard.view')
            ->with('success', 'Authenticated with '.ucfirst($this->drive).' successfully.');
    }
}
