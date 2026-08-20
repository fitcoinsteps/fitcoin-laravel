<?php

namespace App\Http\Controllers\Api;

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
            // Restrict API login to 'user' role only
            $result = $this->authService->attemptLogin($credentials, $request, ['user']);

            return response()->json([
                'access_token'  => $result['tokens']['access_token'],
                'refresh_token' => $result['tokens']['refresh_token'],
                'token_type'    => 'bearer',
                'expires_in'    => $result['tokens']['expires_in'],
                'user'          => $result['user'],
                'device'        => $result['device'] ? [
                    'id'         => $result['device']->id,
                    'name'       => $result['device']->device_name,
                    'is_trusted' => $result['device']->is_trusted,
                ] : null,
            ]);

        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return response()->json(['error' => $e->getMessage()], $status);
        }
    }
}