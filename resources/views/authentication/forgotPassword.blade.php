<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Fitcoin</title>

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

        .forgot-icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .forgot-icon {
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
            padding: 16px 20px 16px 48px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(236, 72, 153, 0.15);
            border-radius: 12px;
            color: white;
            font-size: 15px;
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
            color: rgba(255, 255, 255, 0.35);
        }

        .neon-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.25);
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .neon-input:focus ~ .input-icon {
            color: #ec4899;
        }

        .auth-btn {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #d946ef 0%, #ec4899 100%);
            color: white;
            font-weight: 700;
            font-size: 17px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
        }

        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.4);
        }

        .auth-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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

        .alert-success.show, .alert-error.show {
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

        @media (max-width: 640px) {
            .glass-card {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="glass-card">
        <div class="forgot-icon-wrapper">
            <div class="forgot-icon">
                <i class="fas fa-key"></i>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-center glow-text">Forgot Password</h1>
        <p class="text-sm text-gray-400 text-center mt-2 mb-6">
            Enter your email address and we'll send you a verification code to reset your password.
        </p>

        <div id="message" class="alert-error"></div>

        <form id="forgot-form">
            <div class="neon-input-group">
                <input type="email" id="email" name="email" placeholder="Email Address" required class="neon-input" autocomplete="email">
                <i class="fas fa-envelope input-icon"></i>
            </div>

            <button type="submit" id="submit-btn" class="auth-btn">
                <i class="fas fa-paper-plane mr-2"></i> Send Reset Code
            </button>
        </form>

        <p class="mt-6 text-sm text-center text-gray-400">
            <i class="fas fa-arrow-left mr-2"></i>
            Remember your password? <a href="/login" class="text-neonpink hover:underline">Login</a>
        </p>

        <div class="mt-4 flex justify-center gap-4 text-xs text-gray-500">
            <span><i class="fas fa-shield-alt mr-1"></i> Secure</span>
            <span>•</span>
            <span><i class="fas fa-clock mr-1"></i> 24/7 Support</span>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const form = document.getElementById('forgot-form');
            const messageEl = document.getElementById('message');
            const submitBtn = document.getElementById('submit-btn');
            const emailInput = document.getElementById('email');

            function showMessage(text, type = 'error') {
                messageEl.textContent = text;
                messageEl.className = 'alert-' + type + ' show';
            }

            function hideMessage() {
                messageEl.className = 'alert-error';
                messageEl.textContent = '';
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideMessage();

                const email = emailInput.value.trim();

                if (!email || !email.includes('@') || !email.includes('.')) {
                    showMessage('Please enter a valid email address.', 'error');
                    emailInput.focus();
                    return;
                }

                try {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';

                    const response = await fetch('/api/forgot-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showMessage(data.message || 'Failed to send reset code. Please try again.', 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Send Reset Code';
                        return;
                    }

                    // Capture token and pass it to verify-otp page
                    const token = data.token || '';
                    showMessage('✅ Reset code sent successfully! Redirecting...', 'success');

                    setTimeout(() => {
                        window.location.href = `/verify-otp?email=${encodeURIComponent(email)}&type=password_reset&token=${encodeURIComponent(token)}`;
                    }, 1500);

                } catch (error) {
                    console.error('Forgot password error:', error);
                    showMessage('Network error. Please check your connection and try again.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Send Reset Code';
                }
            });

            // Allow Enter key to submit
            emailInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (!submitBtn.disabled) {
                        form.dispatchEvent(new Event('submit'));
                    }
                }
            });

        })();
    </script>

</body>
</html>