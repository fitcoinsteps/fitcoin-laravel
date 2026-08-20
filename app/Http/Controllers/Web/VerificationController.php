<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\User;
use App\Models\VerificationCode;
use App\Helpers\JwtTokenHelper;
use App\Mail\OtpMail;
use App\Traits\DeviceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    use DeviceTrait;

    /**
     * Verify OTP for registration (email + code).
     */
    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $verificationCode = VerificationCode::where('code', $data['code'])
            ->where('expires_at', '>', now())
            ->where('is_revoked', false)
            ->whereNull('used_at')
            ->first();

        if (!$verificationCode) {
            return response()->json(['message' => 'Invalid or expired OTP code.'], 400);
        }

        // Registration flow
        if ($verificationCode->type === 'registration') {
            $registration = Registration::find($verificationCode->registration_id);
            if (!$registration || $registration->email !== $data['email']) {
                return response()->json(['message' => 'Invalid OTP for this email.'], 400);
            }

            $verificationCode->update(['used_at' => now()]);

            try {
                DB::beginTransaction();

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
                    'is_active'         => true,
                ]);

                $registration->delete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Registration verification failed: ' . $e->getMessage());
                return response()->json(['message' => 'Registration failed. Please try again.'], 500);
            }

            // ✅ Create device record for the new user
            $this->checkDevice($user, $request->ip(), $request->userAgent(), 'Unknown Device', false);

            $tokens = JwtTokenHelper::generateTokens($user);

            $accessCookie = cookie(
                'jwt_token',
                $tokens['access_token'],
                $tokens['expires_in'] / 60,
                '/',
                null,
                true,
                true,
                false,
                'Strict'
            );

            $refreshCookie = cookie(
                'jwt_refresh_token',
                $tokens['refresh_token'],
                30 * 24 * 60,
                '/',
                null,
                true,
                true,
                false,
                'Strict'
            );

            return response()->json([
                'message' => 'Registration completed successfully!',
                'user'    => $user,
                'role'    => $user->role,
            ])->withCookie($accessCookie)->withCookie($refreshCookie);
        }

        // Password reset verification
        if ($verificationCode->type === 'password_reset') {
            $user = User::find($verificationCode->user_id);
            if (!$user || $user->email !== $data['email']) {
                return response()->json(['message' => 'Invalid OTP for this email.'], 400);
            }

            $verificationCode->update(['verified_at' => now()]);

            return response()->json([
                'message' => 'OTP verified. You can now reset your password.',
                'token'   => $verificationCode->token,
                'email'   => $data['email'],
                'type'    => 'password_reset',
            ], 200);
        }

        return response()->json(['message' => 'Unsupported verification type.'], 400);
    }

    /**
     * Resend OTP for registration.
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

        $recentCode = VerificationCode::where('registration_id', $registration->id)
            ->where('type', 'registration')
            ->whereNull('used_at')
            ->where('created_at', '>', now()->subMinutes(2))
            ->first();

        if ($recentCode) {
            return response()->json(['message' => 'Please wait 2 minutes before requesting a new code.'], 429);
        }

        VerificationCode::where('registration_id', $registration->id)
            ->where('type', 'registration')
            ->whereNull('used_at')
            ->update(['is_revoked' => true]);

        $code = random_int(100000, 999999);
        VerificationCode::create([
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
                'message' => 'New OTP sent to your email',
            ], 200);
        } catch (\Exception $e) {
            Log::error('OTP resend failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }
}