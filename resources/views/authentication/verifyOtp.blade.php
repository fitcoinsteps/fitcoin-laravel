<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-indigo-900/50 border border-indigo-600/30 flex items-center justify-center">
                <span class="text-2xl">🔐</span>
            </div>
            
            <h1 class="text-3xl font-bold text-white mb-2">
                Verify Your Email
            </h1>
            
            <p class="text-gray-400 text-sm mt-2">
                We sent a 6-digit verification code to
                <br>
                <strong id="email-display" class="text-indigo-400"></strong>
            </p>
        </div>

        <div id="message" class="hidden mb-4"></div>

        <form id="otp-form" class="space-y-4">
            <div>
                <label for="otp-code" class="block text-sm font-medium text-gray-300 mb-1">Enter OTP Code</label>
                <div class="flex gap-2">
                    <input type="text" id="otp-code" placeholder="Enter 6-digit code" maxlength="6" required
                           class="flex-1 px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 transition text-center text-2xl tracking-widest">
                    <button type="submit" id="verify-btn"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition whitespace-nowrap">
                        Verify
                    </button>
                </div>
            </div>

            <div class="flex justify-between items-center text-sm">
                <button type="button" id="resend-btn" class="text-indigo-400 hover:underline">
                    Resend OTP
                </button>
                <button type="button" id="change-email-btn" class="text-gray-500 hover:text-gray-300 hover:underline">
                    Change Email
                </button>
            </div>
            <p id="resend-timer" class="text-xs text-gray-500 text-center"></p>
        </form>

        <div class="mt-6 text-center text-sm text-gray-400">
            Already verified? <a href="/login" class="text-indigo-400 hover:underline">Login</a>
        </div>
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

            if (!email) {
                window.location.href = '/register';
                return;
            }

            const decodedEmail = decodeURIComponent(email);
            emailDisplay.textContent = decodedEmail;

            let resendTimer = null;
            let resendCountdown = 60;

            function showMessage(text, type = 'success') {
                messageEl.textContent = text;
                messageEl.className = 'mb-4 p-3 rounded-lg text-sm ' + (type === 'success' ? 'bg-green-900/50 text-green-400' : type === 'warning' ? 'bg-yellow-900/50 text-yellow-400' : 'bg-red-900/50 text-red-400');
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

            function redirectBasedOnRole(user) {
                const roles = user.roles.map(r => r.slug);
                let redirectUrl = '/user/dashboard';
                if (roles.includes('super-admin') || roles.includes('admin')) {
                    redirectUrl = '/admin/dashboard';
                }
                window.location.href = redirectUrl;
            }

            async function handleResend() {
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
                        body: JSON.stringify({ email: decodedEmail })
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
            }

            async function handleVerify(code) {
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
                        body: JSON.stringify({ email: decodedEmail, code })
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

                    if (data.token) {
                        localStorage.setItem('access_token', data.token);
                    }
                    if (data.refresh_token) {
                        localStorage.setItem('refresh_token', data.refresh_token);
                    }
                    showMessage('Registration completed successfully! Redirecting...', 'success');
                    otpInput.disabled = true;
                    verifyBtn.disabled = true;
                    verifyBtn.textContent = 'Verified';

                    setTimeout(() => {
                        if (data.user && data.user.roles) {
                            redirectBasedOnRole(data.user);
                        } else {
                            window.location.href = '/dashboard';
                        }
                    }, 1500);

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
                if (confirm('Are you sure you want to change your email? You will need to register again.')) {
                    window.location.href = '/register';
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