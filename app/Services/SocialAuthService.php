<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAccount;
use App\Helpers\JwtTokenHelper;
use App\Traits\DeviceTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class SocialAuthService
{
    use DeviceTrait;

    public function redirect(string $provider)
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($provider);
        return $driver->stateless()->redirect();
    }

    public function handleCallback(string $provider, $request)
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($provider);
        $socialUser = $driver->stateless()->user();

        $user = $this->findOrCreateUser($socialUser, $provider);

        // Perform device checks and update login timestamps
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        if (!$this->isDeviceAvailable($ip, $userAgent, $user->id)) {
            throw new \Exception('Device already in use.');
        }

        $deviceName = ucfirst($provider) . ' OAuth';
        $this->checkDevice($user, $ip, $userAgent, $deviceName, true);

        $user->update([
            'last_login_at'    => now(),
            'last_activity_at' => now(),
        ]);

        $tokens = JwtTokenHelper::generateTokens($user);

        return [
            'tokens' => $tokens,
            'user'   => $user,
            'redirect' => '/user/dashboard', // web redirect
        ];
    }

    public function handleApiCallback(string $provider, string $token)
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($provider);
        $socialUser = $driver->userFromToken($token);

        $user = $this->findOrCreateUser($socialUser, $provider);

        // For API, we can skip device tracking for simplicity, but update login
        $user->update([
            'last_login_at'    => now(),
            'last_activity_at' => now(),
        ]);

        $tokens = JwtTokenHelper::generateTokens($user);

        return [
            'tokens' => $tokens,
            'user'   => $user,
        ];
    }

    private function findOrCreateUser($socialUser, string $provider): User
    {
        $email = $socialUser->getEmail();
        $firstName = $socialUser->user['given_name'] ?? $socialUser->getName() ?? 'Social';
        $lastName = $socialUser->user['family_name'] ?? '';

        if (!$email) {
            $email = $socialUser->getId() . '@' . $provider . '.user';
        }

        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            return $socialAccount->user;
        }

        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            // Link social account but do not change role
            SocialAccount::create([
                'user_id'     => $existingUser->id,
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
            return $existingUser;
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'uuid'              => (string) Str::uuid(),
                'username'          => $email,
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => $email,
                'email_verified_at' => now(),
                'password'          => Hash::make(Str::random(32)),
                'role'              => User::ROLE_USER, // always user
                'status'            => 'active',
                'is_active'         => true,
            ]);

            SocialAccount::create([
                'user_id'     => $user->id,
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Social user creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}