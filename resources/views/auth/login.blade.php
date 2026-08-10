<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Login - Fitcoin</title>

    <style>
        /* All styles (unchanged) – kept identical to what you provided */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .min-h-screen {
            min-height: 100vh;
        }
        .flex {
            display: flex;
        }
        .items-center {
            align-items: center;
        }
        .justify-center {
            justify-content: center;
        }
        .w-full {
            width: 100%;
        }
        .max-w-md {
            max-width: 28rem;
        }
        .block {
            display: block;
        }
        .hidden {
            display: none;
        }
        .space-y-4>*+* {
            margin-top: 1rem;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }
        .bg-white {
            background-color: #ffffff;
        }
        .bg-green-100 {
            background-color: #d1fae5;
        }
        .bg-red-100 {
            background-color: #fee2e2;
        }
        .bg-indigo-600 {
            background-color: #4f46e5;
        }
        .bg-indigo-700 {
            background-color: #4338ca;
        }
        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .text-gray-700 {
            color: #374151;
        }
        .text-gray-600 {
            color: #4b5563;
        }
        .text-gray-500 {
            color: #6b7280;
        }
        .text-white {
            color: #ffffff;
        }
        .text-indigo-600 {
            color: #4f46e5;
        }
        .text-green-700 {
            color: #065f46;
        }
        .text-red-700 {
            color: #991b1b;
        }
        .text-center {
            text-align: center;
        }

        .text-sm {
            font-size: 0.875rem;
        }
        .text-2xl {
            font-size: 1.5rem;
        }
        .font-medium {
            font-weight: 500;
        }
        .font-bold {
            font-weight: 700;
        }

        .p-8 {
            padding: 2rem;
        }
        .p-3 {
            padding: 0.75rem;
        }
        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .mt-1 {
            margin-top: 0.25rem;
        }
        .mt-2 {
            margin-top: 0.5rem;
        }
        .mt-4 {
            margin-top: 1rem;
        }
        .mb-2 {
            margin-bottom: 0.5rem;
        }
        .mb-4 {
            margin-bottom: 1rem;
        }
        .mb-6 {
            margin-bottom: 1.5rem;
        }
        .mr-2 {
            margin-right: 0.5rem;
        }

        .rounded-md {
            border-radius: 0.375rem;
        }
        .rounded-lg {
            border-radius: 0.5rem;
        }
        .border {
            border-width: 1px;
        }
        .border-gray-300 {
            border-color: #d1d5db;
        }
        .border-gray-200 {
            border-color: #e5e7eb;
        }
        .border-t {
            border-top-width: 1px;
        }
        .pt-4 {
            padding-top: 1rem;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .focus\:border-indigo-500:focus {
            border-color: #6366f1;
        }
        .focus\:ring-indigo-500:focus {
            outline: none;
            ring-color: #6366f1;
        }

        .hover\:bg-indigo-700:hover {
            background-color: #4338ca;
        }
        .hover\:bg-green-700:hover {
            background-color: #15803d;
        }
        .hover\:bg-gray-50:hover {
            background-color: #f9fafb;
        }
        .hover\:underline:hover {
            text-decoration: underline;
        }
        .hover\:text-gray-700:hover {
            color: #374151;
        }

        .transition {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 1rem;
            line-height: 1.5;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            background-color: #fff;
        }
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        .btn-primary {
            display: inline-block;
            width: 100%;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 1rem;
            color: #ffffff;
            background-color: #4f46e5;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
        }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-verify {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 1rem;
            color: #ffffff;
            background-color: #16a34a;
            cursor: pointer;
            transition: background-color 0.15s ease;
            white-space: nowrap;
        }
        .btn-verify:hover {
            background-color: #15803d;
        }
        .btn-verify:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-google {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: #ffffff;
            color: #374151;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
            text-decoration: none;
        }
        .btn-google:hover {
            background-color: #f9fafb;
        }
        .btn-google svg {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.5rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
            padding: 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        @media (max-width: 640px) {
            .p-8 {
                padding: 1.5rem;
            }
            .max-w-md {
                max-width: 100%;
                margin-left: 1rem;
                margin-right: 1rem;
            }
            .text-2xl {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

        <!-- message banner -->
        <div id="message" class="hidden mb-4"></div>

        <form id="login-form" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required />
            </div>

            <button type="submit" class="btn-primary transition" id="login-btn">
                Sign in
            </button>
        </form>

        <!-- Google Login Button -->
        <div class="mt-4">
            <a href="/api/auth/google" class="btn-google transition">
                <svg viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Sign in with Google
            </a>
        </div>

        <!-- OTP Verification Section (hidden initially) -->
        <div id="otp-section" class="hidden mt-4 pt-4 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Verify Your Email</h3>
            <p class="text-sm text-gray-600 mb-3">We sent a 6-digit OTP to your email. Please enter it below.</p>
            <div class="flex gap-2">
                <input type="text" id="otp-code" placeholder="Enter 6-digit code" maxlength="6"
                       class="flex-1 px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="button" id="verify-otp-btn" class="btn-verify">
                    Verify
                </button>
            </div>
            <div class="flex justify-between items-center mt-2">
                <button type="button" id="resend-otp-btn" class="text-sm text-indigo-600 hover:underline">
                    Resend OTP
                </button>
                <button type="button" id="back-to-login-btn" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                    Back to Login
                </button>
            </div>
            <p id="resend-timer" class="text-xs text-gray-500 mt-1"></p>
        </div>

        <!-- Footer -->
        <p class="mt-4 text-sm text-center text-gray-600">
            <a href="/ForgotPassword" class="text-indigo-600 hover:underline">Forgot Password?</a>
            <span class="mx-2">|</span>
            Don't have an account? <a href="/register" class="text-indigo-600 hover:underline">Register</a>
        </p>
    </div>

    <script>
        (function() {
            'use strict';

            // ── Auto‑login from OAuth callback ──
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            const refreshToken = urlParams.get('refresh_token');

            if (token) {
                localStorage.setItem('access_token', token);
                if (refreshToken) localStorage.setItem('refresh_token', refreshToken);
                // Clean URL and redirect
                window.history.replaceState({}, document.title, window.location.pathname);
                window.location.href = '/dashboard';
                return; // stop further execution
            }

            // ── Device‑in‑use error handling ──
            const errorParam = urlParams.get('error');
            if (errorParam === 'device_in_use') {
                // showMessage must be defined before this – it is defined below
                // We'll call it after the functions are defined; we'll move it after the function declarations.
                // For now, we store a flag to show later.
                window._deviceError = true;
                window._deviceErrorMsg = 'This device is currently in use by another account. Please logout first.';
                // Clean the URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // ── Existing login logic ──
            const form = document.getElementById('login-form');
            const messageEl = document.getElementById('message');
            const loginBtn = document.getElementById('login-btn');
            const otpSection = document.getElementById('otp-section');
            const otpInput = document.getElementById('otp-code');
            const verifyBtn = document.getElementById('verify-otp-btn');
            const resendBtn = document.getElementById('resend-otp-btn');
            const backToLoginBtn = document.getElementById('back-to-login-btn');
            const timerEl = document.getElementById('resend-timer');

            let pendingEmail = null;
            let resendTimer = null;
            let resendCountdown = 60;

            function showMessage(text, type = 'success') {
                messageEl.textContent = text;
                messageEl.className = 'mb-4 ' + (type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' : 'alert-error');
                messageEl.classList.remove('hidden');
            }

            function hideMessage() {
                messageEl.classList.add('hidden');
            }

            // If we have a pending device error, show it now
            if (window._deviceError) {
                showMessage(window._deviceErrorMsg, 'error');
                delete window._deviceError;
                delete window._deviceErrorMsg;
            }

            function showOTPSection(email) {
                pendingEmail = email;
                otpSection.classList.remove('hidden');
                form.querySelectorAll('input').forEach(input => input.disabled = true);
                loginBtn.disabled = true;
                otpInput.focus();
                startResendTimer();
            }

            function hideOTPSection() {
                otpSection.classList.add('hidden');
                form.querySelectorAll('input').forEach(input => input.disabled = false);
                loginBtn.disabled = false;
                if (resendTimer) {
                    clearInterval(resendTimer);
                    resendTimer = null;
                }
            }

            function startResendTimer() {
                if (resendTimer) clearInterval(resendTimer);
                resendCountdown = 60;
                resendBtn.disabled = true;
                resendBtn.classList.add('opacity-50', 'cursor-not-allowed');
                timerEl.textContent = `Resend available in ${resendCountdown}s`;
                resendTimer = setInterval(() => {
                    resendCountdown--;
                    timerEl.textContent = `Resend available in ${resendCountdown}s`;
                    if (resendCountdown <= 0) {
                        clearInterval(resendTimer);
                        resendTimer = null;
                        resendBtn.disabled = false;
                        resendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        timerEl.textContent = 'Ready to resend';
                    }
                }, 1000);
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                hideMessage();

                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value.trim();

                if (!email || !password) {
                    showMessage('Please fill in all fields.', 'error');
                    return;
                }

                if (!email.includes('@') || !email.includes('.')) {
                    showMessage('Please enter a valid email address.', 'error');
                    return;
                }

                try {
                    loginBtn.disabled = true;
                    loginBtn.textContent = 'Logging in...';

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                    const response = await fetch('/api/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ email, password })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.requires_verification) {
                            showMessage(data.message || 'Please verify your email first.', 'warning');
                            showOTPSection(email);
                            loginBtn.disabled = false;
                            loginBtn.textContent = 'Sign in';
                            return;
                        }
                        showMessage(data.error || data.message || 'Login failed. Please try again.', 'error');
                        loginBtn.disabled = false;
                        loginBtn.textContent = 'Sign in';
                        return;
                    }

                    if (data.access_token) {
                        localStorage.setItem('access_token', data.access_token);
                        if (data.refresh_token) localStorage.setItem('refresh_token', data.refresh_token);
                    }

                    showMessage('Login successful! Redirecting…', 'success');

                    setTimeout(function() {
                        window.location.href = '/dashboard';
                    }, 1000);

                } catch (err) {
                    showMessage('Network error – please check your connection and try again.', 'error');
                    loginBtn.disabled = false;
                    loginBtn.textContent = 'Sign in';
                }
            });

            verifyBtn.addEventListener('click', async function() {
                const code = otpInput.value.trim();
                if (!code || code.length !== 6) {
                    showMessage('Please enter a valid 6-digit OTP code.', 'error');
                    otpInput.focus();
                    return;
                }

                verifyBtn.disabled = true;
                verifyBtn.textContent = 'Verifying...';

                try {
                    const response = await fetch('/api/verify-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email: pendingEmail, code })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showMessage(data.message || 'Invalid or expired OTP code.', 'error');
                        otpInput.value = '';
                        otpInput.focus();
                        verifyBtn.disabled = false;
                        verifyBtn.textContent = 'Verify';
                        return;
                    }

                    showMessage('Email verified! Please login again.', 'success');
                    if (data.token) {
                        localStorage.setItem('access_token', data.token);
                        if (data.refresh_token) localStorage.setItem('refresh_token', data.refresh_token);
                    }

                    hideOTPSection();
                    document.getElementById('password').value = '';
                    otpInput.value = '';
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';

                    setTimeout(function() {
                        window.location.href = '/dashboard';
                    }, 1500);

                } catch (error) {
                    showMessage('Network error, please try again.', 'error');
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';
                }
            });

            resendBtn.addEventListener('click', async function() {
                resendBtn.disabled = true;
                resendBtn.textContent = 'Sending...';

                try {
                    const response = await fetch('/api/resend-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email: pendingEmail })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showMessage(data.message || 'Failed to resend OTP', 'error');
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Resend OTP';
                        return;
                    }

                    showMessage('New OTP sent to your email!', 'success');
                    otpInput.value = '';
                    otpInput.focus();
                    startResendTimer();

                } catch (error) {
                    showMessage('Network error, please try again.', 'error');
                } finally {
                    resendBtn.textContent = 'Resend OTP';
                }
            });

            backToLoginBtn.addEventListener('click', function() {
                hideOTPSection();
                hideMessage();
                otpInput.value = '';
                document.getElementById('password').value = '';
                document.getElementById('email').focus();
            });

            document.addEventListener('input', function(e) {
                if (e.target && e.target.id === 'otp-code') {
                    e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.target && e.target.id === 'otp-code' && e.key === 'Enter') {
                    e.preventDefault();
                    if (!verifyBtn.disabled) verifyBtn.click();
                }
            });

            const savedEmail = localStorage.getItem('pending_login_verification');
            if (savedEmail) {
                setTimeout(() => {
                    document.getElementById('email').value = savedEmail;
                    showOTPSection(savedEmail);
                    showMessage('Please verify your email to continue.', 'warning');
                    localStorage.removeItem('pending_login_verification');
                }, 500);
            }

        })();
    </script>

</body>
</html>