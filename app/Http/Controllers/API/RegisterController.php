<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\VerificationCode;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email|unique:registrations,email',
            'password'   => 'required|min:6|confirmed',
            'phone'      => 'nullable|string',
        ]);

        // Ensure default roles exist
        RoleService::createDefaultRolesIfNeeded();

        // Create pending registration
        $registration = Registration::create([
            'uuid'       => (string) Str::uuid(),
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'username'   => $data['email'], // or generate a username
            'password'   => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'expires_at' => now()->addMinutes(15),
        ]);

        // Generate OTP
        $code = random_int(100000, 999999);
        VerificationCode::create([
            'uuid'            => (string) Str::uuid(),
            'registration_id' => $registration->id,
            'type'            => 'registration',
            'via'             => 'email',
            'code'            => $code,
            'expires_at'      => now()->addMinutes(15),
        ]);

        // Send OTP email (uncomment after setting up Mail)
        // Mail::to($data['email'])->send(new \App\Mail\OtpMail($code));

        return response()->json(['message' => 'OTP sent to your email'], 200);
    }
}