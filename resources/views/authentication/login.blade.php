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
            <a href="/auth/google"
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

        <div class="mt-6 text-center text-sm text-gray-600">
            <a href="/forgotPassword" class="text-indigo-600 hover:underline">Forgot Password?</a>
            <span class="mx-2">|</span>
            Don't have an account? <a href="/register" class="text-indigo-600 hover:underline">Register</a>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const urlParams = new URLSearchParams(window.location.search);
            const errorParam = urlParams.get('error');
            if (errorParam) {
                let errorMsg = 'Login failed. Please try again.';
                if (errorParam === 'device_in_use') {
                    errorMsg = 'This device is already in use. Please logout first.';
                } else if (errorParam === 'social_auth_failed') {
                    errorMsg = 'Social login failed. Please try again.';
                } else if (errorParam === 'account_disabled') {
                    errorMsg = 'Account disabled or locked.';
                } else if (errorParam === 'account_not_allowed') {
                    errorMsg = 'This account is not allowed to use social login.';
                }
                showMessage(errorMsg, 'error');
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            const form = document.getElementById('login-form');
            const messageEl = document.getElementById('message');
            const loginBtn = document.getElementById('login-btn');

            function showMessage(text, type = 'success') {
                messageEl.textContent = text;
                messageEl.className = 'mb-4 p-3 rounded-lg text-sm ' + (type === 'success' ? 'bg-green-100 text-green-700' : type === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                messageEl.classList.remove('hidden');
            }

            function hideMessage() {
                messageEl.classList.add('hidden');
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

                    const response = await fetch('/login', {
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
                            loginBtn.disabled = false;
                            loginBtn.textContent = 'Sign In';
                            return;
                        }
                        showMessage(data.error || data.message || 'Login failed. Please try again.', 'error');
                        loginBtn.disabled = false;
                        loginBtn.textContent = 'Sign In';
                        return;
                    }

                    showMessage('Login successful! Redirecting...', 'success');

                    setTimeout(() => {
                        const redirectUrl = data.user.role === 'super-admin' ? '/admin/dashboard' : '/user/dashboard';
                        window.location.href = redirectUrl;
                    }, 500);

                } catch (err) {
                    showMessage('Network error - please check your connection and try again.', 'error');
                    loginBtn.disabled = false;
                    loginBtn.textContent = 'Sign In';
                }
            });
        })();
    </script>
</body>
</html>