<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'uuid',
        'email',
        'phone',
        'username',
        'password',
        'first_name',
        'last_name',
        'middle_name',
        'employee_code',
        'avatar',
        'role',
        'registration_data',
        'expires_at',
        'is_verified',
        'verified_at',
    ];

    protected $attributes = [
        'role' => 'user',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'registration_data' => 'array',
    ];

    public function verificationCodes()
    {
        return $this->hasMany(VerificationCode::class);
    }
}