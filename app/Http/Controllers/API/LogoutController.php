<?php

namespace App\Http\Controllers\Api;

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

    /**
     * Logout the current device and invalidate the token.
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->authUser();

            if ($user) {
                // Revoke the current device so a new login is allowed
                $fingerprint = hash('sha256', $request->ip() . $request->userAgent() . $user->id);
                Device::where('user_id', $user->id)
                    ->where('fingerprint', $fingerprint)
                    ->update(['revoked_at' => now()]);
            }

            // Invalidate the current JWT token (blacklist)
            $this->guard()->logout();

            return response()->json(['message' => 'Logged out successfully']);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Already logged out'], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['error' => 'Logout failed. Please try again.'], 500);
        }
    }

    /**
     * Logout from all devices (revoke all tokens and devices).
     */
    public function logoutAllDevices(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->authUser();

            if ($user) {
                // Revoke all device records
                Device::where('user_id', $user->id)->update(['revoked_at' => now()]);

                // Revoke all JWT tokens in the database (if table exists)
                if (Schema::hasTable('jwt_tokens')) {
                    DB::table('jwt_tokens')->where('user_id', $user->id)->update(['revoked' => true]);
                }
            }

            // Invalidate the current token as well
            $this->guard()->logout();

            return response()->json(['message' => 'All sessions revoked successfully']);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Already logged out'], 200);
        } catch (\Exception $e) {
            Log::error('Logout all devices error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to revoke all sessions.'], 500);
        }
    }
}