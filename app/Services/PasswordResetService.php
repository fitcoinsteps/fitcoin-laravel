<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificationCode;
use App\Mail\OtpMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PasswordResetService
{
    public function sendResetOtp(string $email): array
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return ['status' => false, 'message' => 'If the email exists, a reset link will be sent.'];
        }

        $token = Str::random(60);
        $otp = random_int(100000, 999999);

        VerificationCode::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->update(['is_revoked' => true]);

        VerificationCode::create([
            'uuid'       => (string) Str::uuid(),
            'user_id'    => $user->id,
            'type'       => 'password_reset',
            'via'        => 'email',
            'code'       => $otp,
            'token'      => $token,
            'expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user->first_name, 'password_reset'));
        } catch (\Exception $e) {
            Log::error('Password reset OTP email failed: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to send OTP. Please try again.'];
        }

        return ['status' => true, 'email' => $user->email, 'token' => $token];
    }

    public function resetPassword(string $email, string $token, string $newPassword): array
    {
        $verification = VerificationCode::where('token', $token)
            ->where('type', 'password_reset')
            ->where('expires_at', '>', now())
            ->where('is_revoked', false)
            ->whereNull('used_at')
            ->first();

        if (!$verification) {
            return ['status' => false, 'message' => 'Invalid or expired token.'];
        }

        $user = User::where('email', $email)->first();
        if (!$user || $user->id != $verification->user_id) {
            return ['status' => false, 'message' => 'User not found.'];
        }

        $user->update(['password' => Hash::make($newPassword)]);
        $verification->update(['used_at' => now()]);

        return ['status' => true, 'user' => $user];
    }
}