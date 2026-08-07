<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permissions';
    protected $guarded = [];
    protected $casts = [
        'is_system' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'rolepermissions', 'permission_id', 'role_id')
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'userpermissions', 'permission_id', 'user_id')
                    ->withPivot('allowed', 'assigned_by')
                    ->withTimestamps();
    }
}