<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\VerificationCode;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RegistrationService
{
    public function createRegistration(array $data, string $role): Registration
    {
        $existing = Registration::where('email', $data['email'])->first();
        if ($existing && $existing->expires_at->isFuture()) {
            // Resend OTP for existing valid registration
            $this->sendOtp($existing);
            return $existing;
        } elseif ($existing) {
            $existing->delete();
        }

        $registration = Registration::create([
            'uuid'       => (string) Str::uuid(),
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'username'   => $data['email'],
            'password'   => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'role'       => $role,
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->sendOtp($registration);
        return $registration;
    }

    private function sendOtp(Registration $registration): void
    {
        $this->invalidateOldOtps($registration->id);
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
            Mail::to($registration->email)->send(new OtpMail($code, $registration->first_name));
        } catch (\Exception $e) {
            Log::error('OTP send failed: ' . $e->getMessage());
        }
    }

    private function invalidateOldOtps(int $registrationId): void
    {
        VerificationCode::where('registration_id', $registrationId)
            ->where('type', 'registration')
            ->whereNull('used_at')
            ->update(['is_revoked' => true]);
    }
}