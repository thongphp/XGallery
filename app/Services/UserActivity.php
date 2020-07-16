<?php

namespace App\Services;

use App\Models\Core\UserActivityModel;
use App\Models\User;

/**
 * Class UserActivity
 * @package App\Services
 */
class UserActivity
{
    /**
     * @param  string  $text // Usually follow format SOMEONE do ACTION on SOMETHING
     * @param  User|null  $user
     * @param  string  $action
     * @param  array  $args
     */
    public function notify(string $text, ?User $user, string $action, array $args = [])
    {
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
