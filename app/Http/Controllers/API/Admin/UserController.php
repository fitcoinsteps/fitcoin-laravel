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
     * Store a newly created user (by admin).
     * The 'users' role is not allowed – normal users must register themselves.
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
                // Block assigning the 'users' role
                function ($attribute, $value, $fail) {
                    if ($value === 'users') {
                        $fail('The users role cannot be assigned by an admin. Users must register themselves.');
                    }
                },
            ],
        ]);

        $user = User::create([
            'uuid'       => (string) Str::uuid(),
            'username'   => $data['email'],   // you can accept a custom username if needed
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
     * You can extend this later as needed.
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
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    /**
     * Assign or change the role of a user (admin only).
     * The 'users' role is not allowed – normal users must register themselves.
     */
    public function assignRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => [
                'required',
                'string',
                'exists:roles,slug',
                // Block assigning the 'users' role
                function ($attribute, $value, $fail) {
                    if ($value === 'users') {
                        $fail('The users role cannot be assigned manually. Users must register themselves.');
                    }
                },
            ],
        ]);

        $role = Role::where('slug', $data['role'])->first();
        // Replace all current roles with the new one
        $user->roles()->sync([$role->id => ['assigned_at' => now()]]);

        return response()->json(['message' => 'Role updated']);
    }
}