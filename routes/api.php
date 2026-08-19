<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\PermissionController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\StepController;
use App\Http\Controllers\Api\TrackingSessionController;
use App\Http\Controllers\Api\FitcoinController;
use App\Http\Controllers\Api\GiftCardController;
use App\Http\Controllers\Api\CryptoWithdrawalController;
use App\Http\Middleware\CheckRole;

// ==================== PUBLIC ROUTES ====================
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

Route::get('auth/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback']);

// ✅ Refresh token - PUBLIC (outside auth middleware)
Route::post('refresh', [AuthController::class, 'refresh']);

// ==================== PROTECTED ROUTES (JWT Required) ====================
Route::middleware('jwt.auth')->group(function () {
    // ==================== AUTH ====================
    Route::post('logout', [LogoutController::class, 'logout']);
    Route::post('logout-all', [LogoutController::class, 'logoutAllDevices']);
    Route::get('me', [AuthController::class, 'me']);

    // ==================== LOGIN HISTORY & DEVICES ====================
    Route::get('login-history', [AuthController::class, 'loginHistory']);
    Route::get('devices', [AuthController::class, 'devices']);
    Route::delete('devices/{device}', [AuthController::class, 'revokeDevice']);
    Route::post('devices/{device}/trust', [AuthController::class, 'trustDevice']);

    // ==================== STEPS ====================
    Route::get('steps/today', [StepController::class, 'today']);
    Route::post('steps', [StepController::class, 'store']);
    Route::get('steps/history', [StepController::class, 'history']);

    // ==================== TRACKING SESSIONS ====================
    Route::post('tracking-sessions', [TrackingSessionController::class, 'store']);
    Route::get('tracking-sessions', [TrackingSessionController::class, 'index']);

    // ==================== FITCOIN ====================
    Route::get('fitcoins/balance', [FitcoinController::class, 'balance']);
    Route::post('fitcoins/convert', [FitcoinController::class, 'convert']);

    // ==================== GIFT CARDS ====================
    Route::get('gift-cards/providers', [GiftCardController::class, 'providers']);
    Route::post('gift-cards/redeem', [GiftCardController::class, 'redeem']);
    Route::get('gift-cards/history', [GiftCardController::class, 'history']);

    // ==================== CRYPTO WITHDRAWALS ====================
    Route::get('crypto/options', [CryptoWithdrawalController::class, 'options']);
    Route::post('crypto/withdraw', [CryptoWithdrawalController::class, 'request']);
    Route::get('crypto/history', [CryptoWithdrawalController::class, 'history']);

    // ==================== ADMIN ROUTES ====================
    Route::middleware([CheckRole::class . ':super-admin,admin'])
        ->prefix('admin')
        ->group(function () {
            // ==================== ROLES & PERMISSIONS ====================
            Route::apiResource('roles', RoleController::class);
            Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
            Route::apiResource('users', UserController::class);
            Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);
            Route::apiResource('permissions', PermissionController::class)->only(['index', 'show']);

            // ==================== ADMIN GIFT CARDS ====================
            Route::get('gift-cards', [\App\Http\Controllers\Admin\GiftCardController::class, 'index']);
            Route::post('gift-cards', [\App\Http\Controllers\Admin\GiftCardController::class, 'store']);
            Route::get('gift-cards/{giftCard}/edit', [\App\Http\Controllers\Admin\GiftCardController::class, 'edit']);
            Route::put('gift-cards/{giftCard}', [\App\Http\Controllers\Admin\GiftCardController::class, 'update']);
            Route::delete('gift-cards/{giftCard}', [\App\Http\Controllers\Admin\GiftCardController::class, 'destroy']);
            Route::get('gift-cards/redemptions', [\App\Http\Controllers\Admin\GiftCardController::class, 'redemptions']);
            Route::get('gift-cards/redemptions/{redemption}', [\App\Http\Controllers\Admin\GiftCardController::class, 'showRedemption']);
            Route::put('gift-cards/redemptions/{redemption}/complete', [\App\Http\Controllers\Admin\GiftCardController::class, 'completeRedemption']);
            Route::put('gift-cards/redemptions/{redemption}/cancel', [\App\Http\Controllers\Admin\GiftCardController::class, 'cancelRedemption']);
            Route::get('gift-cards/statistics', [\App\Http\Controllers\Admin\GiftCardController::class, 'statistics']);
            Route::get('gift-cards/bulk-upload', [\App\Http\Controllers\Admin\GiftCardController::class, 'bulkUpload']);
            Route::post('gift-cards/bulk-upload', [\App\Http\Controllers\Admin\GiftCardController::class, 'storeBulk']);
            Route::get('gift-cards/export', [\App\Http\Controllers\Admin\GiftCardController::class, 'export']);

            // ==================== ADMIN CRYPTO WITHDRAWALS ====================
            Route::get('crypto/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index']);
            Route::get('crypto/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'show']);
            Route::put('crypto/withdrawals/{withdrawal}/process', [\App\Http\Controllers\Admin\WithdrawalController::class, 'process']);
            Route::put('crypto/withdrawals/{withdrawal}/complete', [\App\Http\Controllers\Admin\WithdrawalController::class, 'complete']);
            Route::put('crypto/withdrawals/{withdrawal}/fail', [\App\Http\Controllers\Admin\WithdrawalController::class, 'fail']);
            Route::get('crypto/withdrawals/statistics', [\App\Http\Controllers\Admin\WithdrawalController::class, 'statistics']);
            Route::get('crypto/withdrawals/export', [\App\Http\Controllers\Admin\WithdrawalController::class, 'export']);
        });
});