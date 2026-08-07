<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Pivot
{
    use SoftDeletes;

    protected $table = 'userroles';
    protected $guarded = [];
    protected $casts = [
        'is_deleted' => 'boolean',
        'assigned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}