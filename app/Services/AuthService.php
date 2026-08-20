<?php

namespace App\Services;

use App\Models\User;
use App\Models\Registration;
use App\Models\VerificationCode;
use App\Models\LoginHistory;
use App\Models\Device;
use App\Mail\OtpMail;
use App\Traits\DeviceTrait;
use App\Helpers\JwtTokenHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWTGuard;

class AuthService
{
    use DeviceTrait;

    /**
     * Attempt to authenticate a user with the given credentials.
     * Optionally restrict allowed roles via $allowedRoles.
     *
     * @param array $credentials
     * @param Request $request
     * @param array|null $allowedRoles
     * @return array
     */
    public function attemptLogin(array $credentials, Request $request, ?array $allowedRoles = null): array
    {
        $email = $credentials['email'];
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $deviceName = $credentials['device_name'] ?? 'Unknown Device';

        if ($this->isAccountLocked($email, $ip)) {
            throw new \Exception('Account temporarily locked due to too many failed attempts. Please try again later.', 403);
        }

        try {
            $this->checkRateLimit($email, $ip);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 429);
        }

        $user = User::where('email', $email)->first();

        $verificationNeeded = $this->handleVerification($user, $credentials);
        if ($verificationNeeded) {
            throw new \Exception($verificationNeeded->getContent(), 403);
        }

        /** @var JWTGuard $guard */
        $guard = auth('api');

        if (!$token = $guard->attempt($credentials)) {
            $this->logFailedAttempt($email, $ip, $userAgent);
            throw new \Exception('Invalid credentials', 401);
        }

        /** @var User $user */
        $user = $guard->user();

        // Role restriction (new)
        if ($allowedRoles !== null && !in_array($user->role, $allowedRoles, true)) {
            $guard->logout();
            throw new \Exception('This account type is not allowed to use this login method.', 403);
        }

        if (!$user->is_active || $user->is_locked) {
            $guard->logout();
            throw new \Exception('Account disabled or locked', 403);
        }

        if (!$this->isDeviceAvailable($ip, $userAgent, $user->id)) {
            throw new \Exception('Device already in use. Please logout first before logging in with another account.', 403);
        }

        // Revoke all previous active devices (single active session)
        Device::where('user_id', $user->id)
              ->whereNull('revoked_at')
              ->update(['revoked_at' => now()]);

        $device = $this->checkDevice($user, $ip, $userAgent, $deviceName, $credentials['remember'] ?? false);

        $user->update([
            'last_login_at'    => now(),
            'last_activity_at' => now(),
        ]);

        $this->logSuccessfulLogin($user, $ip, $userAgent);

        if ($device && $device->wasRecentlyCreated) {
            $this->sendLoginNotification($user, $ip, $userAgent);
        }

        $this->clearFailedAttempts($email, $ip);

        $tokens = JwtTokenHelper::generateTokens($user);

