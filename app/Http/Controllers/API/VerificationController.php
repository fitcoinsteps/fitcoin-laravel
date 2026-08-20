<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\User;
use App\Models\VerificationCode;
use App\Mail\OtpMail;
use App\Helpers\JwtTokenHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /**
     * Verify OTP for registration (email + code)
     */
    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        // Find the verification code
        $verificationCode = VerificationCode::where('code', $data['code'])
            ->where('expires_at', '>', now())
            ->where('is_revoked', false)
            ->whereNull('used_at')
            ->first();

        if (!$verificationCode) {
            return response()->json(['message' => 'Invalid or expired OTP code.'], 400);
        }

        // Validate email matches the associated record
        if ($verificationCode->type === 'registration') {
            $registration = Registration::find($verificationCode->registration_id);
            if (!$registration || $registration->email !== $data['email']) {
                return response()->json(['message' => 'Invalid OTP for this email.'], 400);
            }
        } elseif ($verificationCode->type === 'password_reset') {
            $user = User::find($verificationCode->user_id);
            if (!$user || $user->email !== $data['email']) {
                return response()->json(['message' => 'Invalid OTP for this email.'], 400);
            }
        } else {
            return response()->json(['message' => 'Unsupported verification type.'], 400);
        }

        // ---------- PASSWORD RESET FLOW ----------
        if ($verificationCode->type === 'password_reset') {
            // Mark as verified but DO NOT set used_at – keep it null
            $verificationCode->update(['verified_at' => now()]);

            return response()->json([
                'message' => 'OTP verified. You can now reset your password.',
                'token'   => $verificationCode->token,
                'email'   => $data['email'],
                'type'    => 'password_reset',
            ], 200);
        }

        // ---------- REGISTRATION FLOW ----------
        // Mark as used (only for registration)
        $verificationCode->update(['used_at' => now()]);

        $registration = Registration::find($verificationCode->registration_id);
        if (!$registration) {
            return response()->json(['message' => 'Registration record not found.'], 404);
        }

        try {
            DB::beginTransaction();

            // Create user with role stored in registration
            $user = User::create([
                'uuid'              => (string) Str::uuid(),
                'email'             => $registration->email,
                'phone'             => $registration->phone,
                'username'          => $registration->username,
                'password'          => $registration->password,
                'first_name'        => $registration->first_name,
                'last_name'         => $registration->last_name,
                'role'              => $registration->role,
                'email_verified_at' => now(),
                'is_active'         => 1,
            ]);

            // Delete the registration record
            $registration->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            return response()->json(['message' => 'Registration failed. Please try again.'], 500);
        }

        $tokens = JwtTokenHelper::generateSimpleTokens($user);

        return response()->json([
            'message'    => 'Registration completed successfully!',
            'user'       => $user,
            'token'      => $tokens['access_token'],
            'token_type' => 'bearer',
            'expires_in' => $tokens['expires_in'],
            'role'       => $user->role,
        ], 200);
    }

    /**
     * Resend OTP for registration
     */
    public function resend(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $registration = Registration::where('email', $data['email'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$registration) {
            return response()->json(['message' => 'Registration not found or expired.'], 404);
        }

        // Throttle: limit to one OTP per 2 minutes
        $recentCode = VerificationCode::where('registration_id', $registration->id)
            ->where('type', 'registration')
            ->whereNull('used_at')
            ->where('created_at', '>', now()->subMinutes(2))
            ->first();

        if ($recentCode) {
            return response()->json(['message' => 'Please wait 2 minutes before requesting a new code.'], 429);
        }

        // Generate new OTP
        $code = random_int(100000, 999999);

        // Invalidate old OTPs
        VerificationCode::where('registration_id', $registration->id)
            ->where('type', 'registration')
            ->whereNull('used_at')
            ->update(['is_revoked' => true]);

        // Create new OTP
        $verificationCode = VerificationCode::create([
            'uuid'            => (string) Str::uuid(),
            'registration_id' => $registration->id,
            'type'            => 'registration',
            'via'             => 'email',
            'code'            => $code,
            'expires_at'      => now()->addMinutes(15),
        ]);

        try {
            Mail::to($data['email'])->send(new OtpMail($code, $registration->first_name));
            return response()->json([
                'message'    => 'New OTP sent to your email',
                'token'      => $verificationCode->token,
                'expires_at' => $verificationCode->expires_at,
            ], 200);
        } catch (\Exception $e) {
            Log::error('OTP resend failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }

    /**
     * Send OTP for various purposes (registration, login, password reset)
     */
    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email'           => 'required|email',
            'type'            => 'required|in:registration,login,password_reset',
            'user_id'         => 'nullable|exists:users,id',
            'registration_id' => 'nullable|exists:registrations,id',
        ]);

        // Throttle
        $recentCode = VerificationCode::where(function ($query) use ($data) {
                if ($data['user_id']) {
                    $query->where('user_id', $data['user_id']);
                }
                if ($data['registration_id']) {
                    $query->orWhere('registration_id', $data['registration_id']);
                }
            })
            ->where('type', $data['type'])
            ->whereNull('used_at')
            ->where('created_at', '>', now()->subMinutes(2))
            ->first();

        if ($recentCode) {
            return response()->json(['message' => 'Please wait 2 minutes before requesting a new code.'], 429);
        }

        // Revoke old codes
        if ($data['user_id']) {
            VerificationCode::where('user_id', $data['user_id'])
                ->where('type', $data['type'])
                ->whereNull('used_at')
                ->update(['is_revoked' => true]);
        }
        if ($data['registration_id']) {
            VerificationCode::where('registration_id', $data['registration_id'])
                ->where('type', $data['type'])
                ->whereNull('used_at')
                ->update(['is_revoked' => true]);
        }

        $code = random_int(100000, 999999);
        $token = Str::random(60);

        $verificationCode = VerificationCode::create([
            'uuid'            => (string) Str::uuid(),
            'user_id'         => $data['user_id'] ?? null,
            'registration_id' => $data['registration_id'] ?? null,
            'type'            => $data['type'],
            'via'             => 'email',
            'code'            => $code,
            'token'           => $token,
            'expires_at'      => now()->addMinutes(15),
        ]);

        try {
            Mail::to($data['email'])->send(new OtpMail($code, $data['type']));
            return response()->json([
                'message'    => 'OTP sent successfully',
                'token'      => $verificationCode->token,
                'expires_at' => $verificationCode->expires_at,
            ], 200);
        } catch (\Exception $e) {
            Log::error('OTP send failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }

    /**
     * Verify OTP with token (used for password reset)
     */
    public function verifyWithToken(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|exists:verification_codes,token',
            'code'  => 'required|string|size:6',
        ]);

        $verificationCode = VerificationCode::where('token', $data['token'])
            ->where('code', $data['code'])
            ->where('expires_at', '>', now())
            ->where('is_revoked', false)
            ->whereNull('used_at')
            ->first();

        if (!$verificationCode) {
            VerificationCode::where('token', $data['token'])->increment('attempts');
            return response()->json(['message' => 'Invalid or expired OTP code.'], 400);
        }

        if ($verificationCode->type === 'password_reset') {
            $verificationCode->update(['verified_at' => now()]);

            return response()->json([
                'message' => 'OTP verified successfully. Proceed to reset password.',
                'verified' => true,
                'type'     => 'password_reset',
                'token'    => $verificationCode->token,
                'email'    => optional($verificationCode->user)->email,
            ], 200);
        }

        $verificationCode->update(['used_at' => now()]);

        return response()->json([
            'message'  => 'OTP verified successfully.',
            'verified' => true,
            'type'     => $verificationCode->type,
        ], 200);
    }

    /**
     * Check OTP status
     */
    public function checkStatus(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $registration = Registration::where('email', $data['email'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$registration) {
            return response()->json(['message' => 'Registration not found or expired.'], 404);
        }

        $verificationCode = VerificationCode::where('registration_id', $registration->id)
            ->where('type', 'registration')
            ->where('expires_at', '>', now())
            ->where('is_revoked', false)
            ->whereNull('used_at')
            ->first();

        if (!$verificationCode) {
            return response()->json([
                'status'  => 'expired',
                'message' => 'OTP expired or not found. Please request a new one.'
            ], 200);
        }

        return response()->json([
            'status'     => 'active',
            'expires_at' => $verificationCode->expires_at,
            'attempts'   => $verificationCode->attempts,
            'message'    => 'OTP is active',
        ], 200);
    }

    /**
     * Revoke OTP
     */
    public function revokeOtp(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);

        $registration = Registration::where('email', $data['email'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$registration) {
            return response()->json(['message' => 'Registration not found or expired.'], 404);
        }

        VerificationCode::where('registration_id', $registration->id)
            ->where('type', 'registration')
            ->whereNull('used_at')
            ->update(['is_revoked' => true]);

        return response()->json(['message' => 'OTP revoked successfully'], 200);
    }
}