<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'users';        
    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'is_deleted' => 'boolean',
    ];


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'userroles', 'user_id', 'role_id')
                    ->withPivot('assigned_by', 'assigned_at', 'expires_at')
                    ->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'userpermissions', 'user_id', 'permission_id')
                    ->withPivot('allowed', 'assigned_by')
                    ->withTimestamps();
    }
}