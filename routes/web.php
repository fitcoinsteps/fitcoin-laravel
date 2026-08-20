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

Route::middleware(['jwt.auth.cookie'])->group(function () {
    Route::get('/me', [MeController::class, 'me'])->name('me');

    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::delete('/devices/{device}', [DeviceController::class, 'revoke'])->name('devices.revoke');
    Route::post('/devices/{device}/trust', [DeviceController::class, 'trust'])->name('devices.trust');

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