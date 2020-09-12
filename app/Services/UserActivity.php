<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\UserActivity as UserActivityNotification;

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
    public function notify(string $text, ?User $user, string $action, array $args = []): void
    {
        $userActivity = app(\App\Models\Core\UserActivity::class);
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
        $userActivity->notify(new UserActivityNotification);
    }
}
