<?php

namespace App\Http\Controllers\Api;

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
     * POST /api/account/deactivate
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

        return response()->json([
            'message' => 'Account deactivated. You can reactivate it by logging in again.',
        ]);
    }
}