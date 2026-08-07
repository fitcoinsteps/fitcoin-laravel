<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'uuid'            => Str::uuid(),
            'employee_code'   => 'EMP0001',
            'username'        => 'superadmin',
            'first_name'      => 'Super',
            'last_name'       => 'Admin',
            'display_name'    => 'Super Admin',
            'email'           => 'admin@fitcoin.com',
            'email_verified_at' => now(),
            'password'        => bcrypt('password123'),
            'password_changed_at' => now(),
            'status'          => 'active',
            'is_active'       => true,
            'is_locked'       => false,
        ]);

        User::factory(10)->create();
    }
}