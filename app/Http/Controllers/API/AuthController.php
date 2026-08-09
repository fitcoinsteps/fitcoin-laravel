<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\JwtTokenHelper;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth('api')->user();

        if (!$user->is_active || $user->is_locked) {
            auth('api')->logout();
            return response()->json(['error' => 'Account disabled or locked'], 403);
        }

        $user->update(['last_login_at' => now()]);

        $tokens = JwtTokenHelper::generateTokens($user);

        return response()->json($tokens);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        $payload = auth('api')->payload();
        if ($payload->get('token_type') === 'refresh') {
            JwtTokenHelper::revokeRefreshToken($payload->get('jti'));
        }
        auth('api')->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function refresh()
    {
        $payload = auth('api')->payload();
        if ($payload->get('token_type') !== 'refresh') {
            return response()->json(['error' => 'Invalid token type'], 400);
        }

        $jti = $payload->get('jti');
        if (!JwtTokenHelper::isRefreshTokenValid($jti)) {
            return response()->json(['error' => 'Refresh token invalid or expired'], 401);
        }

        $user = auth('api')->user();
        $tokens = JwtTokenHelper::refreshTokens($jti, $user);

        return response()->json($tokens);
    }
}