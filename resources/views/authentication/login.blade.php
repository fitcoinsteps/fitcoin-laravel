<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
            <p class="text-gray-600">Login to your Fitcoin account</p>
        </div>

        <div id="message" class="hidden mb-4"></div>

        <!-- Social Auth Buttons -->
        <div class="space-y-3 mb-6">
            <a href="/api/auth/google"
               class="w-full flex items-center justify-center gap-3 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Sign in with Google
            </a>
        </div>

        <div class="flex items-center gap-4 mb-6">
            <div class="flex-1 border-t border-gray-300"></div>
            <span class="text-sm text-gray-500">or</span>
            <div class="flex-1 border-t border-gray-300"></div>
        </div>

        <form id="login-form" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
            </div>

            <button type="submit" id="login-btn"
                    class="w-full py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                Sign In
            </button>
        </form>

        <div id="otp-section" class="hidden mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Verify Your Email</h3>
            <p class="text-sm text-gray-600 mb-3">We sent a 6-digit OTP to your email.</p>
            <div class="flex gap-2">
                <input type="text" id="otp-code" placeholder="Enter 6-digit code" maxlength="6"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition">
                <button type="button" id="verify-otp-btn"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition whitespace-nowrap">
                    Verify
                </button>
            </div>
            <div class="flex justify-between items-center mt-3">
                <button type="button" id="resend-otp-btn" class="text-sm text-indigo-600 hover:underline">
                    Resend OTP
                </button>
                <button type="button" id="back-to-login-btn" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                    Back to Login
                </button>
            </div>
            <p id="resend-timer" class="text-xs text-gray-500 mt-2"></p>
        </div>

        <div class="mt-6 text-center text-sm text-gray-600">
            <a href="/forgotPassword" class="text-indigo-600 hover:underline">Forgot Password?</a>
            <span class="mx-2">|</span>
            Don't have an account? <a href="/register" class="text-indigo-600 hover:underline">Register</a>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            function redirectBasedOnRole(user) {
                const roles = user.roles.map(r => r.slug);
                let redirectUrl = '/user/dashboard';
                if (roles.includes('super-admin') || roles.includes('admin')) {
                    redirectUrl = '/admin/dashboard';
                }
                window.location.href = redirectUrl;
            }

            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            const refreshToken = urlParams.get('refresh_token');

            if (token) {
                localStorage.setItem('access_token', token);
                if (refreshToken) localStorage.setItem('refresh_token', refreshToken);
                window.history.replaceState({}, document.title, window.location.pathname);

                fetch('/api/me', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showMessage('Authentication failed. Please login again.', 'error');
                    } else {
                        // Always redirect to user dashboard for social auth
                        window.location.href = '/user/dashboard';
                    }
                })
                .catch(() => {
                    showMessage('Network error during authentication. Please try again.', 'error');
                });
                return;
            }

            const errorParam = urlParams.get('error');
            if (errorParam === 'device_in_use') {
                window._deviceError = true;
                window._deviceErrorMsg = 'This device is currently in use by another account. Please logout first.';
                window.history.replaceState({}, document.title, window.location.pathname);
            }

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
                messageEl.className = 'mb-4 p-3 rounded-lg text-sm ' + (type === 'success' ? 'bg-green-100 text-green-700' : type === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                messageEl.classList.remove('hidden');
            }

            function hideMessage() {
                messageEl.classList.add('hidden');
            }

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
                timerEl.textContent = 'Resend available in ' + resendCountdown + 's';
                resendTimer = setInterval(() => {
                    resendCountdown--;
                    timerEl.textContent = 'Resend available in ' + resendCountdown + 's';
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

                try {
                    loginBtn.disabled = true;
                    loginBtn.textContent = 'Logging in...';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                    const response = await fetch('/api/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ email, password })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.requires_verification) {
                            showMessage(data.message || 'Please verify your email first.', 'warning');
                            showOTPSection(email);
                            loginBtn.disabled = false;
                            loginBtn.textContent = 'Sign In';
                            return;
                        }
                        showMessage(data.error || data.message || 'Login failed. Please try again.', 'error');
                        loginBtn.disabled = false;
                        loginBtn.textContent = 'Sign In';
                        return;
                    }

                    if (data.access_token) {
                        localStorage.setItem('access_token', data.access_token);
                        if (data.refresh_token) localStorage.setItem('refresh_token', data.refresh_token);
                    }

                    showMessage('Login successful! Redirecting...', 'success');

                    setTimeout(() => {
                        redirectBasedOnRole(data.user);
                    }, 1000);

                } catch (err) {
                    showMessage('Network error - please check your connection and try again.', 'error');
                    loginBtn.disabled = false;
                    loginBtn.textContent = 'Sign In';
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

                    setTimeout(() => {
                        redirectBasedOnRole(data.user);
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