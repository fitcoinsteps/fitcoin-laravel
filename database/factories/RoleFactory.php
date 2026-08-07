<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Admin', 'Moderator', 'User', 'Guest', 'Manager']);

        return [
            'uuid'        => Str::uuid(),
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $name . ' role',
            'priority'    => fake()->numberBetween(1, 100),
            'is_system'   => false,
            'status'      => 'active',
            'created_by'  => null,
            'updated_by'  => null,
            'deleted_by'  => null,
            'is_deleted'  => false,
        ];
    }
}