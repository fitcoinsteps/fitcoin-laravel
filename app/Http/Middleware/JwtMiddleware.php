<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTGuard;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // 1. Get token from Authorization header or cookie
            $token = $request->bearerToken();
            if (!$token && $request->hasCookie('jwt_token')) {
                $token = $request->cookie('jwt_token');
            }

            if (!$token) {
                throw new JWTException('Token not provided');
            }

            // 2. Set the token on the api guard, then authenticate
            /** @var JWTGuard $guard */
            $guard = auth('api');
            $guard->setToken($token);
            $user = $guard->authenticate();

            if (!$user) {
                throw new JWTException('User not found');
            }

            // 3. Set the authenticated user on the guard (already done by authenticate, but ensure)
            $guard->setUser($user);

            // 4. Merge into request for convenience
            $request->merge(['auth_user' => $user]);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}