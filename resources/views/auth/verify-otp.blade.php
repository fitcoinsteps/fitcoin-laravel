<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - Fitcoin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        neonpink: '#ec4899', neonpurple: '#8b5cf6', neongold: '#d4af37',
                        darkbg: '#0a0512', cardbg: '#1c0929'
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #0a0512;
            color: white;
            overflow-x: hidden;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .glass-card {
            background: rgba(23, 10, 35, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.5s ease forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .otp-icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .otp-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.2), rgba(139, 92, 246, 0.2));
            border: 2px solid rgba(236, 72, 153, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #ec4899;
            box-shadow: 0 0 40px rgba(236, 72, 153, 0.2);
            animation: pulseIcon 2s ease-in-out infinite;
        }

        @keyframes pulseIcon {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 40px rgba(236, 72, 153, 0.2);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 60px rgba(236, 72, 153, 0.4);
            }
        }

        .neon-input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .neon-input {
            width: 100%;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(236, 72, 153, 0.15);
            border-radius: 12px;
            color: white;
            font-size: 18px;
            letter-spacing: 6px;
            text-align: center;
            transition: all 0.3s ease;
            outline: none;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .neon-input:focus {
            border-color: #ec4899;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }

        .neon-input::placeholder {
            color: rgba(255, 255, 255, 0.25);
            letter-spacing: 2px;
        }

        .neon-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-verify {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            font-weight: 700;
            font-size: 17px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
        }

        .btn-verify:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .otp-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
        }

        .otp-actions button {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .otp-actions button:hover {
            color: #ec4899;
            background: rgba(236, 72, 153, 0.1);
        }

        .otp-actions button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .otp-actions .text-neonpink {
            color: #ec4899;
        }

        .resend-timer {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
            text-align: center;
            margin-top: 8px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 16px;
            display: none;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 16px;
            display: none;
        }

        .alert-warning {
            background: rgba(251, 191, 36, 0.15);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #fbbf24;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 16px;
            display: none;
        }

        .alert-success.show, .alert-error.show, .alert-warning.show {
            display: block;
            animation: fadeSlide 0.3s ease forwards;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glow-text {
            text-shadow: 0 0 25px rgba(236, 72, 153, 0.3);
        }

        .email-display {
            color: #ec4899;
            font-weight: 600;
        }

        @media (max-width: 640px) {
            .glass-card {
                padding: 25px 20px;
            }
            .neon-input {
                font-size: 16px;
                letter-spacing: 4px;
                padding: 14px 16px;
            }
        }
    </style>
</head>
<body>

    <div class="glass-card">
        <div class="otp-icon-wrapper">
            <div class="otp-icon">
                <i class="fas fa-envelope"></i>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-center glow-text">
            @if(request()->query('type') === 'password_reset')
                Verify to Reset Password
            @else
                Verify Your Email
            @endif
        </h1>

        <p class="text-sm text-gray-400 text-center mt-2 mb-6">
            @if(request()->query('type') === 'password_reset')
                Enter the 6‑digit code sent to your email to reset your password.
            @else
                We sent a 6‑digit verification code to
            @endif
            <br>
            <span id="email-display" class="email-display"></span>
        </p>

        <!-- Message Display -->
        <div id="message" class="alert-error"></div>

        <form id="otp-form">
            <div class="neon-input-group">
                <input type="text" id="otp-code" placeholder="• • • • • •" maxlength="6" required class="neon-input" autocomplete="off">
            </div>

            <button type="submit" id="verify-btn" class="btn-verify">
                <i class="fas fa-check mr-2"></i> Verify
            </button>

            <div class="otp-actions">
                <button type="button" id="resend-btn">
                    <i class="fas fa-redo mr-2"></i> Resend OTP
                </button>
                <button type="button" id="change-email-btn" class="text-neonpink">
                    @if(request()->query('type') === 'password_reset')
                        <i class="fas fa-arrow-left mr-2"></i> Try again
                    @else
                        <i class="fas fa-envelope mr-2"></i> Change Email
                    @endif
                </button>
            </div>
            <p id="resend-timer" class="resend-timer"></p>
        </form>

        <p class="mt-6 text-sm text-center text-gray-400">
            @if(request()->query('type') === 'password_reset')
                Remember your password? <a href="/login" class="text-neonpink hover:underline">Login</a>
            @else
                Already verified? <a href="/login" class="text-neonpink hover:underline">Login</a>
            @endif
        </p>

        <div class="mt-4 flex justify-center gap-4 text-xs text-gray-500">
            <span><i class="fas fa-shield-alt mr-1"></i> Secure</span>
            <span>•</span>
            <span><i class="fas fa-clock mr-1"></i> 24/7 Support</span>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ── No sparkle particles needed for this layout ──
        });

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
            let resetToken = urlParams.get('token') || null;

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
                messageEl.className = 'alert-' + type + ' show';
            }

            function hideMessage() {
                messageEl.className = 'alert-error';
                messageEl.textContent = '';
            }

            function startResendTimer() {
                if (resendTimer) clearInterval(resendTimer);
                resendCountdown = 60;
                resendBtn.disabled = true;
                resendBtn.style.opacity = '0.5';
                timerEl.textContent = `Resend available in ${resendCountdown}s`;

                resendTimer = setInterval(() => {
                    resendCountdown--;
                    timerEl.textContent = `Resend available in ${resendCountdown}s`;
                    if (resendCountdown <= 0) {
                        clearInterval(resendTimer);
                        resendTimer = null;
                        resendBtn.disabled = false;
                        resendBtn.style.opacity = '1';
                        timerEl.textContent = 'Ready to resend';
                    }
                }, 1000);
            }

            async function handleResend() {
                resendBtn.disabled = true;
                resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';

                try {
                    let endpoint, body;
                    if (type === 'password_reset') {
                        endpoint = '/api/forgot-password';
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
                        resendBtn.innerHTML = '<i class="fas fa-redo mr-2"></i> Resend OTP';
                        return;
                    }

                    if (type === 'password_reset' && data.token) {
                        const newUrl = new URL(window.location.href);
                        newUrl.searchParams.set('token', data.token);
                        window.history.replaceState({}, '', newUrl);
                        resetToken = data.token;
                    }

                    showMessage('✅ New OTP sent to your email!', 'success');
                    otpInput.value = '';
                    otpInput.focus();
                    startResendTimer();

                } catch (error) {
                    console.error('Resend error:', error);
                    showMessage('Network error, please try again.', 'error');
                } finally {
                    resendBtn.innerHTML = '<i class="fas fa-redo mr-2"></i> Resend OTP';
                }
            }

            async function handleVerify(code) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verifying...';

                try {
                    let endpoint, body;

                    if (type === 'password_reset') {
                        if (!resetToken) {
                            showMessage('Missing reset token. Please request a new OTP.', 'error');
                            verifyBtn.disabled = false;
                            verifyBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Verify';
                            return;
                        }
                        endpoint = '/api/verify-token';
                        body = { 
                            code: code, 
                            token: resetToken,
                            email: decodedEmail
                        };
                    } else {
                        endpoint = '/api/verify-otp';
                        body = { 
                            email: decodedEmail, 
                            code: code 
                        };
                    }

                    // Log for debugging
                    console.log('Verifying OTP:', { endpoint, body });

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
                    console.log('Verification response:', data);

                    if (!response.ok) {
                        const errorMsg = data.message || data.error || 'Invalid or expired OTP code.';
                        showMessage(errorMsg, 'error');
                        otpInput.value = '';
                        otpInput.focus();
                        verifyBtn.disabled = false;
                        verifyBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Verify';
                        return;
                    }

                    if (type === 'password_reset') {
                        showMessage('✅ OTP verified! Redirecting to reset password...', 'success');
                        otpInput.disabled = true;
                        verifyBtn.disabled = true;
                        verifyBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Verified ✓';
                        setTimeout(() => {
                            window.location.href = `/reset-password?token=${resetToken}&email=${encodeURIComponent(decodedEmail)}`;
                        }, 1500);
                    } else {
                        if (data.token) {
                            localStorage.setItem('access_token', data.token);
                        }
                        if (data.refresh_token) {
                            localStorage.setItem('refresh_token', data.refresh_token);
                        }
                        showMessage('✅ Registration completed successfully! Redirecting...', 'success');
                        otpInput.disabled = true;
                        verifyBtn.disabled = true;
                        verifyBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Verified ✓';
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 1500);
                    }

                } catch (error) {
                    console.error('Verification error:', error);
                    showMessage('Network error, please try again.', 'error');
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Verify';
                }
            }

            // ── Form submit ──
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

            // ── Resend button ──
            resendBtn.addEventListener('click', handleResend);

            // ── Change email / try again ──
            changeBtn.addEventListener('click', function() {
                if (type === 'password_reset') {
                    window.location.href = '/ForgotPassword';
                } else {
                    if (confirm('Are you sure you want to change your email? You will need to register again.')) {
                        window.location.href = '/register';
                    }
                }
            });

            // ── OTP input validation ──
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

            // ── Start timer and focus ──
            startResendTimer();
            otpInput.focus();

        })();
    </script>

</body>
</html>