        return [
            'tokens' => $tokens,
            'user'   => $user,
            'device' => $device,
        ];
    }

    private function checkRateLimit(string $email, string $ip): void
    {
        $ipKey    = "login_ip_{$ip}";
        $emailKey = "login_email_{$email}";

        $ipAttempts = Cache::get($ipKey, 0);
        if ($ipAttempts >= 20) {
            Cache::put("ip_locked_{$ip}", true, now()->addMinutes(15));
            throw new \Exception('Too many login attempts from your IP. Please try again after 15 minutes.');
        }

        $emailAttempts = Cache::get($emailKey, 0);
        if ($emailAttempts >= 10) {
            Cache::put("user_locked_{$email}", true, now()->addMinutes(30));
            throw new \Exception('Too many login attempts for this account. Please try again after 30 minutes.');
        }

        Cache::put($ipKey, $ipAttempts + 1, 900);
        Cache::put($emailKey, $emailAttempts + 1, 900);
    }

    private function isAccountLocked(string $email, string $ip): bool
    {
        if (Cache::get("user_locked_{$email}", false)) {
            return true;
        }

        if (Cache::get("ip_locked_{$ip}", false)) {
            return true;
        }

        $user = User::where('email', $email)->first();
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            return true;
        }

        return false;
    }

    private function handleVerification(?User $user, array $credentials): ?\Illuminate\Http\JsonResponse
    {
        if ($user && is_null($user->email_verified_at)) {
            return $this->handleUnverifiedUser($user);
        }

        if (!$user) {
            $registration = Registration::where('email', $credentials['email'])
                ->where('expires_at', '>', now())
                ->first();

            if ($registration && Hash::check($credentials['password'], $registration->password)) {
                return $this->handlePendingRegistration($registration);
            }
        }

        return null;
    }

    private function handleUnverifiedUser(User $user): \Illuminate\Http\JsonResponse
    {
        $registration = $this->getOrCreateRegistration($user);
        $this->generateAndSendOtp($registration, $user->first_name);
        return response()->json([
            'error'                  => 'Email not verified. Please check your email for OTP.',
            'requires_verification'  => true,
            'email'                  => $user->email,
            'message'                => 'OTP sent to your email'
        ], 403);
    }

    private function handlePendingRegistration(Registration $registration): \Illuminate\Http\JsonResponse
    {
        $this->generateAndSendOtp($registration, $registration->first_name);
        return response()->json([
            'error'                  => 'Email not verified. Please check your email for OTP.',
            'requires_verification'  => true,
            'email'                  => $registration->email,
            'message'                => 'OTP sent to your email'
        ], 403);
    }

    private function getOrCreateRegistration(User $user): Registration
    {
        $registration = Registration::where('email', $user->email)->first();
        if (!$registration) {
            $registration = Registration::create([
                'uuid'       => (string) Str::uuid(),
                'email'      => $user->email,
                'phone'      => $user->phone,
                'username'   => $user->username,
                'password'   => $user->password,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'role'       => $user->role,
                'expires_at' => now()->addMinutes(15),
            ]);
        }
        return $registration;
    }

    private function generateAndSendOtp(Registration $registration, string $firstName): void
    {
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
            Mail::to($registration->email)->send(new OtpMail($code, $firstName));
        } catch (\Exception $e) {
            Log::error('OTP send failed: ' . $e->getMessage());
        }
    }

    private function logFailedAttempt(string $email, string $ip, string $userAgent): void
    {
        LoginHistory::create([
            'email'      => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'status'     => 'failed',
            'attempts'   => Cache::get("login_attempts_{$email}", 0) + 1,
        ]);

        $attempts = Cache::get("login_attempts_{$email}", 0) + 1;
        Cache::put("login_attempts_{$email}", $attempts, 3600);

        if ($attempts >= 10) {
            Cache::put("user_locked_{$email}", true, now()->addMinutes(30));
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update(['locked_until' => now()->addMinutes(30)]);
            }
        }

        Log::warning('Failed login attempt', [
            'email'    => $email,
            'ip'       => $ip,
            'user_agent' => $userAgent,
            'attempts' => $attempts,
        ]);
    }

    private function logSuccessfulLogin(User $user, string $ip, string $userAgent): void
    {
        LoginHistory::create([
            'user_id'    => $user->id,
            'email'      => $user->email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'status'     => 'success',
            'attempts'   => 0,
        ]);
    }

    private function clearFailedAttempts(string $email, string $ip): void
    {
        Cache::forget("login_attempts_{$email}");
        Cache::forget("login_ip_{$ip}");
        Cache::forget("login_email_{$email}");
        Cache::forget("user_locked_{$email}");
        Cache::forget("ip_locked_{$ip}");
    }

    private function sendLoginNotification(User $user, string $ip, string $userAgent): void
    {
        try {
            Log::info('New device login', [
                'user'      => $user->email,
                'ip'        => $ip,
                'user_agent'=> $userAgent,
            ]);
        } catch (\Exception $e) {
            Log::error('Login notification failed: ' . $e->getMessage());
        }
    }
}