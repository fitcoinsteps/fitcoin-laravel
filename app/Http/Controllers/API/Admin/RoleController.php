<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json(Role::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'slug' => 'required|string|unique:roles,slug',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        $role = Role::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? 50,
            'is_system' => false,
            'status' => 'active',
        ]);

        return response()->json($role, 201);
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permission_ids);

        return response()->json(['message' => 'Permissions assigned']);
    }
}