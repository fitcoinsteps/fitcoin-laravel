<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $allPermissions = Permission::all();

        if ($superAdminRole) {
            foreach ($allPermissions as $permission) {
                DB::table('rolepermissions')->insert([
                    'role_id'       => $superAdminRole->id,
                    'permission_id' => $permission->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}