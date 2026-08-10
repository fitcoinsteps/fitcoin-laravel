<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\PermissionController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\Api\SocialAuthController;

// ── Public routes (no JWT) ──
Route::post('register', [RegisterController::class, 'register']);
Route::post('verify-otp', [VerificationController::class, 'verify']);
Route::post('resend-otp', [VerificationController::class, 'resend']);
Route::post('login', [AuthController::class, 'login']);

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::post('send-otp', [VerificationController::class, 'sendOtp']);
Route::post('verify-token', [VerificationController::class, 'verifyWithToken']);
Route::get('check-otp-status', [VerificationController::class, 'checkStatus']);
Route::delete('revoke-otp', [VerificationController::class, 'revokeOtp']);

// ✅ Social Auth routes – public
Route::get('auth/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback']);

// ── Protected routes (require JWT) ──
Route::middleware('jwt.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    Route::post('revoke-all-sessions', [AuthController::class, 'revokeAllSessions']);
    Route::get('login-history', [AuthController::class, 'loginHistory']);
    Route::get('devices', [AuthController::class, 'devices']);
    Route::delete('devices/{device}', [AuthController::class, 'revokeDevice']);
    Route::post('devices/{device}/trust', [AuthController::class, 'trustDevice']);

    Route::middleware([CheckRole::class . ':super-admin,admin'])->prefix('admin')->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
        Route::apiResource('users', UserController::class);
        Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);
        Route::apiResource('permissions', PermissionController::class)->only(['index', 'show']);
    });
});