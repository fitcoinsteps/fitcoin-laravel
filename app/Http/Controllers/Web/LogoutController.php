<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTGuard;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        try {
            /** @var User|null $user */
            $user = $guard->user();

            if ($user) {
                // Revoke current device
                $fingerprint = hash('sha256', $request->ip() . $request->userAgent() . $user->id);
                Device::where('user_id', $user->id)
                    ->where('fingerprint', $fingerprint)
                    ->update(['revoked_at' => now()]);

                // Revoke all refresh tokens for this user
                if (Schema::hasTable('jwt_tokens')) {
                    DB::table('jwt_tokens')
                        ->where('user_id', $user->id)
                        ->update(['revoked' => true, 'revoked_at' => now()]);
                }
            }

            $guard->logout();

            // Clear both access and refresh cookies
            $accessCookie = cookie('jwt_token', '', -1);
            $refreshCookie = cookie('jwt_refresh_token', '', -1);

            return response()->json(['message' => 'Logged out successfully'])
                ->withCookie($accessCookie)
                ->withCookie($refreshCookie);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Already logged out'], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['error' => 'Logout failed. Please try again.'], 500);
        }
    }

    public function logoutAllDevices(Request $request)
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        try {
            /** @var User|null $user */
            $user = $guard->user();

            if ($user) {
                // Revoke all devices
                Device::where('user_id', $user->id)->update(['revoked_at' => now()]);

                // Revoke all refresh tokens
                if (Schema::hasTable('jwt_tokens')) {
                    DB::table('jwt_tokens')
                        ->where('user_id', $user->id)
                        ->update(['revoked' => true, 'revoked_at' => now()]);
                }
            }

            $guard->logout();

            // Clear both cookies
            $accessCookie = cookie('jwt_token', '', -1);
            $refreshCookie = cookie('jwt_refresh_token', '', -1);

            return response()->json(['message' => 'All sessions revoked successfully'])
                ->withCookie($accessCookie)
                ->withCookie($refreshCookie);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Already logged out'], 200);
        } catch (\Exception $e) {
            Log::error('Logout all devices error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to revoke all sessions.'], 500);
        }
    }
}