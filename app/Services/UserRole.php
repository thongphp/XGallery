<?php

namespace App\Services;

use App\Models\User;
use Laravel\Socialite\One\User as OAuthOneUser;
use Laravel\Socialite\Two\User as OAuthTwoUser;
use Spatie\Permission\Models\Role;

class UserRole
{
    public const PERMISSION_JAV_DOWNLOAD = 'jav.download';
    public const PERMISSION_XIUREN_DOWNLOAD = 'xiuren.download';
    public const PERMISSION_TRUYENCHON_DOWNLOAD = 'truyenchon.download';
    public const PERMISSION_KISSGODDESS_DOWNLOAD = 'kissgoddess.download';
    public const PERMISSION_FLICKR_DOWNLOAD = 'flickr.download';

    public const PERMISSION_ADMIN_CONFIG = 'admin.config';

    public const PERMISSION_USER_CONFIG = 'user.config';

    /**
     * @param  User|OAuthOneUser|OAuthTwoUser  $user
     */
    public function checkAndAssignRole($user): void
    {
        $superAdminEmails = config('services.authenticated.emails');
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
