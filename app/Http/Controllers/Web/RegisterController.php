<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6|confirmed',
            'phone'      => 'nullable|string',
        ]);

        $role = User::count() === 0 ? User::ROLE_SUPER_ADMIN : User::ROLE_USER;

        $this->registrationService->createRegistration($data, $role);

        return response()->json([
            'message' => 'OTP sent to your email',
            'email'   => $data['email'],
            'redirect' => '/verify-otp?email=' . urlencode($data['email'])
        ], 201);
    }
}