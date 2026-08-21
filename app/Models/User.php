<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;  // ✅ add this import
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;   // ✅ add SoftDeletes trait

    const ROLE_SUPER_ADMIN = 'super-admin';
    const ROLE_USER = 'user';

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
        'role',
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
        'fitcoin_balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'locked_until' => 'datetime',
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

    // Relationships
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function jwtTokens(): HasMany
    {
        return $this->hasMany(JwtToken::class);
    }

    public function verificationCodes(): HasMany
    {
        return $this->hasMany(VerificationCode::class);
    }

    // Fitcoin related relationships
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function fitcoinTransactions(): HasMany
    {
        return $this->hasMany(FitcoinTransaction::class);
    }

    public function trackingSessions(): HasMany
    {
        return $this->hasMany(TrackingSession::class);
    }

    // Challenge related
    public function challenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }

    // Role helpers
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }
}