<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminUser = User::where('email', 'admin@fitcoin.com')->first();
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if ($superAdminUser && $superAdminRole) {
            DB::table('userroles')->insert([
                'user_id'    => $superAdminUser->id,
                'role_id'    => $superAdminRole->id,
                'assigned_at'=> now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}