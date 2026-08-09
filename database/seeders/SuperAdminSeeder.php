<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // ------------------ Default Roles ------------------
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'priority' => 1, 'is_system' => 1],
            ['name' => 'Admin', 'slug' => 'admin', 'priority' => 10],
            ['name' => 'Users', 'slug' => 'users', 'priority' => 50],
            ['name' => 'Accounts', 'slug' => 'accounts', 'priority' => 30],
            ['name' => 'Staffs', 'slug' => 'staffs', 'priority' => 40],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $role['name'],
                    'priority' => $role['priority'],
                    'is_system' => $role['is_system'] ?? 0,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ------------------ Super Admin User ------------------
        $user = DB::table('users')->updateOrInsert(
            ['email' => 'admin@fitcoin.com'],
            [
                'uuid' => (string) Str::uuid(),
                'username' => 'Fitcoin',
                'first_name' => 'Fit',
                'last_name' => 'Coin',
                'email_verified_at' => now(),
                'password' => Hash::make('Dont4Get'),
                'status' => 'active',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Attach Super Admin role to this user
        $role = DB::table('roles')->where('slug', 'super-admin')->first();
        $userId = DB::table('users')->where('email', 'admin@fitcoin.com')->value('id');

        DB::table('user_roles')->updateOrInsert(
            ['user_id' => $userId, 'role_id' => $role->id],
            [
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}