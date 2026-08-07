<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermissionFactory extends Factory
{
    private static $permissions = [
        ['module' => 'users', 'name' => 'View Users', 'slug' => 'users.view', 'group_name' => 'User Management'],
        ['module' => 'users', 'name' => 'Create Users', 'slug' => 'users.create', 'group_name' => 'User Management'],
        ['module' => 'users', 'name' => 'Edit Users', 'slug' => 'users.edit', 'group_name' => 'User Management'],
        ['module' => 'users', 'name' => 'Delete Users', 'slug' => 'users.delete', 'group_name' => 'User Management'],
        ['module' => 'roles', 'name' => 'View Roles', 'slug' => 'roles.view', 'group_name' => 'Role Management'],
        ['module' => 'roles', 'name' => 'Create Roles', 'slug' => 'roles.create', 'group_name' => 'Role Management'],
        ['module' => 'roles', 'name' => 'Edit Roles', 'slug' => 'roles.edit', 'group_name' => 'Role Management'],
        ['module' => 'roles', 'name' => 'Delete Roles', 'slug' => 'roles.delete', 'group_name' => 'Role Management'],
        ['module' => 'permissions', 'name' => 'View Permissions', 'slug' => 'permissions.view', 'group_name' => 'Permission Management'],
        ['module' => 'permissions', 'name' => 'Assign Permissions', 'slug' => 'permissions.assign', 'group_name' => 'Permission Management'],
    ];

    private static $index = 0;

    public function definition(): array
    {
        $perm = self::$permissions[self::$index % count(self::$permissions)];
        self::$index++;

        return [
            'uuid'       => Str::uuid(),
            'module'     => $perm['module'],
            'name'       => $perm['name'],
            'slug'       => $perm['slug'],
            'description'=> $perm['name'],
            'group_name' => $perm['group_name'],
            'is_system'  => false,
            'deleted_by' => null,
            'is_deleted' => false,
        ];
    }
}