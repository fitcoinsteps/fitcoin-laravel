<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {

        Role::create([
            'uuid'        => Str::uuid(),
            'name'        => 'Super Admin',
            'slug'        => 'super-admin',
            'description' => 'Has access to all features',
            'priority'    => 1,
            'is_system'   => true,
            'status'      => 'active',
        ]);

        Role::factory(5)->create();
    }
}