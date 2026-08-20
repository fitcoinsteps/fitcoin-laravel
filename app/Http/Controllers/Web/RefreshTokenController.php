<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Helpers\JwtTokenHelper;
use Illuminate\Http\Request;

class RefreshTokenController extends Controller
{
    public function refresh(Request $request)
    {
        $refreshToken = $request->cookie('jwt_refresh_token');

        if (!$refreshToken) {
            return response()->json(['error' => 'Refresh token not found'], 401);
        }

        try {
            $token = JwtTokenHelper::refreshTokens($refreshToken);

            $accessCookie = cookie(
                'jwt_token',
                $token['access_token'],
                $token['expires_in'] / 60,
                '/',
                null,
                true,      // secure
                true,      // httpOnly
                false,     // raw
                'Strict'   // SameSite
            );

            return response()->json([
                'access_token' => $token['access_token'],
                'token_type'   => 'bearer',
                'expires_in'   => $token['expires_in'],
            ])->withCookie($accessCookie);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not refresh token'], 401);
        }
    }
}