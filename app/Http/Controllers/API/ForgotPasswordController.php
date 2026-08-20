<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    protected PasswordResetService $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $result = $this->passwordResetService->sendResetOtp($request->email);

        if (!$result['status']) {
            return response()->json(['message' => $result['message']], 200);
        }

        return response()->json([
            'message' => 'OTP sent to your email.',
            'email'   => $result['email'],
            'token'   => $result['token'],
        ]);
    }
}