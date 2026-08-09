<?php

namespace App\Helpers;

use App\Models\JwtToken;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtTokenHelper
{
    /**
     * Generate access + refresh tokens and store the refresh token in DB.
     */
    public static function generateTokens($user): array
    {
        $refreshTtl = config('jwt.refresh_ttl', 20160); // 2 weeks
        $refreshJti = (string) Str::uuid();

        // Access token (short-lived)
        $accessToken = auth('api')->claims([
            'token_type' => 'access'
        ])->login($user);

        // Refresh token (long-lived) with unique jti
        $refreshToken = auth('api')->claims([
            'token_type' => 'refresh',
            'jti' => $refreshJti,
        ])->setTTL($refreshTtl)->login($user);

        // Store in DB for revocation checks
        JwtToken::create([
            'user_id' => $user->id,
            'token_id' => $refreshJti,
            'token_type' => 'refresh',
            'device_info' => request()->header('User-Agent'),
            'ip_address' => request()->ip(),
            'expires_at' => now()->addMinutes($refreshTtl),
            'revoked' => false,
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    /**
     * Revoke a refresh token by its jti.
     */
    public static function revokeRefreshToken(string $jti): void
    {
        JwtToken::where('token_id', $jti)->update([
            'revoked' => true,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Refresh flow: revoke old refresh token, issue new pair.
     */
    public static function refreshTokens(string $oldJti, $user): array
    {
        self::revokeRefreshToken($oldJti);
        return self::generateTokens($user);
    }

    /**
     * Validate that a refresh token exists in DB and is not revoked/expired.
     */
    public static function isRefreshTokenValid(string $jti): bool
    {
        $token = JwtToken::where('token_id', $jti)->first();
        return $token && !$token->revoked && $token->expires_at->isFuture();
    }
}