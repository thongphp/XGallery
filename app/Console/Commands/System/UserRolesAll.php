<?php

namespace App\Console\Commands\System;

use App\Facades\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class UserRolesAll extends Command
{
    /**
     * Command for re-assign roles to all users.
     *
     * @var string
     */
    protected $signature = 'system:user:roles-sync-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Entry point
     */
    public function handle(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            UserRole::checkAndAssignRole($user);
        }
    }
}
