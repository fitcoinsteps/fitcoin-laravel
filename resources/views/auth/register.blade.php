<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Fitcoin</title>

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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #0a0512;
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* ===== Animated Background ===== */
        .animated-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(236, 72, 153, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(139, 92, 246, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 100%, rgba(212, 175, 55, 0.03) 0%, transparent 50%);
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }

        /* ===== Logo in Background ===== */
        .bg-logo {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(2.5);
            z-index: 0;
            opacity: 0.04;
            pointer-events: none;
            filter: blur(6px);
            animation: logoFloat 15s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translate(-50%, -50%) scale(2.5) rotate(0deg); }
            50% { transform: translate(-50%, -50%) scale(2.7) rotate(5deg); }
        }

        .bg-logo img {
            width: 500px;
            height: 500px;
            object-fit: contain;
        }

        /* ===== Floating Orbs ===== */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            opacity: 0.3;
            animation: orbFloat 8s ease-in-out infinite;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            background: rgba(236, 72, 153, 0.15);
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 250px;
            height: 250px;
            background: rgba(139, 92, 246, 0.12);
            bottom: -80px;
            left: -80px;
            animation-delay: 2s;
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(212, 175, 55, 0.08);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 4s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* ===== Main Container ===== */
        .register-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 60px;
            width: 100%;
            max-width: 1100px;
            z-index: 1;
            position: relative;
        }

        /* ===== Left Side - Hero Section ===== */
        .hero-section {
            flex: 1;
            max-width: 400px;
            animation: fadeSlideRight 0.8s ease forwards;
        }

        @keyframes fadeSlideRight {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .hero-section .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(236, 72, 153, 0.08);
            border: 1px solid rgba(236, 72, 153, 0.12);
            border-radius: 50px;
            font-size: 11px;
            color: #ec4899;
            letter-spacing: 0.5px;
            margin-bottom: 24px;
        }

        .hero-section .hero-badge i {
            font-size: 12px;
        }

        .hero-section h1 {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 16px;
        }

        .hero-section h1 .highlight {
            background: linear-gradient(135deg, #ec4899, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-section p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.4);
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .hero-section .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .hero-section .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: default;
        }

        .hero-section .feature-item:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(236, 72, 153, 0.08);
            transform: translateY(-2px);
        }

        .hero-section .feature-item .icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .hero-section .feature-item .icon.pink { background: rgba(236, 72, 153, 0.1); color: #ec4899; }
        .hero-section .feature-item .icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
        .hero-section .feature-item .icon.gold { background: rgba(212, 175, 55, 0.1); color: #d4af37; }
        .hero-section .feature-item .icon.green { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        .hero-section .feature-item .text {
            font-size: 12px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
        }

        .hero-section .feature-item .text span {
            display: block;
            font-size: 10px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.25);
            margin-top: 1px;
        }

        /* ===== Right Side - Card ===== */
        .glass-card {
            background: rgba(23, 10, 35, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 40px 38px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.04);
            animation: fadeSlideLeft 0.8s ease forwards;
            position: relative;
            overflow: hidden;
        }

        @keyframes fadeSlideLeft {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .glass-card::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 24px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.1), rgba(139, 92, 246, 0.05), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .glass-card .card-glow {
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse, rgba(236, 72, 153, 0.03), transparent 70%);
            pointer-events: none;
        }

        /* ===== Brand ===== */
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .brand-logo .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(139, 92, 246, 0.15));
            border: 1px solid rgba(236, 72, 153, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #ec4899;
        }

        .brand-logo span {
            font-size: 20px;
            font-weight: 700;
            color: white;
        }

        .brand-logo span small {
            font-weight: 400;
            color: rgba(255, 255, 255, 0.2);
        }

        .card-header {
            margin-bottom: 22px;
        }

        .card-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: white;
        }

        .card-header p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
            margin-top: 2px;
        }

        /* ===== Inputs ===== */
        .input-group {
            margin-bottom: 12px;
            position: relative;
        }

        .input-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.12);
            font-size: 14px;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .input-group input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            color: white;
            font-size: 13px;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group input:focus {
            border-color: rgba(236, 72, 153, 0.2);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.04);
        }

        .input-group input:focus ~ .input-icon {
            color: #ec4899;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.15);
        }

        .input-group input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ===== Password Strength ===== */
        .strength-wrapper {
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .strength-bar {
            flex: 1;
            height: 3px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.04);
            overflow: hidden;
        }

        .strength-bar .fill {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: all 0.4s ease;
        }

        .strength-text {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.15);
            white-space: nowrap;
            min-width: 50px;
            text-align: right;
        }

        /* ===== Button ===== */
        .btn-primary {
            width: 100%;
            padding: 13px;
            border-radius: 12px;
            background: linear-gradient(135deg, #d946ef 0%, #ec4899 100%);
            color: white;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(236, 72, 153, 0.15);
            position: relative;
            overflow: hidden;
            margin-top: 4px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(236, 72, 153, 0.25);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary .ripple {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
            transform: translateX(-100%);
            animation: ripple 3s infinite;
        }

        @keyframes ripple {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* ===== Messages ===== */
        .alert-msg {
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 14px;
            display: none;
            text-align: center;
        }

        .alert-msg.success {
            display: block;
            background: rgba(34, 197, 94, 0.06);
            border: 1px solid rgba(34, 197, 94, 0.1);
            color: #4ade80;
        }

        .alert-msg.error {
            display: block;
            background: rgba(239, 68, 68, 0.06);
            border: 1px solid rgba(239, 68, 68, 0.1);
            color: #f87171;
        }

        /* ===== Social ===== */
        .social-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 16px 0 14px;
        }

        .social-divider hr {
            flex: 1;
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
        }

        .social-divider span {
            color: rgba(255, 255, 255, 0.1);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .social-row {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .social-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.01);
            text-decoration: none;
        }

        .social-btn:hover {
            border-color: rgba(236, 72, 153, 0.15);
            color: white;
            transform: translateY(-2px);
            background: rgba(236, 72, 153, 0.04);
        }

        .social-btn .fa-google-plus-g { color: #ea4335; }
        .social-btn .fa-apple { color: #fff; }

        /* ===== Terms ===== */
        .terms-group {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 4px 0 12px;
        }

        .terms-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin-top: 2px;
            flex-shrink: 0;
            accent-color: #ec4899;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 4px;
            cursor: pointer;
        }

        .terms-group label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: color 0.3s ease;
            line-height: 1.4;
        }

        .terms-group label:hover {
            color: rgba(255, 255, 255, 0.4);
        }

        .terms-group label a {
            color: #ec4899;
            text-decoration: none;
        }

        .terms-group label a:hover {
            text-decoration: underline;
        }

        /* ===== Footer ===== */
        .card-footer {
            margin-top: 16px;
            text-align: center;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.2);
        }

        .card-footer a {
            color: #ec4899;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .card-footer a:hover {
            text-decoration: underline;
        }

        /* ===== Responsive ===== */
        @media (max-width: 1024px) {
            .register-wrapper {
                flex-direction: column;
                gap: 30px;
                max-width: 480px;
            }

            .hero-section {
                max-width: 100%;
                text-align: center;
                order: 2;
            }

            .hero-section .hero-badge {
                margin: 0 auto 20px;
            }

            .hero-section h1 {
                font-size: 32px;
            }

            .hero-section .features-grid {
                grid-template-columns: 1fr 1fr;
                max-width: 400px;
                margin: 0 auto;
            }

            .glass-card {
                order: 1;
                padding: 30px 24px;
            }

            .bg-logo img {
                width: 300px;
                height: 300px;
            }

            .orb-1, .orb-2, .orb-3 {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .glass-card {
                padding: 22px 16px;
                border-radius: 18px;
            }

            .hero-section h1 {
                font-size: 26px;
            }

            .hero-section .features-grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .hero-section .feature-item {
                padding: 10px 12px;
            }

            .hero-section .feature-item .text {
                font-size: 11px;
            }

            .input-group input {
                padding: 11px 12px 11px 38px;
                font-size: 12px;
            }

            .btn-primary {
                padding: 12px;
                font-size: 13px;
            }

            .brand-logo span {
                font-size: 18px;
            }

            .card-header h2 {
                font-size: 19px;
            }
        }
    </style>
</head>
<body>

    <!-- ===== Animated Background ===== -->
    <div class="animated-bg"></div>

    <!-- ===== Background Logo ===== -->
    <div class="bg-logo">
        <img src="{{ asset('images/FitCoin_metallic_coin_emblem_logo_202607311418.jpeg') }}" alt="Fitcoin Logo">
    </div>

    <!-- ===== Orbs ===== -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- ===== Main Wrapper ===== -->
    <div class="register-wrapper">

        <!-- ===== Left Side - Hero ===== -->
        <div class="hero-section">
            <div class="hero-badge">
                <i class="fas fa-rocket"></i>
                Join 50,000+ active users
            </div>

            <h1>
                Start Earning<br>
                <span class="highlight">Every Step Counts</span>
            </h1>

            <p>
                Create your account and start earning rewards for your daily activity.
                Get paid for staying active and healthy.
            </p>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="icon pink"><i class="fas fa-walking"></i></div>
                    <div class="text">
                        Track Steps
                        <span>Monitor your daily activity</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon gold"><i class="fas fa-coins"></i></div>
                    <div class="text">
                        Earn Coins
                        <span>Get rewarded for every step</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon purple"><i class="fas fa-trophy"></i></div>
                    <div class="text">
                        Win Prizes
                        <span>Compete and win rewards</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="icon green"><i class="fas fa-shield-alt"></i></div>
                    <div class="text">
                        Secure & Safe
                        <span>Your data is protected</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Right Side - Card ===== -->
        <div class="glass-card">
            <div class="card-glow"></div>

            <!-- Brand -->
            <div class="brand-logo">
                <div class="logo-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <span>Fitcoin <small>• Register</small></span>
            </div>

            <div class="card-header">
                <h2>Create Account</h2>
                <p>Start your fitness journey today</p>
            </div>

            <!-- Message -->
            <div id="message" class="alert-msg"></div>

            <!-- Form -->
            <form id="register-form">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="input-group">
                        <input type="text" id="first_name" placeholder="First Name" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    <div class="input-group">
                        <input type="text" id="last_name" placeholder="Last Name" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="input-group">
                    <input type="email" id="email" placeholder="Email Address" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>

                <div class="input-group">
                    <input type="text" id="phone" placeholder="Phone (optional)">
                    <i class="fas fa-phone input-icon"></i>
                </div>

                <div class="input-group">
                    <input type="password" id="password" placeholder="Password" required minlength="6">
                    <i class="fas fa-lock input-icon"></i>
                </div>
                <div class="strength-wrapper">
                    <div class="strength-bar">
                        <div class="fill" id="strengthBar"></div>
                    </div>
                    <span class="strength-text" id="strengthText">Weak</span>
                </div>

                <div class="input-group">
                    <input type="password" id="password_confirmation" placeholder="Confirm Password" required minlength="6">
                    <i class="fas fa-check-circle input-icon"></i>
                </div>

                <div class="terms-group">
                    <input type="checkbox" id="termsCheckbox" required>
                    <label for="termsCheckbox">
                        I agree to the <a href="#">Terms of Service</a> &amp; <a href="#">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" id="register-btn" class="btn-primary">
                    <span class="ripple"></span>
                    <i class="fas fa-user-plus mr-2"></i> Create Account
                </button>
            </form>

            <!-- Social -->
            <div class="social-divider">
                <hr>
                <span>or continue with</span>
                <hr>
            </div>

            <div class="social-row">
                <a href="/api/auth/google" class="social-btn" title="Sign up with Google">
                    <i class="fa-brands fa-google-plus-g"></i>
                </a>
                <a href="/api/auth/apple" class="social-btn" title="Sign up with Apple">
                    <i class="fa-brands fa-apple"></i>
                </a>
            </div>

            <div class="card-footer">
                Already have an account? <a href="/login">Sign In</a>
            </div>
        </div>
    </div>

    <script>
        // ===== Password Strength =====
        (function() {
            const input = document.getElementById('password');
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');

            const levels = [
                { label: 'Weak', color: '#ef4444', width: '20%' },
                { label: 'Weak', color: '#ef4444', width: '40%' },
                { label: 'Fair', color: '#f59e0b', width: '60%' },
                { label: 'Good', color: '#fbbf24', width: '80%' },
                { label: 'Strong', color: '#34d399', width: '90%' },
                { label: 'Very Strong', color: '#22c55e', width: '100%' }
            ];

            function checkStrength(password) {
                let score = 0;
                if (password.length >= 6) score++;
                if (password.length >= 10) score++;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score++;
                if (password.match(/\d/)) score++;
                if (password.match(/[^a-zA-Z\d]/)) score++;
                return Math.min(score, 5);
            }

            input.addEventListener('input', function() {
                const strength = checkStrength(this.value);
                const level = levels[strength];
                bar.style.width = level.width;
                bar.style.background = level.color;
                text.textContent = level.label;
                text.style.color = level.color;
            });
        })();

        // ===== Registration Logic =====
        (function() {
            const form = document.getElementById('register-form');
            const message = document.getElementById('message');
            const btn = document.getElementById('register-btn');
            const terms = document.getElementById('termsCheckbox');

            function showMessage(text, type = 'success') {
                message.textContent = text;
                message.className = 'alert-msg ' + type;
            }

            function hideMessage() {
                message.className = 'alert-msg';
                message.textContent = '';
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideMessage();

                if (!terms.checked) {
                    showMessage('Please agree to the Terms of Service.', 'error');
                    return;
                }

                const payload = {
                    first_name: document.getElementById('first_name').value.trim(),
                    last_name: document.getElementById('last_name').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    phone: document.getElementById('phone').value.trim(),
                    password: document.getElementById('password').value,
                    password_confirmation: document.getElementById('password_confirmation').value
                };

                if (!payload.first_name || !payload.last_name || !payload.email || !payload.password) {
                    showMessage('Please fill in all required fields.', 'error');
                    return;
                }

                if (payload.password !== payload.password_confirmation) {
                    showMessage('Passwords do not match.', 'error');
                    return;
                }

                if (payload.password.length < 6) {
                    showMessage('Password must be at least 6 characters.', 'error');
                    return;
                }

                try {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating Account...';

                    const response = await fetch('/api/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const errorMsg = data.message || data.error || 'Registration failed.';
                        showMessage(errorMsg, 'error');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-user-plus mr-2"></i> Create Account';
                        return;
                    }

                    showMessage('✅ Account created! Redirecting to verification...', 'success');

                    setTimeout(() => {
                        window.location.href = `/verify-otp?email=${encodeURIComponent(payload.email)}`;
                    }, 1500);

                } catch (error) {
                    console.error('Registration error:', error);
                    showMessage('Network error. Please try again.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-user-plus mr-2"></i> Create Account';
                }
            });

            // Enter key support
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const active = document.activeElement;
                    if (active && active.closest('#register-form') && !btn.disabled) {
                        form.dispatchEvent(new Event('submit'));
                    }
                }
            });
        })();
    </script>

</body>
</html>