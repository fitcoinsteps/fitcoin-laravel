<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Role;
use App\Helpers\JwtTokenHelper;
use App\Services\RoleService;
use App\Traits\DeviceTrait;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SocialAuthController extends Controller
{
    use DeviceTrait;

    public function redirect(string $provider): RedirectResponse
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($provider);
        return $driver->stateless()->redirect();
    }

    public function callback(string $provider, Request $request): RedirectResponse
    {
        try {
            /** @var AbstractProvider $driver */
            $driver = Socialite::driver($provider);
            $socialUser = $driver->stateless()->user();

            $email = $socialUser->getEmail();
            $firstName = $socialUser->user['given_name'] ?? $socialUser->getName() ?? '';
            $lastName = $socialUser->user['family_name'] ?? '';

            if (!$email) {
                $email = $socialUser->getId() . '@apple.user';
            }

            $socialAccount = SocialAccount::where('provider', $provider)
                                ->where('provider_id', $socialUser->getId())
                                ->first();

            if ($socialAccount) {
                $user = $socialAccount->user;
            } else {
                $user = User::where('email', $email)->first();

                if (!$user) {
                    DB::beginTransaction();
                    try {
                        RoleService::createDefaultRolesIfNeeded();

                        $user = User::create([
                            'uuid'              => (string) Str::uuid(),
                            'username'          => $email,
                            'first_name'        => $firstName,
                            'last_name'         => $lastName,
                            'email'             => $email,
                            'email_verified_at' => now(),
                            'password'          => bcrypt(Str::random(32)),
                            'status'            => 'active',
                            'is_active'         => true,
                        ]);

                        // Always assign 'users' role for social auth (not super-admin)
                        $role = Role::where('slug', 'users')->first();
                        if ($role) {
                            $user->roles()->attach($role->id, ['assigned_at' => now()]);
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Social registration failed: ' . $e->getMessage());
                        return redirect()->to('/login?error=registration_failed');
                    }
                }

                $user->socialAccounts()->create([
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
            }

            if (!$user->is_active || $user->is_locked) {
                return redirect()->to('/login?error=account_disabled');
            }

            $ip        = $request->ip();
            $userAgent = $request->userAgent();

            if (!$this->isDeviceAvailable($ip, $userAgent, $user->id)) {
                return redirect()->to('/login?error=device_in_use');
            }

            $deviceName = ucfirst($provider) . ' OAuth';
            $this->checkDevice($user, $ip, $userAgent, $deviceName, true);

            $user->update([
                'last_login_at'    => now(),
                'last_activity_at' => now(),
            ]);

            $tokens = JwtTokenHelper::generateTokens($user);

            // Redirect to login page with token - login page will handle redirect to /user/dashboard
            return redirect()->to('/login?token=' . $tokens['access_token'] . '&refresh_token=' . ($tokens['refresh_token'] ?? ''));

        } catch (\Exception $e) {
            Log::error('Social auth callback failed: ' . $e->getMessage());
            return redirect()->to('/login?error=social_auth_failed');
        }
    }
}