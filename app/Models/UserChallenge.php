<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChallenge extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'challenge_id',
        'status',
        'started_at',
        'completed_at',
        'steps_at_start',
        'steps_at_completion',
        'reward_earned',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }
}