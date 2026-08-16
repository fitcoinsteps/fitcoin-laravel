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
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($provider);
        $socialUser = $driver->stateless()->user();

        $socialAccount = SocialAccount::where('provider', $provider)
                            ->where('provider_id', $socialUser->getId())
                            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;
        } else {
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $isFirstUser = false;
                DB::beginTransaction();
                try {
                    $userCount = User::lockForUpdate()->count();
                    $isFirstUser = $userCount === 0;
                    RoleService::createDefaultRolesIfNeeded();

                    $user = User::create([
                        'uuid'              => (string) Str::uuid(),
                        'username'          => $socialUser->getEmail(),
                        'first_name'        => $socialUser->user['given_name'] ?? $socialUser->getName(),
                        'last_name'         => $socialUser->user['family_name'] ?? '',
                        'email'             => $socialUser->getEmail(),
                        'email_verified_at' => now(),
                        'password'          => bcrypt(Str::random(32)),
                        'status'            => 'active',
                        'is_active'         => true,
                    ]);

                    $roleSlug = $isFirstUser ? 'super-admin' : 'users';
                    $role = Role::where('slug', $roleSlug)->first();
                    $user->roles()->attach($role->id, ['assigned_at' => now()]);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Social registration failed: ' . $e->getMessage());
                    $frontendUrl = config('app.frontend_url', '/login');
                    return redirect()->away($frontendUrl . '?error=registration_failed');
                }
            }

            $user->socialAccounts()->create([
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        $ip        = $request->ip();
        $userAgent = $request->userAgent();

        // ── Device availability check ──
        if (!$this->isDeviceAvailable($ip, $userAgent, $user->id)) {
            $frontendUrl = config('app.frontend_url', '/login');
            return redirect()->away($frontendUrl . '?error=device_in_use');
        }

        // ── Register/update device ──
        $this->checkDevice($user, $ip, $userAgent, 'Google OAuth', true);

        $user->update(['last_login_at' => now()]);

        $tokens = JwtTokenHelper::generateTokens($user);

        $frontendUrl = config('app.frontend_url', '/login');
        return redirect()->away(
            $frontendUrl . '?token=' . $tokens['access_token'] . '&refresh_token=' . ($tokens['refresh_token'] ?? '')
        );
    }
}