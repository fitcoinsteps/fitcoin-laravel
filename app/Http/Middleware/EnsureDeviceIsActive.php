<?php

namespace App\Http\Middleware;

use App\Models\Device;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTGuard;

class EnsureDeviceIsActive
{
    public function handle(Request $request, Closure $next)
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        /** @var User|null $user */
        $user = $guard->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Calculate fingerprint same as DeviceTrait
        $fingerprint = hash('sha256', $request->ip() . $request->userAgent() . $user->id);

        $device = Device::where('user_id', $user->id)
                        ->where('fingerprint', $fingerprint)
                        ->whereNull('revoked_at')
                        ->first();

        if (!$device) {
            // Revoke token and force re-login
            $guard->logout();
            return response()->json(['error' => 'Session expired. Please login again.'], 401);
        }

        return $next($request);
    }
}