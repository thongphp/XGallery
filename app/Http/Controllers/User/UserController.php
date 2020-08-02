<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends BaseController
{
    use AuthorizesRequests;

    /**
     * @return Application|Factory|View
     */
    public function profile()
    {
        $user = Auth::user();

        return view(
            'user.profile',
            $this->getViewDefaultOptions(
                [
                    'title' => 'Profile',
                    'user' => $user,
                    'googleInfo' => $user->getGoogleInfo(),
                ]
            )
        );
    }

    /**
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('dashboard.dashboard.view')->with('info', 'Logout success');
    }
}
