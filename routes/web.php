<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\GiftCardController;
use App\Http\Controllers\Admin\WithdrawalController;

// ==================== PUBLIC PAGES ====================
Route::get('/', function () {
    return view('website.home');
})->name('home');

Route::get('/about', function () {
    return view('website.views.aboutSection.about');
})->name('about');

Route::get('/contact', function () {
    return view('user.contact');
})->name('contact');

// ==================== AUTHENTICATION ====================
Route::get('/login', function () {
    return view('authentication.login');
})->name('login');

// ✅ Web login uses AuthController::webLogin (session-based)
Route::post('/login', [AuthController::class, 'webLogin'])->name('login');

// ✅ Web logout uses AuthController::webLogout (session-based)
Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

Route::get('/register', function () {
    return view('authentication.register');
})->name('register');

Route::get('/verifyOtp', function () {
    return view('authentication.verifyOtp');
})->name('verify.otp');

Route::get('/forgotPassword', function () {
    return view('authentication.ForgotPassword');
})->name('password.request');

Route::get('/resetPassword', function () {
    return view('authentication.resetPassword');
})->name('password.reset');

Route::get('/user/dashboard', function () {
    return view('user.dashboard');
})->name('user.dashboard');

Route::get('/dashboard', function () {
    return redirect('/user/dashboard');
})->name('dashboard');

// ==================== ADMIN DASHBOARD ====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super-admin,admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/activity', [AdminDashboardController::class, 'activity'])->name('dashboard.activity');

    // Gift Cards
    Route::prefix('gift-cards')->name('gift-cards.')->group(function () {
        Route::get('/', [GiftCardController::class, 'index'])->name('index');
        Route::get('/create', [GiftCardController::class, 'create'])->name('create');
        Route::post('/store', [GiftCardController::class, 'store'])->name('store');
        Route::get('/{giftCard}/edit', [GiftCardController::class, 'edit'])->name('edit');
        Route::put('/{giftCard}', [GiftCardController::class, 'update'])->name('update');
        Route::delete('/{giftCard}', [GiftCardController::class, 'destroy'])->name('destroy');
        Route::get('/bulk-upload', [GiftCardController::class, 'bulkUpload'])->name('bulk-upload');
        Route::post('/bulk-upload', [GiftCardController::class, 'storeBulk'])->name('store-bulk');
        Route::get('/export', [GiftCardController::class, 'export'])->name('export');
    });

    // Redemptions
    Route::prefix('redemptions')->name('redemptions.')->group(function () {
        Route::get('/', [GiftCardController::class, 'redemptions'])->name('index');
        Route::get('/{redemption}', [GiftCardController::class, 'showRedemption'])->name('show');
        Route::put('/{redemption}/complete', [GiftCardController::class, 'completeRedemption'])->name('complete');
        Route::put('/{redemption}/cancel', [GiftCardController::class, 'cancelRedemption'])->name('cancel');
    });

    // Withdrawals
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'index'])->name('index');
        Route::get('/{withdrawal}', [WithdrawalController::class, 'show'])->name('show');
        Route::put('/{withdrawal}/process', [WithdrawalController::class, 'process'])->name('process');
        Route::put('/{withdrawal}/complete', [WithdrawalController::class, 'complete'])->name('complete');
        Route::put('/{withdrawal}/fail', [WithdrawalController::class, 'fail'])->name('fail');
        Route::get('/export', [WithdrawalController::class, 'export'])->name('export');
    });
});

Route::fallback(function () {
    return redirect('/');
});