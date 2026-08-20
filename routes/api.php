<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RefreshTokenController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\StepController;
use App\Http\Controllers\Api\FitcoinController;
use App\Http\Controllers\Api\ProfileController;

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [RegisterController::class, 'register']);
Route::post('verify-otp', [VerificationController::class, 'verify']);
Route::post('resend-otp', [VerificationController::class, 'resend']);
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [ResetPasswordController::class, 'reset']);

Route::post('send-otp', [VerificationController::class, 'sendOtp']);
Route::post('verify-token', [VerificationController::class, 'verifyWithToken']);
Route::get('check-otp-status', [VerificationController::class, 'checkStatus']);
Route::delete('revoke-otp', [VerificationController::class, 'revokeOtp']);

Route::post('auth/{provider}/mobile', [SocialAuthController::class, 'handleApiCallback']);

// ✅ Refresh route must be public
Route::post('refresh', [RefreshTokenController::class, 'refresh']);

Route::middleware(['jwt.auth', 'device.active'])->group(function () {
    Route::post('logout', [LogoutController::class, 'logout']);
    Route::post('logout-all', [LogoutController::class, 'logoutAllDevices']);
    Route::get('me', [MeController::class, 'me']);

    Route::get('devices', [DeviceController::class, 'index']);
    Route::delete('devices/{device}', [DeviceController::class, 'revoke']);
    Route::post('devices/{device}/trust', [DeviceController::class, 'trust']);

    Route::get('steps/today', [StepController::class, 'today']);
    Route::post('steps', [StepController::class, 'sync']);
    Route::post('steps/goal', [StepController::class, 'updateGoal']);

    Route::get('fitcoin/balance', [FitcoinController::class, 'balance']);
    Route::post('fitcoin/convert', [FitcoinController::class, 'convert']);

    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar']);
});