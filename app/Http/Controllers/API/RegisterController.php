<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\VerificationCode;
use App\Services\RoleService;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email', // Only check users table now
            'password'   => 'required|min:6|confirmed',
            'phone'      => 'nullable|string',
        ]);

        // Check if registration already exists (pending verification)
        $existingRegistration = Registration::where('email', $data['email'])->first();
        
        if ($existingRegistration) {
            // Check if the registration is still valid (not expired)
            if ($existingRegistration->expires_at->isFuture()) {
                // Registration exists and is still valid - resend OTP
                $code = random_int(100000, 999999);
                
                // Invalidate old OTPs
                VerificationCode::where('registration_id', $existingRegistration->id)
                    ->where('type', 'registration')
                    ->whereNull('used_at')
                    ->update(['is_revoked' => true]);
                
                // Create new OTP
                VerificationCode::create([
                    'uuid'            => (string) Str::uuid(),
                    'registration_id' => $existingRegistration->id,
                    'type'            => 'registration',
                    'via'             => 'email',
                    'code'            => $code,
                    'expires_at'      => now()->addMinutes(15),
                ]);
                
                // Resend OTP
                try {
                    Mail::to($data['email'])->send(new OtpMail($code, $existingRegistration->first_name));
                    
                    return response()->json([
                        'message' => 'OTP resent to your email',
                        'email'   => $data['email'],
                        'redirect' => '/verify-otp?email=' . urlencode($data['email'])
                    ], 200);
                } catch (\Exception $e) {
                    Log::error('OTP resend failed: ' . $e->getMessage());
                    return response()->json([
                        'message' => 'Failed to send OTP. Please try again.',
                    ], 500);
                }
            } else {
                // Registration expired - delete it and create new one
                $existingRegistration->delete();
                return $this->createNewRegistration($data);
            }
        }

        // No existing registration - create new one
        return $this->createNewRegistration($data);
    }

    private function createNewRegistration($data)
    {
        // Ensure default roles exist
        RoleService::createDefaultRolesIfNeeded();

        // Create pending registration
        $registration = Registration::create([
            'uuid'       => (string) Str::uuid(),
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'username'   => $data['email'],
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

        // Send OTP email
        try {
            Mail::to($data['email'])->send(new OtpMail($code, $data['first_name']));
            
            return response()->json([
                'message' => 'OTP sent to your email',
                'email'   => $data['email'],
                'redirect' => '/verify-otp?email=' . urlencode($data['email'])
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('OTP email failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to send OTP. Please try again.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}