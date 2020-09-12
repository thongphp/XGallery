<?php

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
            'jav_download',
            'xiuren_download',
            'truyenchon_download',
            'kissgoddess',
            'flickr_download',
            'admin_config'
        ];

        // Admin
        $admin = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web']
        );

        $this->createAndSyncPermissions($adminPermissions, $admin, 'web');

        // User
        $userPermissions = [
            'jav_download',
            'xiuren_download',
            'truyenchon_download',
            'kissgoddess',
            'flickr_download',
            'user_config'
        ];

        $user = Role::updateOrCreate(
            ['name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user', 'guard_name' => 'web']
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
