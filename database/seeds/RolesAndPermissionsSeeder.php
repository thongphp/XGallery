<?php

use App\Models\User;
use App\Services\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $adminPermissions = [
            UserRole::PERMISSION_JAV_DOWNLOAD,
            UserRole::PERMISSION_XIUREN_DOWNLOAD,
            UserRole::PERMISSION_TRUYENCHON_DOWNLOAD,
            UserRole::PERMISSION_KISSGODDESS_DOWNLOAD,
            UserRole::PERMISSION_FLICKR_DOWNLOAD,
            UserRole::PERMISSION_ADMIN_CONFIG,
        ];

        // Admin
        $admin = Role::updateOrCreate(
            ['name' => User::ROLE_ADMIN, 'guard_name' => 'web'],
            ['name' => User::ROLE_ADMIN, 'guard_name' => 'web']
        );

        $this->createAndSyncPermissions($adminPermissions, $admin, 'web');

        // User
        $userPermissions = [
            UserRole::PERMISSION_JAV_DOWNLOAD,
            UserRole::PERMISSION_XIUREN_DOWNLOAD,
            UserRole::PERMISSION_TRUYENCHON_DOWNLOAD,
            UserRole::PERMISSION_KISSGODDESS_DOWNLOAD,
            UserRole::PERMISSION_FLICKR_DOWNLOAD,
            UserRole::PERMISSION_USER_CONFIG,
        ];

        $user = Role::updateOrCreate(
            ['name' => User::ROLE_USER, 'guard_name' => 'web'],
            ['name' => User::ROLE_USER, 'guard_name' => 'web']
        );

        $this->createAndSyncPermissions($userPermissions, $user, 'web');
    }

    /**
     * @param array $permissions
     * @param Role $role
     * @param string $guard
     */
    private function createAndSyncPermissions(array $permissions, Role $role, string $guard): void
    {
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        $role->syncPermissions($permissions);
    }
}
