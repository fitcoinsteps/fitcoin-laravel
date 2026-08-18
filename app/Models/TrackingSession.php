<?php

// app/Models/TrackingSession.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'start_lat',
        'start_lng',
        'end_lat',
        'end_lng',
        'distance_meters',
        'duration_seconds',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
        'distance_meters' => 'float',
        'duration_seconds' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
