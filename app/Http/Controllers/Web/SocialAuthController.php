<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    protected SocialAuthService $socialAuthService;

    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    public function redirect(string $provider)
    {
        return $this->socialAuthService->redirect($provider);
    }

    public function callback(string $provider, Request $request)
    {
        try {
            $result = $this->socialAuthService->handleCallback($provider, $request);

            // Access token cookie
            $accessCookie = cookie(
                'jwt_token',
                $result['tokens']['access_token'],
                $result['tokens']['expires_in'] / 60,
                '/',
                null,
                true,      // secure
                true,      // httpOnly
                false,     // raw
                'Strict'   // SameSite
            );

            // Refresh token cookie
            $refreshCookie = cookie(
                'jwt_refresh_token',
                $result['tokens']['refresh_token'],
                30 * 24 * 60,
                '/',
                null,
                true,      // secure
                true,      // httpOnly
                false,     // raw
                'Strict'   // SameSite
            );

            return redirect($result['redirect'])
                ->withCookie($accessCookie)
                ->withCookie($refreshCookie);
        } catch (\Exception $e) {
            return redirect('/login?error=social_auth_failed');
        }
    }
}