<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\VerificationCode;
use App\Helpers\JwtTokenHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6',
        ]);

        $verification = VerificationCode::whereHas('registration', function ($q) use ($request) {
            $q->where('email', $request->email);
        })
        ->where('code', $request->code)
        ->where('expires_at', '>', now())
        ->whereNull('used_at')
        ->where('is_revoked', false)
        ->first();

        if (!$verification) {
            return response()->json(['error' => 'Invalid or expired OTP'], 422);
        }

        // Mark code as used
        $verification->update(['used_at' => now()]);

        $registration = $verification->registration;

        // Determine if this is the first user ever
        $isFirstUser = User::count() === 0;

        // Create the actual user
        $user = User::create([
            'uuid'              => (string) Str::uuid(),
            'username'          => $registration->username,
            'first_name'        => $registration->first_name,
            'last_name'         => $registration->last_name,
            'email'             => $registration->email,
            'password'          => $registration->password, // already hashed
            'phone'             => $registration->phone,
            'email_verified_at' => now(),
            'status'            => 'active',
            'is_active'         => true,
        ]);

        // Assign role: first user = super-admin, others = users
        $roleSlug = $isFirstUser ? 'super-admin' : 'users';
        $role = Role::where('slug', $roleSlug)->first();
        $user->roles()->attach($role->id, ['assigned_at' => now()]);

        // Optionally mark registration as verified
        $registration->update(['is_verified' => true, 'verified_at' => now()]);

        // Generate JWT tokens
        $tokens = JwtTokenHelper::generateTokens($user);

        return response()->json($tokens, 201);
    }
}