<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            UserRolesTableSeeder::class,
            RolePermissionsTableSeeder::class,
            UserPermissionsTableSeeder::class,
        ]);
    }
}