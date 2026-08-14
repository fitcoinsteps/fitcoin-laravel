<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Fitcoin</title>

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
        }
        .glow-text {
            text-shadow: 0 0 25px rgba(236, 72, 153, 0.6);
        }
        .glass-card {
            background: rgba(28, 9, 41, 0.5);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(236, 72, 153, 0.2);
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            border-color: rgba(236, 72, 153, 0.6);
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(139, 92, 246, 0.3);
        }

        .phone-perspective-wrapper {
            perspective: 1200px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 10;
            animation: breathePhone 4.5s ease-in-out infinite;
        }
        @keyframes breathePhone {
            0%,
            100% {
                transform: scale(1) rotateY(-25deg) rotateX(10deg) rotateZ(5deg);
            }
            50% {
                transform: scale(1.03) rotateY(-15deg) rotateX(5deg) rotateZ(3deg);
            }
        }
        .phone-frame {
            background: #150822;
            border-radius: 45px;
            border: 4px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 100px rgba(236, 72, 153, 0.6), inset 0 0 20px rgba(139, 92, 246, 0.2);
            overflow: hidden;
            position: relative;
            max-width: 300px;
            width: 100%;
            transform: rotateY(-25deg) rotateX(10deg) rotateZ(5deg) scale(0.9);
            transition: transform 0.6s;
        }
        .phone-frame:hover {
            transform: rotateY(-15deg) rotateX(5deg) rotateZ(3deg) scale(0.95);
        }

        .step-counter-container {
            position: relative;
            width: 160px;
            height: 160px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .app-ring-chart {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: conic-gradient(from 180deg, #8b5cf6 0%, #06b6d4 25%, #ec4899 50%, #d4af37 75%, #8b5cf6 100%);
            box-shadow: inset 0 0 40px rgba(236, 72, 153, 0.6), 0 0 80px rgba(139, 92, 246, 0.5);
            transform: rotate(-20deg);
            animation: ringPulse 2s ease-in-out infinite;
        }
        @keyframes ringPulse {
            0%,
            100% {
                transform: rotate(-20deg) scale(1);
                opacity: 0.9;
            }
            50% {
                transform: rotate(-10deg) scale(1.06);
                opacity: 1;
            }
        }
        .app-ring-inner {
            position: relative;
            z-index: 4;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #150822;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(236, 72, 153, 0.5);
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.9);
        }

        .particle-field {
            position: absolute;
            inset: -40px;
            z-index: 1;
            pointer-events: none;
            overflow: visible;
        }
        .sparkle {
            position: absolute;
            background: #ec4899;
            border-radius: 50%;
            box-shadow: 0 0 15px #ec4899, 0 0 30px #ec4899;
            animation: sparkleExplosion var(--duration) linear infinite;
            opacity: 0;
        }
        @keyframes sparkleExplosion {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--tx), var(--ty)) scale(0);
                opacity: 0;
            }
        }

        .app-btn {
            background: rgba(44, 18, 66, 0.85);
            border: 1px solid rgba(236, 72, 153, 0.3);
            border-radius: 16px;
            transition: all 0.3s;
            animation: floatButton 3s ease-in-out infinite;
        }
        .app-btn:hover {
            background: rgba(236, 72, 153, 0.25);
            border-color: #ec4899;
            transform: scale(1.05) !important;
        }
        @keyframes floatButton {
            0%,
            100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .btn-1 {
            animation-delay: 0s;
        }
        .btn-2 {
            animation-delay: 0.5s;
        }
        .btn-3 {
            animation-delay: 1s;
        }
        .btn-4 {
            animation-delay: 1.5s;
        }

        .flying-money {
            position: absolute;
            color: #22c55e;
            text-shadow: 0 0 20px rgba(34, 197, 94, 0.7), 0 0 50px rgba(34, 197, 94, 0.3);
            animation: flyMoney 8s linear infinite;
            z-index: 5;
        }
        @keyframes flyMoney {
            0% {
                transform: translateY(0) rotate(0deg) scale(0.8) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-400px) rotate(720deg) scale(1.3) translateX(150px);
                opacity: 0;
            }
        }

        .nav-icon-active {
            color: #ec4899;
            filter: drop-shadow(0 0 8px #ec4899);
        }
        .nav-icon-inactive {
            color: #6b4a7d;
            transition: all 0.3s;
        }

        .split-screen {
            display: flex;
            height: 100vh;
            width: 100vw;
        }
        .left-panel {
            flex: 1;
            background: #0a0512;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 20px;
        }
        .bg-logo-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(1.8);
            z-index: 0;
            opacity: 0.35;
            filter: blur(2px);
        }
        .bg-logo-container img {
            width: 600px;
            height: 600px;
            object-fit: contain;
        }
        .right-panel {
            flex: 1;
            background: #130823;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
            position: relative;
        }

        .glass-auth-card {
            background: rgba(23, 10, 35, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 800px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 1;
        }

        .reset-icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .reset-icon {
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
            margin-bottom: 16px;
            position: relative;
        }
        .neon-input {
            width: 100%;
            padding: 16px 20px;
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
        .neon-input-group .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.25);
            font-size: 18px;
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
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
        }
        .alert-warning {
            background: rgba(251, 191, 36, 0.15);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #fbbf24;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        @media (max-width: 1024px) {
            .split-screen {
                flex-direction: column;
                overflow-y: auto;
                height: auto;
                min-height: 100vh;
            }
            .left-panel {
                display: none;
            }
            .right-panel {
                padding: 20px;
                background: #0a0512;
            }
            .glass-auth-card {
                padding: 25px;
                border-radius: 16px;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="split-screen">

        <!-- ==================== LEFT PANEL ==================== -->
        <div class="left-panel">
            <div class="relative z-10 text-center max-w-lg mb-2">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight drop-shadow-lg">Achieve More <br>With Every Step.</h1>
            </div>
            <div class="bg-logo-container">
                <img src="{{ asset('images/FitCoin_metallic_coin_emblem_logo_202607311418.jpeg') }}" alt="Background Logo">
            </div>
            <div class="relative z-10 ml-8">
                <div class="flying-money" style="top: -30%; left: -20%; animation-delay: 0s; font-size: 2.5rem;"><i class="fas fa-money-bill-wave"></i></div>
                <div class="flying-money" style="bottom: -20%; right: -30%; animation-delay: 3s; font-size: 3rem;"><i class="fas fa-coins"></i></div>
                <div class="phone-perspective-wrapper">
                    <div class="phone-frame">
                        <div class="p-5 space-y-3">
                            <div class="flex justify-between items-center">
                                <div class="text-gray-400 text-xs">Good Morning,</div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neonpink to-neonpurple border-2 border-neonpink/50"><img src="https://i.pravatar.cc/150?img=5" class="w-full h-full rounded-full object-cover"></div>
                            </div>
                            <div class="step-counter-container mx-auto">
                                <div class="particle-field" id="sparkleField"></div>
                                <div class="app-ring-chart"></div>
                                <div class="app-ring-inner flex-col gap-0.5">
                                    <span class="text-neonpink text-[10px] font-semibold">71.25%</span>
                                    <span class="text-xl font-black text-white">14,250</span>
                                    <span class="text-[9px] text-gray-400">STEPS</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <div class="app-btn btn-1 p-2 flex flex-col items-center text-center gap-1 min-h-[80px] justify-center relative">
                                    <i class="fas fa-shoe-prints text-xl text-neonpink"></i>
                                    <div class="font-bold text-[10px] leading-tight text-white">Double Steps</div>
                                    <div class="text-[8px] text-gray-400 leading-tight">Watch ads!</div>
                                </div>
                                <div class="app-btn btn-2 p-2 flex flex-col items-center text-center gap-1 min-h-[80px] justify-center relative">
                                    <i class="fas fa-coins text-xl text-neonpink"></i>
                                    <div class="font-bold text-[10px] leading-tight text-white">Earn Coins</div>
                                    <div class="text-[8px] text-gray-400 leading-tight">Daily Quests!</div>
                                </div>
                                <div class="app-btn btn-3 p-2 flex flex-col items-center text-center gap-1 min-h-[80px] justify-center relative">
                                    <i class="fas fa-money-bill-wave text-xl text-neonpink"></i>
                                    <div class="font-bold text-[10px] leading-tight text-white">Redeem Cash</div>
                                    <div class="text-[8px] text-gray-400 leading-tight">Withdraw safely!</div>
                                </div>
                                <div class="app-btn btn-4 p-2 flex flex-col items-center text-center gap-1 min-h-[80px] justify-center relative">
                                    <i class="fas fa-user-plus text-xl text-neonpink"></i>
                                    <div class="font-bold text-[10px] leading-tight text-white">Profile & Invite</div>
                                    <div class="text-[8px] text-gray-400 leading-tight">Invite Friends!</div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-2 bg-gradient-to-t from-[#0a0512] to-transparent flex justify-between items-center text-sm relative">
                            <i class="fas fa-home nav-icon-active"></i>
                            <i class="fas fa-heartbeat nav-icon-inactive"></i>
                            <i class="fas fa-chart-line nav-icon-inactive"></i>
                            <i class="fas fa-user nav-icon-inactive"></i>
                            <div class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-20 h-1 bg-gray-600 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== RIGHT PANEL ==================== -->
        <div class="right-panel">
            <div class="glass-auth-card">
                <div class="flex justify-center mb-6">
                    <span class="text-2xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-neonpink to-neonpurple"><i class="fas fa-bolt text-neonpink mr-2"></i>Fitcoin</span>
                </div>

                <div class="reset-icon-wrapper">
                    <div class="reset-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-center">Reset Password</h1>
                <p class="text-sm text-gray-400 text-center mt-2 mb-6">
                    Enter your new password below to regain access to your account.
                </p>

                <!-- Message Display -->
                <div id="message" class="mb-4 hidden"></div>

                <form id="reset-form">
                    <input type="hidden" id="reset-token" value="{{ request()->query('token') }}">
                    <input type="hidden" id="reset-email" value="{{ request()->query('email') }}">

                    <div class="neon-input-group">
                        <input type="password" id="password" name="password" placeholder="New Password" required class="neon-input" minlength="8">
                        <span class="input-icon"><i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i></span>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>

                    <div class="neon-input-group">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required class="neon-input" minlength="8">
                    </div>

                    <button type="submit" id="reset-btn" class="auth-btn">
                        <i class="fas fa-key mr-2"></i> Reset Password
                    </button>
                </form>

                <p class="mt-6 text-sm text-center text-gray-400">
                    <a href="/login" class="text-neonpink hover:underline"><i class="fas fa-arrow-left mr-2"></i> Back to Login</a>
                </p>
            </div>

            <div class="mt-6 flex justify-between items-center w-full max-w-[800px] text-[10px] text-gray-500 px-2">
                <span>Fitcoin Inc. 2024</span>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ── Sparkle particles ──
            const field = document.getElementById('sparkleField');
            if (field) {
                for (let i = 0; i < 30; i++) {
                    const spark = document.createElement('div');
                    spark.className = 'sparkle';
                    const size = Math.random() * 3 + 1;
                    spark.style.width = size + 'px';
                    spark.style.height = size + 'px';
                    spark.style.left = Math.random() * 100 + '%';
                    spark.style.top = Math.random() * 100 + '%';
                    const angle = Math.random() * 360;
                    const distance = Math.random() * 100 + 20;
                    spark.style.setProperty('--tx', Math.cos(angle) * distance + 'px');
                    spark.style.setProperty('--ty', Math.sin(angle) * distance + 'px');
                    spark.style.setProperty('--duration', (Math.random() * 2 + 1) + 's');
                    spark.style.animationDelay = Math.random() * 3 + 's';
                    field.appendChild(spark);
                }
            }
        });

        (function() {
            'use strict';

            const form = document.getElementById('reset-form');
            const messageEl = document.getElementById('message');
            const resetBtn = document.getElementById('reset-btn');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const strengthBar = document.getElementById('strengthBar');

            function showMessage(text, type = 'success') {
                messageEl.textContent = text;
                messageEl.className = 'mb-4 ' + (type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' :
                    'alert-error');
                messageEl.classList.remove('hidden');
            }

            function hideMessage() {
                messageEl.classList.add('hidden');
            }

            // ── Password strength checker ──
            function checkPasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
                if (password.match(/\d/)) strength++;
                if (password.match(/[^a-zA-Z\d]/)) strength++;
                return strength;
            }

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = checkPasswordStrength(password);
                const percentages = [0, 25, 50, 75, 100];
                const colors = ['#ef4444', '#f59e0b', '#fbbf24', '#34d399', '#22c55e'];
                const labels = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];

                strengthBar.style.width = percentages[strength] + '%';
                strengthBar.style.background = colors[strength] || '#22c55e';
                strengthBar.parentElement.style.background = 'rgba(255,255,255,0.05)';
            });

            // ── Toggle password visibility ──
            document.getElementById('togglePassword').addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            // ── Form submit ──
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideMessage();

                const token = document.getElementById('reset-token').value;
                const email = document.getElementById('reset-email').value;
                const password = passwordInput.value;
                const password_confirmation = confirmInput.value;

                if (!token || !email) {
                    showMessage('Invalid reset session. Please request a new reset.', 'error');
                    return;
                }

                if (password.length < 8) {
                    showMessage('Password must be at least 8 characters.', 'error');
                    passwordInput.focus();
                    return;
                }

                if (password !== password_confirmation) {
                    showMessage('Passwords do not match.', 'error');
                    confirmInput.focus();
                    return;
                }

                // Log the request for debugging
                console.log('Sending reset request:', {
                    email: email,
                    token: token,
                    password: '***'
                });

                try {
                    resetBtn.disabled = true;
                    resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Resetting...';

                    const response = await fetch('/api/reset-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                        },
                        body: JSON.stringify({ email, token, password, password_confirmation })
                    });

                    const data = await response.json();
                    console.log('Reset response:', data);

                    if (!response.ok) {
                        showMessage(data.error || data.message || 'Password reset failed.', 'error');
                        resetBtn.disabled = false;
                        resetBtn.innerHTML = '<i class="fas fa-key mr-2"></i> Reset Password';
                        return;
                    }

                    if (data.access_token) {
                        localStorage.setItem('access_token', data.access_token);
                        if (data.refresh_token) {
                            localStorage.setItem('refresh_token', data.refresh_token);
                        }
                    }

                    showMessage('✅ Password reset successfully! Redirecting...', 'success');
                    passwordInput.disabled = true;
                    confirmInput.disabled = true;
                    resetBtn.disabled = true;
                    resetBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Done ✓';

                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 2000);

                } catch (error) {
                    console.error('Reset error:', error);
                    showMessage('Network error. Please try again.', 'error');
                    resetBtn.disabled = false;
                    resetBtn.innerHTML = '<i class="fas fa-key mr-2"></i> Reset Password';
                }
            });

            // ── Enter key support ──
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const active = document.activeElement;
                    if (active === passwordInput || active === confirmInput) {
                        if (!resetBtn.disabled) {
                            form.dispatchEvent(new Event('submit'));
                        }
                    }
                }
            });

            // ── Check if we have token and email ──
            const token = document.getElementById('reset-token').value;
            const email = document.getElementById('reset-email').value;
            if (!token || !email) {
                showMessage('Invalid or missing reset credentials. Please request a new password reset.', 'warning');
            }

        })();
    </script>
</body>
</html>