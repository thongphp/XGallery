<?php

namespace App\Console\Commands\System;

use App\Models\User;
use Illuminate\Console\Command;

class UserRole extends Command
{
    /**
     * Command for assign roles to user whom is not has role yet.
     *
     * @var string
     */
    protected $signature = 'system:user:sync-roles';

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
        $users = User::doesntHave('roles')->get();

        foreach ($users as $user) {
            \App\Facades\UserRole::checkAndAssignRole($user);
        }
    }
}
