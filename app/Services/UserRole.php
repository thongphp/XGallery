<?php

namespace App\Services;

use App\Models\User;
use Laravel\Socialite\One\User as OAuthOneUser;
use Laravel\Socialite\Two\User as OAuthTwoUser;
use Spatie\Permission\Models\Role;

class UserRole
{
    /**
     * @param User|OAuthOneUser|OAuthTwoUser $user
     */
    public function checkAndAssignRole($user): void
    {
        $superAdminEmails = config('auth.super_admin_email');
        $superAdminEmails = !is_array($superAdminEmails) ? [$superAdminEmails] : $superAdminEmails;
        $userRole = Role::findByName(User::ROLE_USER, 'web');
        $adminRole = Role::findByName(User::ROLE_ADMIN, 'web');

        if (in_array($user->email, $superAdminEmails, true)) {
            $user->assignRole($adminRole);

            return;
        }

        $user->assignRole($userRole);
    }
}
