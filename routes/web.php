<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\LogoutController;
use App\Http\Controllers\Web\RefreshTokenController;
use App\Http\Controllers\Web\ForgotPasswordController;
use App\Http\Controllers\Web\ResetPasswordController;
use App\Http\Controllers\Web\RegisterController;
use App\Http\Controllers\Web\SocialAuthController;
use App\Http\Controllers\Web\VerificationController;
use App\Http\Controllers\Web\MeController;
use App\Http\Controllers\Web\DeviceController;
use App\Http\Controllers\Web\FitcoinController;
use App\Http\Controllers\Web\StepController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\FriendshipController;
use App\Http\Controllers\Web\ChallengeController;
use App\Http\Controllers\Web\AccountController;

Route::get('/', function () {
    return view('website.home');
})->name('home');

Route::get('/about', function () {
    return view('website.views.aboutSection.about');
})->name('about');

Route::get('/contact', function () {
    return view('user.contact');
})->name('contact');

Route::get('/login', function () {
    return view('authentication.login');
})->name('login');

Route::get('/register', function () {
    return view('authentication.register');
})->name('register');

Route::get('/verifyOtp', function () {
    return view('authentication.verifyOtp');
})->name('verify.otp');

Route::get('/forgotPassword', function () {
    return view('authentication.forgotPassword');
})->name('password.request');

Route::get('/resetPassword', function () {
    return view('authentication.resetPassword');
})->name('password.reset');

Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::post('/logout-all', [LogoutController::class, 'logoutAllDevices'])->name('logout.all');
Route::post('/refresh', [RefreshTokenController::class, 'refresh'])->name('refresh');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::post('/web-register', [RegisterController::class, 'register'])->name('register.submit');
Route::post('/verify-otp', [VerificationController::class, 'verify'])->name('verify.otp.submit');
Route::post('/resend-otp', [VerificationController::class, 'resend'])->name('resend.otp.submit');

Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

Route::middleware(['jwt.auth.cookie', 'device.active'])->group(function () {
    Route::get('/me', [MeController::class, 'me'])->name('me');

    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::delete('/devices/{device}', [DeviceController::class, 'revoke'])->name('devices.revoke');
    Route::post('/devices/{device}/trust', [DeviceController::class, 'trust'])->name('devices.trust');

    Route::get('/steps/today', [StepController::class, 'today'])->name('steps.today');
    Route::post('/steps', [StepController::class, 'sync'])->name('steps.sync');
    Route::post('/steps/goal', [StepController::class, 'updateGoal'])->name('steps.goal');

    Route::get('/fitcoin/balance', [FitcoinController::class, 'balance'])->name('fitcoin.balance');
    Route::post('/fitcoin/convert', [FitcoinController::class, 'convert'])->name('fitcoin.convert');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');

    // Friendship routes
    Route::get('/friends', [FriendshipController::class, 'index'])->name('friends.index');
    Route::post('/friends/request', [FriendshipController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friends/accept', [FriendshipController::class, 'accept'])->name('friends.accept');
    Route::post('/friends/reject', [FriendshipController::class, 'reject'])->name('friends.reject');
    Route::get('/friends/requests', [FriendshipController::class, 'pending'])->name('friends.requests');
    Route::delete('/friends/{friendId}', [FriendshipController::class, 'remove'])->name('friends.remove');
    Route::get('/users/search', [FriendshipController::class, 'searchUsers'])->name('users.search');

    // Challenge routes (user CRUD + activation)
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::post('/challenges', [ChallengeController::class, 'store'])->name('challenges.store');
    Route::put('/challenges/{id}', [ChallengeController::class, 'update'])->name('challenges.update');
    Route::delete('/challenges/{id}', [ChallengeController::class, 'destroy'])->name('challenges.destroy');
    Route::post('/challenges/{id}/activate', [ChallengeController::class, 'activate'])->name('challenges.activate');
    Route::get('/challenges/active', [ChallengeController::class, 'active'])->name('challenges.active');
    Route::get('/challenges/history', [ChallengeController::class, 'history'])->name('challenges.history');
    Route::get('/challenges/{id}/progress', [ChallengeController::class, 'checkProgress'])->name('challenges.progress');

    Route::post('/account/deactivate', [AccountController::class, 'deactivate'])->name('account.deactivate');


    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');

    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('super_admin.dashboard');
        })->name('admin.dashboard');
    });
});

Route::get('/dashboard', function () {
    return redirect('/user/dashboard');
})->name('dashboard');