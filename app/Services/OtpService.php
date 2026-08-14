<?php

namespace App\Services;

use App\Models\VerificationCode;
use App\Models\User;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OtpService
{
    public function generateOtp($via, $type, $userId = null, $registrationId = null)
    {
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Generate unique token
        $token = Str::uuid();

        return VerificationCode::create([
            'uuid' => Str::uuid(),
            'user_id' => $userId,
            'registration_id' => $registrationId,
            'type' => $type, // registration, login, password_reset, etc.
            'via' => $via, // email, sms
            'code' => $otp,
            'token' => $token,
            'expires_at' => Carbon::now()->addMinutes(10),
            'attempts' => 0,
            'is_revoked' => false,
        ]);
    }

    public function verifyOtp($token, $code)
    {
        $verification = VerificationCode::where('token', $token)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->where('is_revoked', false)
            ->whereNull('used_at')
            ->first();

        if (!$verification) {
            return false;
        }

        // Mark as used
        $verification->update([
            'used_at' => Carbon::now()
        ]);

        return $verification;
    }

    public function revokeOldCodes($userId, $type)
    {
        VerificationCode::where('user_id', $userId)
            ->where('type', $type)
            ->whereNull('used_at')
            ->update(['is_revoked' => true]);
    }

    public function incrementAttempts($token)
    {
        $verification = VerificationCode::where('token', $token)->first();
        if ($verification) {
            $verification->increment('attempts');
            
            // Revoke after 3 failed attempts
            if ($verification->attempts >= 3) {
                $verification->update(['is_revoked' => true]);
                return false;
            }
            return true;
        }
        return false;
    }
}