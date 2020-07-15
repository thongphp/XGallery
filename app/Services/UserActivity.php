<?php

namespace App\Services;

use App\Models\Core\UserActivityModel;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserActivity
 * @package App\Services
 */
class UserActivity
{
    public function notify(string $text, string $action, array $args = [])
    {
        $user = Auth::user();
        $userActivity = app(UserActivityModel::class);
        $userActivity->actor_table = 'users';
        $userActivity->actor_id = $user ? $user->id : null;
        $userActivity->action = $action;
        $userActivity->text = $text;
        if (!empty($args)) {
            foreach ($args as $key => $value) {
                $userActivity->{$key} = $value;
            }
        }
        $userActivity->save();
        $userActivity->notify(new \App\Notifications\UserActivity);
    }
}
