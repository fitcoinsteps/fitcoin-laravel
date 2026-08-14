<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'priority',
        'is_system',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'is_deleted',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->wherePivot('is_deleted', 0);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->wherePivot('is_deleted', 0);
    }
}