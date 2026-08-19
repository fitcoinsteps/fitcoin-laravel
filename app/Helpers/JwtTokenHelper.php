<?php

namespace App\Helpers;

use App\Models\JwtToken;
use App\Models\User;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtTokenHelper
{
    /**
     * Generate JWT token for a user (with refresh token storage).
     * 
     * @param User $user
     * @return array
     */
    public static function generateTokens(User $user): array
    {
        // Generate access token
        $accessToken = JWTAuth::fromUser($user);
        
        // Generate refresh token with custom claims
        $refreshJti = (string) Str::uuid();
        $refreshToken = JWTAuth::fromUser($user, ['exp' => now()->addDays(30)->timestamp]);
        
        // Store refresh token in database
        JwtToken::create([
            'user_id' => $user->id,
            'token_id' => $refreshJti,
            'token_type' => 'refresh',
            'device_info' => request()->header('User-Agent', 'Unknown'),
            'ip_address' => request()->ip(),
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);
        
        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    /**
     * Simple token generation without refresh token.
     * 
     * @param User $user
     * @return array
     */
    public static function generateSimpleTokens(User $user): array
    {
        $token = JWTAuth::fromUser($user);
        
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    /**
     * Refresh the token - handles expired tokens too.
     * 
     * @return array
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     * @throws JWTException
     */
    public static function refreshTokens(): array
    {
        try {
            // Try to refresh the token (works even if expired)
            $token = JWTAuth::refresh();
            
            return [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ];
        } catch (TokenExpiredException $e) {
            // Token is expired - try to refresh anyway
            // JWTAuth::refresh() should handle this
            throw $e;
        } catch (TokenInvalidException $e) {
            throw $e;
        } catch (JWTException $e) {
            throw $e;
        }
    }

    /**
     * Refresh with revoke: revoke old refresh token, issue new pair.
     * 
     * @param string $oldJti
     * @param User $user
     * @return array
     */
    public static function refreshWithRevoke(string $oldJti, User $user): array
    {
        self::revokeRefreshToken($oldJti);
        return self::generateTokens($user);
    }

    /**
     * Revoke a refresh token by its jti.
     * 
     * @param string $jti
     * @return void
     */
    public static function revokeRefreshToken(string $jti): void
    {
        JwtToken::where('token_id', $jti)->update([
            'revoked' => true,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Validate that a refresh token exists in DB and is not revoked/expired.
     * 
     * @param string $jti
     * @return bool
     */
    public static function isRefreshTokenValid(string $jti): bool
    {
        $token = JwtToken::where('token_id', $jti)->first();
        return $token && !$token->revoked && $token->expires_at->isFuture();
    }

    /**
     * Get the user from a token.
     * 
     * @param string $token
     * @return User|null
     */
    public static function getUserFromToken(string $token): ?User
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload->get('sub');
            return User::find($userId);
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Get the jti from a token.
     * 
     * @param string $token
     * @return string|null
     */
    public static function getJtiFromToken(string $token): ?string
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            return $payload->get('jti');
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Invalidate/blacklist a token.
     * 
     * @param string $token
     * @return bool
     */
    public static function invalidateToken(string $token): bool
    {
        try {
            JWTAuth::setToken($token)->invalidate();
            return true;
        } catch (JWTException $e) {
            return false;
        }
    }

    /**
     * Clean up expired or revoked tokens from database.
     * 
     * @return int Number of deleted tokens
     */
    public static function cleanupTokens(): int
    {
        return JwtToken::where('expires_at', '<', now())
            ->orWhere('revoked', true)
            ->delete();
    }

    /**
     * Get the current authenticated user.
     * 
     * @return User|null
     */
    public static function getAuthenticatedUser(): ?User
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Check if token is valid.
     * 
     * @param string $token
     * @return bool
     */
    public static function isTokenValid(string $token): bool
    {
        try {
            JWTAuth::setToken($token)->authenticate();
            return true;
        } catch (JWTException $e) {
            return false;
        }
    }
}