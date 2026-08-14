<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class RoleService
{
    public static function createDefaultRolesIfNeeded()
    {
        $defaults = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'priority' => 1, 'is_system' => 1],
            ['name' => 'Admin',         'slug' => 'admin',       'priority' => 10],
            ['name' => 'Users',         'slug' => 'users',       'priority' => 50],
        ];

        foreach ($defaults as $role) {
            if (!Role::where('slug', $role['slug'])->exists()) {
                Role::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => $role['name'],
                    'slug' => $role['slug'],
                    'priority' => $role['priority'],
                    'is_system' => $role['is_system'] ?? 0,
                    'status' => 'active',
                ]);
            }
        }
    }

    public static function createDefaultPermissionsIfNeeded()
    {
        $permissions = [
            ['uuid' => 'e2611259-2024-41a8-822a-546cf2d329ec', 'module' => 'users', 'name' => 'View Users', 'slug' => 'users.view', 'group_name' => 'User Management'],
            ['uuid' => '53b8376a-d62c-4140-bc90-ecfb0ae79adc', 'module' => 'users', 'name' => 'Create Users', 'slug' => 'users.create', 'group_name' => 'User Management'],
            ['uuid' => '021e1071-4e22-4a88-be1c-37cc73df3fdf', 'module' => 'users', 'name' => 'Edit Users', 'slug' => 'users.edit', 'group_name' => 'User Management'],
            ['uuid' => 'ad74fc5d-d44c-4f84-a8e8-98c4e697fca7', 'module' => 'users', 'name' => 'Delete Users', 'slug' => 'users.delete', 'group_name' => 'User Management'],
            ['uuid' => '579b5dae-fb55-4ace-84c4-e43717bfaf49', 'module' => 'roles', 'name' => 'View Roles', 'slug' => 'roles.view', 'group_name' => 'Role Management'],
            ['uuid' => '3e011749-3c0d-4736-8001-205fee414a49', 'module' => 'roles', 'name' => 'Create Roles', 'slug' => 'roles.create', 'group_name' => 'Role Management'],
            ['uuid' => '0ebd9ffc-9a51-4aa6-9c7b-f8d79e0acf1f', 'module' => 'roles', 'name' => 'Edit Roles', 'slug' => 'roles.edit', 'group_name' => 'Role Management'],
            ['uuid' => '8412c9ed-e6f8-4de3-836b-f7de90ebce3e', 'module' => 'roles', 'name' => 'Delete Roles', 'slug' => 'roles.delete', 'group_name' => 'Role Management'],
            ['uuid' => '29ec82a8-38b2-440a-8ee6-34310d00cf39', 'module' => 'permissions', 'name' => 'View Permissions', 'slug' => 'permissions.view', 'group_name' => 'Permission Management'],
            ['uuid' => 'af2aade3-498d-4453-88ac-9945693562bb', 'module' => 'permissions', 'name' => 'Assign Permissions', 'slug' => 'permissions.assign', 'group_name' => 'Permission Management'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                [
                    'uuid' => $perm['uuid'],
                    'module' => $perm['module'],
                    'name' => $perm['name'],
                    'group_name' => $perm['group_name'],
                    'is_system' => 0,
                ]
            );
        }
    }

    public static function setupDefaults()
    {
        self::createDefaultRolesIfNeeded();
        self::createDefaultPermissionsIfNeeded();

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();

        // Super Admin gets ALL permissions
        if ($superAdminRole) {
            $allPermissions = Permission::pluck('id')->toArray();
            $superAdminRole->permissions()->sync($allPermissions);
        }

        // Admin gets a subset (everything except 'permissions.assign')
        if ($adminRole) {
            $adminPermissions = Permission::where('slug', '!=', 'permissions.assign')->pluck('id')->toArray();
            $adminRole->permissions()->sync($adminPermissions);
        }
    }
}