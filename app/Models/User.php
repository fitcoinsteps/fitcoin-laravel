<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the JWT subject claim.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key-value array containing any custom claims to add to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the social accounts associated with the user.
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot('assigned_at', 'expires_at')
                    ->wherePivot('is_deleted', 0);
    }

    /**
     * The permissions that belong to the user.
     */
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

    /**
     * Get the JWT tokens owned by the user.
     */
    public function jwtTokens()
    {
        return $this->hasMany(JwtToken::class);
    }

    /**
     * Check if the user has a specific role slug.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles->contains('slug', $roleSlug);
    }

    /**
     * Check if the user has any of the given role slugs.
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->roles->pluck('slug')->intersect($roleSlugs)->isNotEmpty();
    }
}