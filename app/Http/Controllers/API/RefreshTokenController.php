<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\JwtTokenHelper;
use Illuminate\Http\Request;

class RefreshTokenController extends Controller
{
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        try {
            $token = JwtTokenHelper::refreshTokens($request->refresh_token);

            return response()->json([
                'access_token' => $token['access_token'],
                'token_type'   => 'bearer',
                'expires_in'   => $token['expires_in'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not refresh token'], 401);
        }
    }
}