<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Dashboard - Fitcoin</title>
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
            0%, 100% {
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
            0%, 100% {
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
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .btn-1 { animation-delay: 0s; }
        .btn-2 { animation-delay: 0.5s; }
        .btn-3 { animation-delay: 1s; }
        .btn-4 { animation-delay: 1.5s; }
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
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold">Welcome, {{ $user->first_name }}!</h2>
                        <p class="text-gray-400 text-sm">Your fitness journey continues</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white transition">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="glass-card p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-neonpink/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-shoe-prints text-neonpink text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Today's Steps</p>
                                <p class="text-2xl font-bold">14,250</p>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-coins text-green-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Coins Earned</p>
                                <p class="text-2xl font-bold">2,450</p>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-trophy text-yellow-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Achievements</p>
                                <p class="text-2xl font-bold">12</p>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-fire text-purple-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">Streak</p>
                                <p class="text-2xl font-bold">7 Days</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 glass-card p-4 rounded-xl">
                    <h3 class="font-bold mb-2">Recent Activity</h3>
                    <div class="space-y-2 text-sm text-gray-400">
                        <p><i class="fas fa-circle text-green-500 text-xs mr-2"></i> Completed daily steps goal</p>
                        <p><i class="fas fa-circle text-blue-500 text-xs mr-2"></i> Earned 50 bonus coins</p>
                        <p><i class="fas fa-circle text-yellow-500 text-xs mr-2"></i> Unlocked new achievement</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>