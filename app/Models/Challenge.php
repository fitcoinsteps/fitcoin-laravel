<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challenge extends Model
{
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'goal_type',
        'goal_value',
        'time_limit_minutes',
        'reward_fitcoins',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }
}