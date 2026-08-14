<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('user.home');
})->name('home');

Route::get('/about', function () {
    return view('user.about');
})->name('about');

Route::get('/contact', function () {
    return view('user.contact');
})->name('contact');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/verify-otp', function () {
    return view('auth.verify-otp');
})->name('verify.otp');

Route::get('/ForgotPassword', function () {
    return view('auth.ForgotPassword');
})->name('password.request');

Route::get('/reset-password', function () {
    return view('auth.reset-password');
})->name('password.reset');

Route::get('/admin', function () {
    return view('admin.admin');
})->name('admin');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'userDashboard'])
        ->name('user.dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
        ->middleware(['role:admin,super-admin'])
        ->name('admin.dashboard');

    Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdminDashboard'])
        ->middleware(['role:super-admin'])
        ->name('super-admin.dashboard');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');