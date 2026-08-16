<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index()
    {
        return response()->json(User::with('roles')->paginate(20));
    }

    /**
     * Store a newly created user (by super-admin).
     * Allowed roles: admin (or any role except 'users' and 'super-admin').
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'role'       => [
                'required',
                'string',
                'exists:roles,slug',
                function ($attribute, $value, $fail) {
                    if ($value === 'users') {
                        $fail('The users role cannot be assigned by an admin. Users must register themselves.');
                    }
                    if ($value === 'super-admin') {
                        $fail('Only one super-admin is allowed. You cannot create another super-admin.');
                    }
                },
            ],
        ]);

        $user = User::create([
            'uuid'       => (string) Str::uuid(),
            'username'   => $data['email'],
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'email_verified_at' => now(),
            'status'     => 'active',
            'is_active'  => true,
        ]);

        $role = Role::where('slug', $data['role'])->first();
        $user->roles()->attach($role->id, ['assigned_at' => now()]);

        return response()->json($user->load('roles'), 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return response()->json($user->load('roles'));
    }

    /**
     * Update the specified user (basic fields only).
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:users,email,' . $user->id,
            'password'   => 'sometimes|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($user->fresh('roles'));
    }

    /**
     * Remove the specified user (soft delete).
     * Prevent deleting the super-admin.
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return response()->json(['error' => 'Cannot delete the super-admin account.'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    /**
     * Assign or change the role of a user (super-admin only).
     * The 'users' role is not allowed, and 'super-admin' cannot be assigned to anyone.
     */
    public function assignRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => [
                'required',
                'string',
                'exists:roles,slug',
                function ($attribute, $value, $fail) {
                    if ($value === 'users') {
                        $fail('The users role cannot be assigned manually. Users must register themselves.');
                    }
                    if ($value === 'super-admin') {
                        $fail('Only one super-admin is allowed. You cannot assign the super-admin role.');
                    }
                },
            ],
        ]);

        // Prevent changing the super-admin's role
        if ($user->hasRole('super-admin')) {
            return response()->json(['error' => 'Cannot change the role of the super-admin account.'], 403);
        }

        $role = Role::where('slug', $data['role'])->first();
        $user->roles()->sync([$role->id => ['assigned_at' => now()]]);

        return response()->json(['message' => 'Role updated']);
    }
}