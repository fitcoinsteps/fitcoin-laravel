<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'module',
        'name',
        'slug',
        'description',
        'group_name',
        'is_system',
        'deleted_by',
        'is_deleted',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->wherePivot('is_deleted', 0);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions')
                    ->withPivot('allowed')
                    ->wherePivot('is_deleted', 0);
    }
}