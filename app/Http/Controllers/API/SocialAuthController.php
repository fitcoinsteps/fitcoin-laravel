<?php

namespace App\Http\Controllers\Api;

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

    public function handleApiCallback(Request $request, string $provider)
    {
        $request->validate(['token' => 'required|string']);

        try {
            $result = $this->socialAuthService->handleApiCallback($provider, $request->token);

            return response()->json([
                'access_token' => $result['tokens']['access_token'],
                'token_type'   => 'bearer',
                'expires_in'   => $result['tokens']['expires_in'],
                'user'         => $result['user'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}