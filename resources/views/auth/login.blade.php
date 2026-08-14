<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Login - Fitcoin</title>

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

        .social-row {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.03);
            cursor: pointer;
            text-decoration: none;
        }
        .social-btn:hover {
            border-color: rgba(236, 72, 153, 0.4);
            color: white;
            transform: translateY(-3px);
        }

        .otp-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
        .otp-section h3 {
            color: white;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .otp-section p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            margin-bottom: 12px;
        }
        .otp-input-row {
            display: flex;
            gap: 10px;
        }
        .otp-input-row input {
            flex: 1;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(236, 72, 153, 0.15);
            border-radius: 12px;
            color: white;
            font-size: 16px;
            letter-spacing: 4px;
            text-align: center;
            transition: all 0.3s ease;
            outline: none;
        }
        .otp-input-row input:focus {
            border-color: #ec4899;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
            background: rgba(255, 255, 255, 0.08);
        }
        .btn-verify {
            padding: 14px 24px;
            border-radius: 12px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
        }
        .btn-verify:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .otp-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }
        .otp-actions button {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.4);
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .otp-actions button:hover {
            color: #ec4899;
        }
        .otp-actions .text-neonpink {
            color: #ec4899;
        }
        .resend-timer {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.3);
            margin-top: 4px;
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

        <div class="right-panel">
            <div class="glass-auth-card">
                <div class="flex justify-center mb-6">
                    <span class="text-2xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-neonpink to-neonpurple"><i class="fas fa-bolt text-neonpink mr-2"></i>Fitcoin</span>
                </div>

                <h3 class="text-xl font-bold mb-1">Welcome Back</h3>
                <p class="text-sm text-gray-400 mb-4">Access your fitness journey.</p>

                <div id="message" class="mb-4 hidden"></div>

                <form id="login-form">
                    <div class="neon-input-group">
                        <input type="email" id="email" name="email" placeholder="Email Address" required class="neon-input">
                    </div>
                    <div class="neon-input-group">
                        <input type="password" id="password" name="password" placeholder="Password" required class="neon-input">
                    </div>

                    <div class="flex justify-between items-center text-sm text-gray-400 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer hover:text-white transition">
                            <input type="checkbox" class="accent-neonpink bg-transparent border-white/20 rounded h-4 w-4"> Remember Me
                        </label>
                        <a href="/ForgotPassword" class="hover:text-neonpink transition">Forgot Password?</a>
                    </div>

                    <button type="submit" id="login-btn" class="auth-btn">
                        <i class="fas fa-sign-in-alt mr-2"></i> Log In
                    </button>
                </form>

                <div class="social-row">
                    <a href="/api/auth/google" class="social-btn" title="Sign in with Google">
                        <i class="fab fa-google text-red-400"></i>
                    </a>
                    <a href="/api/auth/apple" class="social-btn" title="Sign in with Apple">
                        <i class="fab fa-apple text-white"></i>
                    </a>
                </div>

                <div class="relative my-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-700/30"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-4 bg-[#130823] text-gray-500">or continue with</span>
                    </div>
                </div>

                <div id="otp-section" class="otp-section hidden">
                    <h3>Verify Your Email</h3>
                    <p>We sent a 6-digit OTP to your email. Please enter it below.</p>
                    <div class="otp-input-row">
                        <input type="text" id="otp-code" placeholder="Enter code" maxlength="6" autocomplete="off">
                        <button type="button" id="verify-otp-btn" class="btn-verify">Verify</button>
                    </div>
                    <div class="otp-actions">
                        <button type="button" id="resend-otp-btn">Resend OTP</button>
                        <button type="button" id="back-to-login-btn" class="text-neonpink">Back to Login</button>
                    </div>
                    <p id="resend-timer" class="resend-timer"></p>
                </div>

                <p class="mt-4 text-sm text-center text-gray-400">
                    Don't have an account? <a href="/register" class="text-neonpink hover:underline">Register</a>
                </p>
            </div>

            <div class="mt-6 flex justify-between items-center w-full max-w-[800px] text-[10px] text-gray-500 px-2">
                <span>Fitcoin Inc. 2024</span>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        'use strict';

        document.addEventListener("DOMContentLoaded", function() {
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

        const form = document.getElementById('login-form');
        const messageEl = document.getElementById('message');
        const loginBtn = document.getElementById('login-btn');
        const otpSection = document.getElementById('otp-section');
        const otpInput = document.getElementById('otp-code');
        const verifyBtn = document.getElementById('verify-otp-btn');
        const resendBtn = document.getElementById('resend-otp-btn');
        const backToLoginBtn = document.getElementById('back-to-login-btn');
        const timerEl = document.getElementById('resend-timer');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

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

        function showOTPSection(email) {
            pendingEmail = email;
            otpSection.classList.remove('hidden');
            emailInput.disabled = true;
            passwordInput.disabled = true;
            loginBtn.disabled = true;
            otpInput.focus();
            startResendTimer();
        }

        function hideOTPSection() {
            otpSection.classList.add('hidden');
            emailInput.disabled = false;
            passwordInput.disabled = false;
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
            resendBtn.style.opacity = '0.5';
            timerEl.textContent = 'Resend available in ' + resendCountdown + 's';
            resendTimer = setInterval(() => {
                resendCountdown--;
                timerEl.textContent = 'Resend available in ' + resendCountdown + 's';
                if (resendCountdown <= 0) {
                    clearInterval(resendTimer);
                    resendTimer = null;
                    resendBtn.disabled = false;
                    resendBtn.style.opacity = '1';
                    timerEl.textContent = 'Ready to resend';
                }
            }, 1000);
        }

        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const refreshToken = urlParams.get('refresh_token');

        if (token) {
            localStorage.setItem('access_token', token);
            if (refreshToken) localStorage.setItem('refresh_token', refreshToken);
            window.history.replaceState({}, document.title, window.location.pathname);
            window.location.href = '/dashboard';
            return;
        }

        const errorParam = urlParams.get('error');
        if (errorParam === 'device_in_use') {
            showMessage('This device is currently in use by another account. Please logout first.', 'error');
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            hideMessage();

            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

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
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Logging in...';

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
                        loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Log In';
                        return;
                    }
                    showMessage(data.error || data.message || 'Login failed. Please try again.', 'error');
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Log In';
                    return;
                }

                if (data.access_token) {
                    localStorage.setItem('access_token', data.access_token);
                    if (data.refresh_token) localStorage.setItem('refresh_token', data.refresh_token);
                }

                showMessage('Login successful! Redirecting…', 'success');

                const redirectUrl = data.redirect_url || '/dashboard';

                setTimeout(function() {
                    window.location.href = redirectUrl;
                }, 1000);

            } catch (err) {
                console.error('Login error:', err);
                showMessage('Network error – please check your connection and try again.', 'error');
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i> Log In';
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
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                const response = await fetch('/api/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
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
                passwordInput.value = '';
                otpInput.value = '';
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify';

                setTimeout(function() {
                    window.location.href = '/dashboard';
                }, 1500);

            } catch (error) {
                console.error('OTP verification error:', error);
                showMessage('Network error, please try again.', 'error');
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify';
            }
        });

        resendBtn.addEventListener('click', async function() {
            resendBtn.disabled = true;
            resendBtn.textContent = 'Sending...';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                const response = await fetch('/api/resend-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
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
                console.error('Resend OTP error:', error);
                showMessage('Network error, please try again.', 'error');
            } finally {
                resendBtn.textContent = 'Resend OTP';
                resendBtn.disabled = false;
            }
        });

        backToLoginBtn.addEventListener('click', function() {
            hideOTPSection();
            hideMessage();
            otpInput.value = '';
            passwordInput.value = '';
            emailInput.focus();
        });

        otpInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });

        otpInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (!verifyBtn.disabled) verifyBtn.click();
            }
        });

        const savedEmail = localStorage.getItem('pending_login_verification');
        if (savedEmail) {
            setTimeout(() => {
                emailInput.value = savedEmail;
                showOTPSection(savedEmail);
                showMessage('Please verify your email to continue.', 'warning');
                localStorage.removeItem('pending_login_verification');
            }, 500);
        }

    })();
    </script>
</body>
</html>