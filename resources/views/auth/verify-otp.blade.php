<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">
                @if(request()->query('type') === 'password_reset')
                    Verify to Reset Password
                @else
                    Verify Your Email
                @endif
            </h1>
            <p class="text-sm text-gray-600 mt-2">
                @if(request()->query('type') === 'password_reset')
                    Enter the 6‑digit code sent to your email to reset your password.
                @else
                    We sent a 6‑digit verification code to<br>
                @endif
                <strong id="email-display" class="text-indigo-600"></strong>
            </p>
        </div>

        <div id="message" class="mb-4 hidden"></div>

        <form id="otp-form" class="space-y-4">
            <div>
                <label for="otp-code" class="block text-sm font-medium text-gray-700">Enter OTP Code</label>
                <div class="flex gap-2 mt-1">
                    <input type="text" id="otp-code" placeholder="Enter 6-digit code" maxlength="6" required
                           class="flex-1 px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" id="verify-btn"
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition whitespace-nowrap">
                        Verify
                    </button>
                </div>
            </div>

            <div class="flex justify-between items-center text-sm">
                <button type="button" id="resend-btn" class="text-indigo-600 hover:underline">
                    Resend OTP
                </button>
                <button type="button" id="change-email-btn" class="text-gray-500 hover:text-gray-700 hover:underline">
                    @if(request()->query('type') === 'password_reset')
                        Try again
                    @else
                        Change Email
                    @endif
                </button>
            </div>
            <p id="resend-timer" class="text-xs text-gray-500 text-center"></p>
        </form>

        <p class="mt-4 text-sm text-center text-gray-600">
            @if(request()->query('type') === 'password_reset')
                Remember your password? <a href="/login" class="text-indigo-600 hover:underline">Login</a>
            @else
                Already verified? <a href="/login" class="text-indigo-600 hover:underline">Login</a>
            @endif
        </p>
    </div>

    <script>
        (function() {
            'use strict';

            const form = document.getElementById('otp-form');
            const otpInput = document.getElementById('otp-code');
            const verifyBtn = document.getElementById('verify-btn');
            const resendBtn = document.getElementById('resend-btn');
            const changeBtn = document.getElementById('change-email-btn');
            const messageEl = document.getElementById('message');
            const emailDisplay = document.getElementById('email-display');
            const timerEl = document.getElementById('resend-timer');

            const urlParams = new URLSearchParams(window.location.search);
            const email = urlParams.get('email');
            const type = urlParams.get('type') || 'registration';
            const resetToken = urlParams.get('token') || null;

            if (!email) {
                window.location.href = (type === 'password_reset') ? '/ForgotPassword' : '/register';
                return;
            }

            const decodedEmail = decodeURIComponent(email);
            emailDisplay.textContent = decodedEmail;

            let resendTimer = null;
            let resendCountdown = 60;

            function showMessage(text, type = 'success') {
                messageEl.textContent = text;
                messageEl.className = `mb-4 p-3 rounded-md text-sm ${
                    type === 'success' ? 'bg-green-100 text-green-700' :
                    type === 'warning' ? 'bg-yellow-100 text-yellow-700' :
                    'bg-red-100 text-red-700'
                }`;
                messageEl.classList.remove('hidden');
            }

            function hideMessage() {
                messageEl.classList.add('hidden');
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

            async function handleResend() {
                resendBtn.disabled = true;
                resendBtn.textContent = 'Sending...';

                try {
                    let endpoint, body;
                    if (type === 'password_reset') {
                        endpoint = '/api/forgot-password';      // ✅ correct lowercase
                        body = { email: decodedEmail };
                    } else {
                        endpoint = '/api/resend-otp';
                        body = { email: decodedEmail };
                    }

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(body)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showMessage(data.message || 'Failed to resend OTP', 'error');
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Resend OTP';
                        return;
                    }

                    // Update token if a new one is returned
                    if (type === 'password_reset' && data.token) {
                        const newUrl = new URL(window.location.href);
                        newUrl.searchParams.set('token', data.token);
                        window.history.replaceState({}, '', newUrl);
                        // Also update the variable
                        resetToken = data.token;
                    }

                    showMessage('✅ New OTP sent to your email!', 'success');
                    otpInput.value = '';
                    otpInput.focus();
                    startResendTimer();

                } catch (error) {
                    showMessage('Network error, please try again.', 'error');
                } finally {
                    resendBtn.textContent = 'Resend OTP';
                }
            }

            async function handleVerify(code) {
                verifyBtn.disabled = true;
                verifyBtn.textContent = 'Verifying...';

                try {
                    let endpoint, body;

                    if (type === 'password_reset') {
                        if (!resetToken) {
                            showMessage('Missing reset token. Please request a new OTP.', 'error');
                            verifyBtn.disabled = false;
                            verifyBtn.textContent = 'Verify';
                            return;
                        }
                        endpoint = '/api/verify-token';
                        body = { code, token: resetToken };
                    } else {
                        endpoint = '/api/verify-otp';
                        body = { email: decodedEmail, code };
                    }

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(body)
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

                    if (type === 'password_reset') {
                        showMessage('✅ OTP verified! Redirecting to reset password...', 'success');
                        otpInput.disabled = true;
                        verifyBtn.disabled = true;
                        verifyBtn.textContent = 'Verified ✓';
                        setTimeout(() => {
                            window.location.href = `/reset-password?token=${resetToken}&email=${encodeURIComponent(decodedEmail)}`;
                        }, 1500);
                    } else {
                        // Registration
                        if (data.token) {
                            localStorage.setItem('access_token', data.token);
                        }
                        if (data.refresh_token) {
                            localStorage.setItem('refresh_token', data.refresh_token);
                        }
                        showMessage('✅ Registration completed successfully! Redirecting...', 'success');
                        otpInput.disabled = true;
                        verifyBtn.disabled = true;
                        verifyBtn.textContent = 'Verified ✓';
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 1500);
                    }

                } catch (error) {
                    showMessage('Network error, please try again.', 'error');
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';
                }
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                hideMessage();
                const code = otpInput.value.trim();
                if (!code || code.length !== 6) {
                    showMessage('Please enter a valid 6-digit OTP code.', 'error');
                    otpInput.focus();
                    return;
                }
                handleVerify(code);
            });

            resendBtn.addEventListener('click', handleResend);

            changeBtn.addEventListener('click', function() {
                if (type === 'password_reset') {
                    window.location.href = '/ForgotPassword';
                } else {
                    if (confirm('Are you sure you want to change your email? You will need to register again.')) {
                        window.location.href = '/register';
                    }
                }
            });

            otpInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });

            otpInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (!verifyBtn.disabled) {
                        form.dispatchEvent(new Event('submit'));
                    }
                }
            });

            startResendTimer();
            otpInput.focus();

        })();
    </script>
</body>
</html>