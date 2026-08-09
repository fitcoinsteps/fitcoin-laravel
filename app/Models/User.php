<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'uuid',
        'employee_code',
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'display_name',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'phone_verified_at',
        'avatar',
        'status',
        'is_active',
        'is_locked',
        'is_deleted',
        'last_login_at',
        'last_activity_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    // JWT interface
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
// in app/Models/User.php
public function socialAccounts()
{
    return $this->hasMany(SocialAccount::class);
}
    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot('assigned_at', 'expires_at')
                    ->wherePivot('is_deleted', 0);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
                    ->withPivot('allowed')
                    ->wherePivot('is_deleted', 0);
    }

    public function jwtTokens()
    {
        return $this->hasMany(JwtToken::class);
    }
}