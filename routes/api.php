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
use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\AccountController;

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

    // Friendship routes
    Route::get('friends', [FriendshipController::class, 'index']);
    Route::post('friends/request', [FriendshipController::class, 'sendRequest']);
    Route::post('friends/accept', [FriendshipController::class, 'accept']);
    Route::post('friends/reject', [FriendshipController::class, 'reject']);
    Route::get('friends/requests', [FriendshipController::class, 'pending']);
    Route::delete('friends/{friendId}', [FriendshipController::class, 'remove']);
    Route::get('users/search', [FriendshipController::class, 'searchUsers']);

    // Challenge routes (user CRUD + activation)
    Route::get('challenges', [ChallengeController::class, 'index']);
    Route::post('challenges', [ChallengeController::class, 'store']);
    Route::put('challenges/{id}', [ChallengeController::class, 'update']);
    Route::delete('challenges/{id}', [ChallengeController::class, 'destroy']);
    Route::post('challenges/{id}/activate', [ChallengeController::class, 'activate']);
    Route::get('challenges/active', [ChallengeController::class, 'active']);
    Route::get('challenges/history', [ChallengeController::class, 'history']);
    Route::get('challenges/{id}/progress', [ChallengeController::class, 'checkProgress']);

    Route::post('account/deactivate', [AccountController::class, 'deactivate']);

});