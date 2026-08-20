<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use App\Helpers\JwtTokenHelper;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    protected PasswordResetService $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = $this->passwordResetService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if (!$result['status']) {
            return response()->json(['error' => $result['message']], 400);
        }

        $user = $result['user'];
        $tokens = JwtTokenHelper::generateTokens($user);

        LoginHistory::create([
            'user_id'    => $user->id,
            'email'      => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => 'success',
            'attempts'   => 0,
        ]);

        $cookie = cookie(
            'jwt_token',
            $tokens['access_token'],
            $tokens['expires_in'] / 60,
            '/',
            null,
            true,
            true
        );

        return response()->json([
            'message'      => 'Password reset successfully.',
            'access_token' => $tokens['access_token'],
            'token_type'   => 'bearer',
            'expires_in'   => $tokens['expires_in'],
            'user'         => $user,
        ])->withCookie($cookie);
    }
}