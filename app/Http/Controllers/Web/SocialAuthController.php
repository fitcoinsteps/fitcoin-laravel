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

            $cookie = cookie(
                'jwt_token',
                $result['tokens']['access_token'],
                $result['tokens']['expires_in'] / 60,
                '/',
                null,
                true,
                true
            );

            return redirect($result['redirect'])->withCookie($cookie);
        } catch (\Exception $e) {
            return redirect('/login?error=social_auth_failed');
        }
    }
}