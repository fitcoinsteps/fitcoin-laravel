<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPermission extends Pivot
{
    use SoftDeletes;

    protected $table = 'userpermissions';

    protected $fillable = [
        'user_id',
        'permission_id',
        'allowed',
        'assigned_by',
    ];

    protected $casts = [
        'allowed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}