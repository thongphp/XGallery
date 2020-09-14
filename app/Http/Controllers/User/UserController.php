<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Models\Core\UserActivity;
use App\Repositories\UserActivitiesRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function activities(Request $request)
    {
        $repository = app(UserActivitiesRepository::class);
        $filters = $request->all();
        $filters[UserActivity::ACTOR_ID] = Auth::id();
        $items = $repository->getItems($filters);

        return view(
            'user.activities',
            [
                'title' => 'User Activities',
                'sidebar' => $this->getMenuItems(),
                'actor' => Auth::user(),
                'activities' => $items
            ]
        );
    }
}
