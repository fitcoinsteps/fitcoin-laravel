<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Role;
use App\Helpers\JwtTokenHelper;
use App\Services\RoleService;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    /**
     * Redirect to the provider (Google / Apple)
     */
    public function redirect($provider)
    {
        // For Apple you may need to set up a custom provider in Socialite
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Handle callback from the provider
     */
    public function callback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        // Find existing social account or create new user
        $socialAccount = SocialAccount::where('provider', $provider)
                            ->where('provider_id', $socialUser->getId())
                            ->first();

        if ($socialAccount) {
            // Existing social login
            $user = $socialAccount->user;
        } else {
            // Check if a user already exists with this email
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Create a new user
                $isFirstUser = User::count() === 0;
                RoleService::createDefaultRolesIfNeeded(); // ensure roles exist

                $user = User::create([
                    'uuid'              => (string) Str::uuid(),
                    'username'          => $socialUser->getEmail(),
                    'first_name'        => $socialUser->user['given_name'] ?? $socialUser->getName(),
                    'last_name'         => $socialUser->user['family_name'] ?? '',
                    'email'             => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    'password'          => bcrypt(Str::random(32)), // random password
                    'status'            => 'active',
                    'is_active'         => true,
                ]);

                // Assign role: first user = super-admin, else = users
                $roleSlug = $isFirstUser ? 'super-admin' : 'users';
                $role = Role::where('slug', $roleSlug)->first();
                $user->roles()->attach($role->id, ['assigned_at' => now()]);
            }

            // Link the social account
            $user->socialAccounts()->create([
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Generate JWT tokens (just like normal login)
        $tokens = JwtTokenHelper::generateTokens($user);

        return response()->json($tokens);
    }
}