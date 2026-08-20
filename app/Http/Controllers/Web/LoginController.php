<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string|min:6',
            'remember'    => 'nullable|boolean',
            'device_name' => 'nullable|string|max:255',
        ]);

        try {
            $result = $this->authService->attemptLogin($credentials, $request);

            // Access token cookie (short-lived)
            $accessCookie = cookie(
                'jwt_token',
                $result['tokens']['access_token'],
                $result['tokens']['expires_in'] / 60,
                '/',
                null,
                true,
                true
            );

            // Refresh token cookie (long-lived, 30 days)
            $refreshCookie = cookie(
                'jwt_refresh_token',
                $result['tokens']['refresh_token'],
                30 * 24 * 60,
                '/',
                null,
                true,
                true
            );

            return response()->json([
                'message' => 'Login successful',
                'user'    => $result['user'],
            ])->withCookie($accessCookie)->withCookie($refreshCookie);

        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }
}