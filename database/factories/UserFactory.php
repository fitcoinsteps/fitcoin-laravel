<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'employee_code' => 'EMP' . fake()->unique()->numberBetween(1000, 9999),
            'username' => fake()->unique()->userName(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'display_name' => fn(array $attributes) => $attributes['first_name'] . ' ' . $attributes['last_name'],
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->optional()->phoneNumber(),
            'phone_verified_at' => now(),
            'password' => bcrypt('password'),
            'password_changed_at' => now(),
            'avatar' => null,
            'status' => 'active',
            'is_active' => true,
            'is_locked' => false,
            'is_deleted' => false,
            'last_login_at' => now(),
            'last_activity_at' => now(),
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }
}