<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';
    protected $guarded = [];
    protected $casts = [
        'is_system' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'userroles', 'role_id', 'user_id')
                    ->withPivot('assigned_by', 'assigned_at', 'expires_at')
                    ->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'rolepermissions', 'role_id', 'permission_id')
                    ->withTimestamps();
    }
}