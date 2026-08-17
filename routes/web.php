<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('website.home');
})->name('home');

// Route::get('/about', function () {
//     return view('user.about');
// })->name('about');

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
    return view('authentication.ForgotPassword');
})->name('password.request');

Route::get('/resetPassword', function () {
    return view('authentication.resetPassword');
})->name('password.reset');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/user/dashboard', function () {
    return view('user.dashboard');
})->name('user.dashboard');

Route::get('/dashboard', function () {
    return redirect('/user/dashboard');
})->name('dashboard');
