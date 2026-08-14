<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\JwtTokenHelper;
use App\Models\Registration;
use App\Models\VerificationCode;
use App\Models\LoginHistory;
use App\Models\Device;
use App\Models\User;
use App\Mail\OtpMail;
use App\Traits\DeviceTrait;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTGuard;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use DeviceTrait;

    private function guard(): JWTGuard
    {
        return auth('api');
    }

    private function authUser(): ?User
    {
        /** @var User|null $user */
        $user = $this->guard()->user();
        return $user;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string|min:6',
            'remember'    => 'nullable|boolean',
            'device_name' => 'nullable|string|max:255',
        ]);

        $email      = $credentials['email'];
        $ip         = $request->ip();
        $userAgent  = $request->userAgent();
        $deviceName = $credentials['device_name'] ?? 'Unknown Device';

        if ($this->isAccountLocked($email, $ip)) {
            return response()->json([
                'error' => 'Account temporarily locked due to too many failed attempts. Please try again later.'
            ], 403);
        }

        try {
            $this->checkRateLimit($email, $ip);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 429);
        }

        $user = User::where('email', $email)->first();

        $verificationNeeded = $this->handleVerification($user, $credentials);
        if ($verificationNeeded) {
            return $verificationNeeded;
        }

        if (!$token = $this->guard()->attempt($credentials)) {
            $this->logFailedAttempt($email, $ip, $userAgent);
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        /** @var User $user */
        $user = $this->authUser();

        if (!$user->is_active || $user->is_locked) {
            $this->guard()->logout();
            return response()->json(['error' => 'Account disabled or locked'], 403);
        }

        if (!$this->isDeviceAvailable($ip, $userAgent, $user->id)) {
            return response()->json([
                'error' => 'Device already in use. Please logout first before logging in with another account.'
            ], 403);
        }

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

        auth()->login($user);

        $redirectUrl = $this->getRedirectUrl($user);

        return response()->json([
            'access_token' => $tokens['access_token'],
            'token_type'   => 'bearer',
            'expires_in'   => $tokens['expires_in'],
            'user'         => $user,
            'device'       => $device ? [
                'id'        => $device->id,
                'name'      => $device->device_name,
                'is_trusted'=> $device->is_trusted,
            ] : null,
            'redirect_url' => $redirectUrl,
        ]);
    }

    private function getRedirectUrl(User $user): string
    {
        if ($user->hasRole('super-admin')) {
            return '/super-admin/dashboard';
        }
        
        if ($user->hasRole('admin')) {
            return '/admin/dashboard';
        }
        
        return '/dashboard';
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

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->authUser();

            if ($user) {
                $fingerprint = hash('sha256', $request->ip() . $request->userAgent() . $user->id);
                Device::where('user_id', $user->id)
                    ->where('fingerprint', $fingerprint)
                    ->update(['revoked_at' => now()]);
            }

            $this->guard()->logout();
            
            auth()->logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Already logged out'], 200);
        }
    }

    public function revokeAllSessions(): \Illuminate\Http\JsonResponse
    {
        $user = $this->authUser();

        if ($user) {
            Device::where('user_id', $user->id)->update(['revoked_at' => now()]);
            DB::table('jwt_tokens')->where('user_id', $user->id)->update(['revoked' => true]);
        }

        $this->guard()->logout();
        auth()->logout();

        return response()->json([
            'message' => 'All sessions revoked successfully'
        ]);
    }

    public function loginHistory(Request $request): \Illuminate\Http\JsonResponse
    {
        $user  = $this->authUser();
        $limit = $request->get('limit', 20);

        $history = LoginHistory::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($history);
    }

    public function devices(): \Illuminate\Http\JsonResponse
    {
        $user = $this->authUser();
        $devices = Device::where('user_id', $user->id)
            ->orderBy('last_used_at', 'desc')
            ->get();

        return response()->json($devices);
    }

    public function revokeDevice(int $deviceId): \Illuminate\Http\JsonResponse
    {
        $user = $this->authUser();
        $device = Device::where('user_id', $user->id)
            ->where('id', $deviceId)
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $device->update(['revoked_at' => now()]);

        return response()->json(['message' => 'Device revoked successfully']);
    }

    public function trustDevice(int $deviceId): \Illuminate\Http\JsonResponse
    {
        $user = $this->authUser();
        $device = Device::where('user_id', $user->id)
            ->where('id', $deviceId)
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $device->update(['is_trusted' => true]);

        return response()->json(['message' => 'Device trusted successfully']);
    }

    public function forgotPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'If the email exists, a reset link will be sent.'], 200);
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
            Log::info("Password reset OTP sent to {$user->email}");
        } catch (\Exception $e) {
            Log::error('Password reset OTP email failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
        }

        return response()->json([
            'message' => 'OTP sent to your email.',
            'email'   => $user->email,
            'type'    => 'password_reset',
            'token'   => $token,
        ], 200);
    }

    public function resetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $verification = VerificationCode::where('token', $request->token)
            ->where('type', 'password_reset')
            ->where('expires_at', '>', now())
            ->where('is_revoked', false)
            ->whereNull('used_at')
            ->first();

        if (!$verification) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || $user->id != $verification->user_id) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $verification->update(['used_at' => now()]);

        Device::where('user_id', $user->id)->update(['revoked_at' => now()]);

        $token = JwtTokenHelper::generateTokens($user);

        $this->logSuccessfulLogin($user, $request->ip(), $request->userAgent());

        auth()->login($user);

        return response()->json([
            'message'      => 'Password reset successfully.',
            'access_token' => $token['access_token'],
            'token_type'   => 'bearer',
            'expires_in'   => $token['expires_in'],
            'user'         => $user,
        ]);
    }

    public function me(): \Illuminate\Http\JsonResponse
    {
        $user = $this->authUser();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->load(['roles', 'permissions', 'devices']);

        return response()->json($user);
    }

    public function refresh(): \Illuminate\Http\JsonResponse
    {
        try {
            $token = JwtTokenHelper::refreshTokens();
            return response()->json([
                'access_token' => $token['access_token'],
                'token_type'   => 'bearer',
                'expires_in'   => $token['expires_in'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not refresh token'], 401);
        }
    }
}