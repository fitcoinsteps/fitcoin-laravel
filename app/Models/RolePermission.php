<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermission extends Pivot
{
    use SoftDeletes;

    protected $table = 'rolepermissions';
    protected $guarded = [];
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}