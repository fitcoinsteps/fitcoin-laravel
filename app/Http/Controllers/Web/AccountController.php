<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTGuard;

class AccountController extends Controller
{
    protected AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * POST /account/deactivate
     */
    public function deactivate(Request $request)
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        /** @var User|null $user */
        $user = $guard->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->accountService->deactivateAccount($user);

        // Invalidate current token
        $guard->logout();

        // Clear cookies
        $accessCookie = cookie('jwt_token', '', -1);
        $refreshCookie = cookie('jwt_refresh_token', '', -1);

        return response()->json([
            'message' => 'Account deactivated. You can reactivate it by logging in again.',
        ])->withCookie($accessCookie)->withCookie($refreshCookie);
    }
}