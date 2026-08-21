<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'api/*',
        'login',
        'web-register',
        'verify-otp',
        'resend-otp',
        'forgot-password',
        'reset-password',
        'refresh',
        'logout',
        'logout-all',
    ];
}