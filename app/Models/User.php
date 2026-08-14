<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

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

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function jwtTokens()
    {
        return $this->hasMany(JwtToken::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r)) {
                    return true;
                }
            }
            return false;
        }

        return !!$role->intersect($this->roles)->count();
    }

    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('slug', $permission);
        }

        if (is_array($permission)) {
            foreach ($permission as $p) {
                if ($this->hasPermission($p)) {
                    return true;
                }
            }
            return false;
        }

        return !!$permission->intersect($this->permissions)->count();
    }

    public function hasAnyRole($roles)
    {
        return $this->hasRole($roles);
    }

    public function hasAllRoles($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function hasPermissionTo($permission)
    {
        return $this->hasPermission($permission);
    }

    public function hasAnyPermission($permissions)
    {
        return $this->hasPermission($permissions);
    }
}