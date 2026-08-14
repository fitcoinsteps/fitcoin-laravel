<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitcoin - Unlock Your Fitness Value</title>
    
    <!-- ====== علامة AdSense الوصفية ====== -->
    <meta name="google-adsense-account" content="ca-pub-3446681960932357">
    
    <!-- ====== كود AdSense الأساسي (مرة واحدة فقط) ====== -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3446681960932357"
         crossorigin="anonymous"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        neonpink: '#ec4899', neonpurple: '#8b5cf6', neongold: '#d4af37', neongreen: '#22c55e',
                        darkbg: '#0a0512', cardbg: '#1c0929'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@1.37.1/tsparticles.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background: #0a0512; color: white; overflow-x: hidden;}
        .glow-text { text-shadow: 0 0 25px rgba(236, 72, 153, 0.6); }
        .glass-card { background: rgba(28, 9, 41, 0.5); backdrop-filter: blur(14px); border: 1px solid rgba(236, 72, 153, 0.2); transition: all 0.3s ease; }
        .glass-card:hover { border-color: rgba(236, 72, 153, 0.6); transform: translateY(-5px); box-shadow: 0 10px 40px rgba(139, 92, 246, 0.3); }
        .phone-perspective-wrapper { perspective: 1200px; display: flex; justify-content: center; align-items: center; position: relative; z-index: 10; animation: breathePhone 4.5s ease-in-out infinite; }
        @keyframes breathePhone { 0%, 100% { transform: scale(1) rotateY(-25deg) rotateX(10deg) rotateZ(5deg); } 50% { transform: scale(1.03) rotateY(-15deg) rotateX(5deg) rotateZ(3deg); } }
        .phone-frame { background: #150822; border-radius: 45px; border: 4px solid rgba(255,255,255,0.15); box-shadow: 0 20px 100px rgba(236, 72, 153, 0.6), inset 0 0 20px rgba(139,92,246,0.2); overflow: hidden; position: relative; max-width: 350px; width: 100%; transform: rotateY(-25deg) rotateX(10deg) rotateZ(5deg) scale(0.9); transition: transform 0.6s; }
        .phone-frame:hover { transform: rotateY(-15deg) rotateX(5deg) rotateZ(3deg) scale(0.95); }
        .step-counter-container { position: relative; width: 160px; height: 160px; margin: 0 auto; display: flex; align-items: center; justify-content: center; }
        .app-ring-chart { position: absolute; inset: 0; border-radius: 50%; background: conic-gradient(from 180deg, #8b5cf6 0%, #06b6d4 25%, #ec4899 50%, #d4af37 75%, #8b5cf6 100%); box-shadow: inset 0 0 40px rgba(236, 72, 153, 0.6), 0 0 80px rgba(139, 92, 246, 0.5); transform: rotate(-20deg); animation: ringPulse 2s ease-in-out infinite; }
        @keyframes ringPulse { 0%, 100% { transform: rotate(-20deg) scale(1); opacity: 0.9; } 50% { transform: rotate(-10deg) scale(1.06); opacity: 1; } }
        .app-ring-inner { position: relative; z-index: 4; width: 120px; height: 120px; border-radius: 50%; background: #150822; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid rgba(236, 72, 153, 0.5); box-shadow: inset 0 0 40px rgba(0,0,0,0.9); }
        .particle-field { position: absolute; inset: -40px; z-index: 1; pointer-events: none; overflow: visible; }
        .sparkle { position: absolute; background: #ec4899; border-radius: 50%; box-shadow: 0 0 15px #ec4899, 0 0 30px #ec4899; animation: sparkleExplosion var(--duration) linear infinite; opacity: 0; }
        @keyframes sparkleExplosion { 0% { transform: translate(0, 0) scale(1); opacity: 1; } 100% { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; } }
        .app-btn { background: rgba(44, 18, 66, 0.85); border: 1px solid rgba(236, 72, 153, 0.3); border-radius: 16px; transition: all 0.3s; animation: floatButton 3s ease-in-out infinite; }
        .app-btn:hover { background: rgba(236, 72, 153, 0.25); border-color: #ec4899; transform: scale(1.05) !important; }
        @keyframes floatButton { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .btn-1 { animation-delay: 0s; } .btn-2 { animation-delay: 0.5s; } .btn-3 { animation-delay: 1s; } .btn-4 { animation-delay: 1.5s; }
        .flying-money { position: absolute; color: #22c55e; text-shadow: 0 0 20px rgba(34, 197, 94, 0.7), 0 0 50px rgba(34, 197, 94, 0.3); animation: flyMoney 8s linear infinite; z-index: 5; }
        @keyframes flyMoney { 0% { transform: translateY(0) rotate(0deg) scale(0.8) translateX(0); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-400px) rotate(720deg) scale(1.3) translateX(150px); opacity: 0; } }
        .store-btn { display: flex; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 8px 14px; color: white; transition: all 0.3s; backdrop-filter: blur(5px); }
        .store-btn:hover { background: rgba(255,255,255,0.15); transform: translateY(-3px); border-color: #ec4899; box-shadow: 0 10px 20px rgba(236,72,153,0.2); }
        .main-cta-btn { background: linear-gradient(135deg, #ec4899, #8b5cf6); box-shadow: 0 0 40px rgba(236, 72, 153, 0.5); transition: all 0.3s ease; }
        .main-cta-btn:hover { box-shadow: 0 0 80px rgba(236, 72, 153, 0.8); transform: scale(1.05); }
        .nav-icon-active { color: #ec4899; filter: drop-shadow(0 0 8px #ec4899); }
        .nav-icon-inactive { color: #6b4a7d; transition: all 0.3s; }
        
        /* ====== تنسيق الإعلانات ====== */
        .ad-container {
            background: rgba(28, 9, 41, 0.3);
            border-radius: 16px;
            padding: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            min-height: 90px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .ad-container ins {
            width: 100% !important;
        }
        .ad-label {
            font-size: 10px;
            color: rgba(255,255,255,0.2);
            text-align: center;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
    </style>
</head>
<body class="min-h-screen relative flex flex-col">

    <!-- خلفية أساسية -->
    <div class="fixed inset-0 bg-[radial-gradient(circle_800px_at_50%_50%,_#4c1d95_0%,_transparent_80%)] opacity-60 z-0 pointer-events-none"></div>
    <div id="tsparticles" class="fixed inset-0 z-0 pointer-events-none"></div>

    <!-- ==================== القائمة العلوية ==================== -->
    @include('partials.navbar')

    <!-- ==================== قسم البطل (HERO) ==================== -->
    <section class="relative z-20 container mx-auto px-6 pt-6 pb-12 flex flex-col lg:flex-row items-center justify-center gap-12 min-h-[80vh] overflow-hidden">
        
        <!-- اللوغو الكبير كخلفية -->
        <div class="absolute inset-0 flex justify-center items-center pointer-events-none z-0 opacity-10 md:opacity-15">
            <img src="{{ asset('images/FitCoin_metallic_coin_emblem_logo_202607311418.jpeg') }}" alt="Background Logo" class="w-[150%] max-w-[800px] lg:max-w-[1000px] object-contain blur-[2px]">
        </div>

        <!-- المحتوى النصي والأزرار -->
        <div class="relative z-10 space-y-6 max-w-xl text-center lg:text-left flex flex-col items-center lg:items-start">
            <h1 class="text-5xl md:text-7xl font-black leading-tight tracking-tight drop-shadow-[0_4px_20px_rgba(0,0,0,0.8)]">
                Unlock Fitness <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-neonpink to-neongold glow-text">Value with Fitcoin</span>
            </h1>
            <p class="text-xl text-gray-300 leading-relaxed font-light max-w-lg drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]">Turn every step into a crypto reward. Track, Earn, and Redeem.</p>

            <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-4">
                <a href="#" class="store-btn"><i class="fab fa-apple text-3xl"></i><div class="text-left leading-tight"><div class="text-[8px] text-gray-400 uppercase tracking-wider">App Store</div><div class="text-sm font-bold">Download</div></div></a>
                <a href="#" class="store-btn"><i class="fab fa-google-play text-3xl"></i><div class="text-left leading-tight"><div class="text-[8px] text-gray-400 uppercase tracking-wider">Google Play</div><div class="text-sm font-bold">Get it</div></div></a>
            </div>

            <div class="w-full flex justify-center lg:justify-start mt-2">
                <a href="{{ route('login') }}" class="main-cta-btn w-full sm:w-auto px-10 py-4 rounded-full text-white font-bold text-lg shadow-[0_0_30px_rgba(236,72,153,0.6)] transition transform"><i class="fas fa-rocket mr-2"></i> Log In / Sign Up</a>
            </div>
        </div>

        <!-- الموبايل المائل + إعلان 1 -->
        <div class="relative z-10 flex flex-col items-center w-full max-w-md mt-10 lg:mt-0">
            <div class="flying-money" style="top: -20%; left: -10%; animation-delay: 0s; font-size: 3.5rem;"><i class="fas fa-money-bill-wave"></i></div>
            <div class="flying-money" style="bottom: -10%; right: -20%; animation-delay: 3s; font-size: 4.5rem;"><i class="fas fa-coins"></i></div>
            <div class="flying-money" style="bottom: 30%; left: -30%; animation-delay: 1.5s; font-size: 2.5rem;"><i class="fas fa-sack-dollar"></i></div>
            
            <div class="absolute inset-0 bg-gradient-to-br from-neonpink/40 to-neonpurple/40 rounded-[60px] blur-3xl transform scale-110 z-[-1] animate-pulse"></div>
            
            <div class="phone-perspective-wrapper w-full">
                <div class="phone-frame w-full relative shadow-2xl">
                    <div class="px-5 pt-3 pb-2 flex justify-between items-center text-gray-400 text-[10px] font-bold">
                        <span>9:16</span><div class="flex gap-1.5"><i class="fas fa-signal text-[10px]"></i><i class="fas fa-wifi text-[10px]"></i><i class="fas fa-battery-full text-[10px] text-green-400"></i></div>
                    </div>
                    <div class="p-5 space-y-4 relative">
                        <div class="flex justify-between items-start">
                            <div><div class="text-gray-400 text-sm">Good Morning,</div><div class="text-2xl font-bold">Sarah!</div></div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-neonpink to-neonpurple border-2 border-neonpink/50 shadow-[0_0_15px_rgba(236,72,153,0.5)]"><img src="https://i.pravatar.cc/150?img=5" class="w-full h-full rounded-full object-cover"></div>
                        </div>
                        <div class="step-counter-container">
                            <div class="particle-field" id="sparkleField"></div>
                            <div class="app-ring-chart"><div class="app-ring-overlay"></div></div>
                            <div class="app-ring-inner flex-col gap-0.5">
                                <span class="text-neonpink text-[11px] font-semibold tracking-wider">71.25%</span>
                                <span class="text-2xl font-black text-white tracking-tight">14,250</span>
                                <span class="text-[10px] text-gray-400 font-medium tracking-wide">STEPS</span>
                            </div>
                        </div>
                        <div class="text-center text-[10px] text-gray-500 -mt-2">Wednesday, November 15</div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="app-btn btn-1 p-3 flex flex-col items-center text-center gap-1 min-h-[100px] justify-center relative">
                                <i class="fas fa-shoe-prints text-2xl text-neonpink drop-shadow-[0_0_10px_rgba(236,72,153,0.6)]"></i>
                                <div class="font-bold text-xs leading-tight text-white">Double Steps</div>
                                <div class="text-[9px] text-gray-400 leading-tight">Watch ads!</div>
                                <div class="absolute bottom-2 right-2 w-5 h-5 rounded-full bg-gradient-to-br from-neonpink/40 to-transparent flex items-center justify-center text-[8px] text-white border border-white/10"><i class="fas fa-play"></i></div>
                            </div>
                            <div class="app-btn btn-2 p-3 flex flex-col items-center text-center gap-1 min-h-[100px] justify-center relative">
                                <i class="fas fa-coins text-2xl text-neonpink drop-shadow-[0_0_10px_rgba(236,72,153,0.6)]"></i>
                                <div class="font-bold text-xs leading-tight text-white">Earn Coins</div>
                                <div class="text-[9px] text-gray-400 leading-tight">Daily Quests!</div>
                                <div class="absolute bottom-2 right-2 w-5 h-5 rounded-full border border-neonpink/50 flex items-center justify-center text-[8px] text-neonpink"><i class="fas fa-crosshairs"></i></div>
                            </div>
                            <div class="app-btn btn-3 p-3 flex flex-col items-center text-center gap-1 min-h-[100px] justify-center relative">
                                <i class="fas fa-money-bill-wave text-2xl text-neonpink drop-shadow-[0_0_10px_rgba(236,72,153,0.6)]"></i>
                                <div class="font-bold text-xs leading-tight text-white">Redeem Cash</div>
                                <div class="text-[9px] text-gray-400 leading-tight">Withdraw safely!</div>
                                <div class="absolute bottom-2 right-2 w-5 h-5 rounded-full border border-neonpink/50 flex items-center justify-center text-[8px] text-neonpink"><i class="fas fa-wallet"></i></div>
                            </div>
                            <div class="app-btn btn-4 p-3 flex flex-col items-center text-center gap-1 min-h-[100px] justify-center relative">
                                <i class="fas fa-user-plus text-2xl text-neonpink drop-shadow-[0_0_10px_rgba(236,72,153,0.6)]"></i>
                                <div class="font-bold text-xs leading-tight text-white">Profile & Invite</div>
                                <div class="text-[9px] text-gray-400 leading-tight">Invite Friends!</div>
                                <div class="absolute bottom-2 right-2 w-5 h-5 rounded-full border border-neonpink/50 flex items-center justify-center text-[8px] text-neonpink"><i class="fas fa-user-plus"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-gradient-to-t from-[#0a0512] to-transparent flex justify-between items-center text-lg relative">
                        <i class="fas fa-home nav-icon-active animate-pulse"></i>
                        <i class="fas fa-heartbeat nav-icon-inactive hover:text-neonpink transition"></i>
                        <i class="fas fa-chart-line nav-icon-inactive hover:text-neonpink transition"></i>
                        <i class="fas fa-user nav-icon-inactive hover:text-neonpink transition"></i>
                        <div class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-32 h-1 bg-gray-600 rounded-full"></div>
                    </div>
                </div>
            </div>
            
            <!-- ============================================================ -->
            <!-- ====== إعلان 1: تحت الموبايل (ads_under_mobile) ====== -->
            <!-- data-ad-slot: 5511964470 -->
            <!-- ============================================================ -->
            <div class="ad-container w-full mt-6">
                <div class="ad-label">— Advertisement —</div>
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3446681960932357"
                     data-ad-slot="5511964470"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </div>
    </section>

    <!-- ==================== قسم الإحصائيات ==================== -->
    <section class="relative z-20 container mx-auto px-6 py-16 border-t border-white/5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center max-w-5xl mx-auto">
            <div class="glass-card p-8 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md">
                <div class="text-5xl font-black text-neonpink drop-shadow-[0_0_15px_rgba(236,72,153,0.5)]">2M+</div>
                <div class="text-gray-400 text-sm mt-3 uppercase tracking-widest font-semibold">Steps Verified Daily</div>
            </div>
            <div class="glass-card p-8 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md">
                <div class="text-5xl font-black text-neongold drop-shadow-[0_0_15px_rgba(212,175,55,0.5)]">$500k+</div>
                <div class="text-gray-400 text-sm mt-3 uppercase tracking-widest font-semibold">Earned by Users</div>
            </div>
            <div class="glass-card p-8 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md">
                <div class="text-5xl font-black text-neonpurple drop-shadow-[0_0_15px_rgba(139,92,246,0.5)]">50k+</div>
                <div class="text-gray-400 text-sm mt-3 uppercase tracking-widest font-semibold">Active Community</div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ====== إعلان 2: بين الإحصائيات وكيف يعمل (ads_between_dep) ====== -->
    <!-- data-ad-slot: 4198882803 -->
    <!-- ============================================================ -->
    <section class="relative z-20 container mx-auto px-6 py-8">
        <div class="ad-container max-w-4xl mx-auto">
            <div class="ad-label">— Sponsored —</div>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-3446681960932357"
                 data-ad-slot="4198882803"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </section>

    <!-- ==================== قسم كيف يعمل ==================== -->
    <section class="relative z-20 container mx-auto px-6 py-16">
        <div class="text-center mb-14">
            <h2 class="text-4xl md:text-5xl font-black text-white">How <span class="text-transparent bg-clip-text bg-gradient-to-r from-neonpink to-neongold">Fitcoin</span> Works</h2>
            <p class="text-gray-400 mt-3 max-w-xl mx-auto">Start earning in just 3 simple steps. It's that easy.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- البطاقة 1 -->
            <div class="flex flex-col items-center text-center space-y-4 p-8 rounded-2xl glass-card border border-white/5 bg-cardbg/50 backdrop-blur-md transition hover:border-neonpink/30">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-neonpink/20 to-neonpurple/20 flex items-center justify-center text-4xl text-neonpink border border-neonpink/30 shadow-[0_0_20px_rgba(236,72,153,0.2)]">1</div>
                <h3 class="text-2xl font-bold text-white">Track & Walk</h3>
                <p class="text-gray-400 text-sm leading-relaxed max-w-xs">Open the app, allow step tracking, and just walk anywhere. Every step counts!</p>
            </div>
            
            <!-- البطاقة 2 (مميزة) -->
            <div class="flex flex-col items-center text-center space-y-4 p-8 rounded-2xl glass-card border border-white/5 bg-cardbg/50 backdrop-blur-md transition hover:border-neongold/30 transform md:scale-105 relative z-10">
                <div class="absolute -top-3 w-16 h-1 bg-gradient-to-r from-neongold to-neonpink rounded-full"></div>
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-neongold/20 to-neonpurple/20 flex items-center justify-center text-4xl text-neongold border border-neongold/30 shadow-[0_0_20px_rgba(212,175,55,0.2)]">2</div>
                <h3 class="text-2xl font-bold text-white">Earn Rewards</h3>
                <p class="text-gray-400 text-sm leading-relaxed max-w-xs">Watch ads, complete daily quests, or challenge friends to supercharge your Fitcoin!</p>
            </div>
            
            <!-- البطاقة 3 -->
            <div class="flex flex-col items-center text-center space-y-4 p-8 rounded-2xl glass-card border border-white/5 bg-cardbg/50 backdrop-blur-md transition hover:border-neongreen/30">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-neongreen/20 to-neonpink/20 flex items-center justify-center text-4xl text-neongreen border border-neongreen/30 shadow-[0_0_20px_rgba(34,197,94,0.2)]">3</div>
                <h3 class="text-2xl font-bold text-white">Redeem & Cash Out</h3>
                <p class="text-gray-400 text-sm leading-relaxed max-w-xs">Convert your Fitcoin to real cash, gift cards, or crypto instantly via the wallet.</p>
            </div>
        </div>
    </section>

    <!-- ==================== آراء المستخدمين ==================== -->
    <section class="relative z-20 container mx-auto px-6 py-16 border-t border-white/5">
        <div class="text-center mb-14">
            <h2 class="text-4xl md:text-5xl font-black text-white">What Our <span class="text-transparent bg-clip-text bg-gradient-to-r from-neongold to-neonpink">Users Say</span></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <div class="glass-card p-6 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md text-center transition hover:border-neonpink/30">
                <div class="flex justify-center text-neongold mb-4 text-xl drop-shadow-[0_0_10px_rgba(212,175,55,0.3)]"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="text-gray-300 italic text-sm leading-relaxed">"I've earned over $200 just by walking to work every day. This app is literally paying me to stay healthy!"</p>
                <div class="mt-4 font-bold text-white text-sm flex items-center justify-center gap-2"><div class="w-6 h-6 rounded-full bg-gradient-to-br from-neonpink to-neonpurple text-[10px] flex items-center justify-center">JM</div> Jessica M.</div>
            </div>
            <div class="glass-card p-6 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md text-center transition hover:border-neongold/30 transform md:scale-105 z-10 relative">
                <div class="absolute -top-3 w-10 h-1 bg-gradient-to-r from-neongold to-neonpink rounded-full left-1/2 transform -translate-x-1/2"></div>
                <div class="flex justify-center text-neongold mb-4 text-xl drop-shadow-[0_0_10px_rgba(212,175,55,0.3)]"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="text-gray-300 italic text-sm leading-relaxed">"The 'Double Steps' feature is a game changer. Watching a single ad literally doubles my daily earnings."</p>
                <div class="mt-4 font-bold text-white text-sm flex items-center justify-center gap-2"><div class="w-6 h-6 rounded-full bg-gradient-to-br from-neongold to-neonpurple text-[10px] flex items-center justify-center">MT</div> Mark T.</div>
            </div>
            <div class="glass-card p-6 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md text-center transition hover:border-neonpurple/30">
                <div class="flex justify-center text-neongold mb-4 text-xl drop-shadow-[0_0_10px_rgba(212,175,55,0.3)]"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p class="text-gray-300 italic text-sm leading-relaxed">"The referral program is incredible. I invited my whole family and we're all earning Fitcoins together."</p>
                <div class="mt-4 font-bold text-white text-sm flex items-center justify-center gap-2"><div class="w-6 h-6 rounded-full bg-gradient-to-br from-neongreen to-neonpink text-[10px] flex items-center justify-center">AK</div> Anna K.</div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ====== إعلان 3: بين الآراء والدعوة الختامية (ads_another) ====== -->
    <!-- data-ad-slot: 6045533122 -->
    <!-- ============================================================ -->
    <section class="relative z-20 container mx-auto px-6 py-8">
        <div class="ad-container max-w-4xl mx-auto">
            <div class="ad-label">— Sponsored Content —</div>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-3446681960932357"
                 data-ad-slot="6045533122"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </section>

    <!-- ==================== دعوة ختامية (CTA) ==================== -->
    <section class="relative z-20 container mx-auto px-6 py-16 max-w-5xl">
        <div class="glass-card p-12 md:p-16 rounded-3xl border-2 border-neonpink/40 bg-gradient-to-br from-darkbg to-cardbg text-center relative overflow-hidden shadow-[0_0_60px_rgba(236,72,153,0.2)]">
            <div class="absolute top-0 right-0 w-80 h-80 bg-neonpink rounded-full blur-[100px] opacity-20 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-neonpurple rounded-full blur-[100px] opacity-20 pointer-events-none"></div>
            <h2 class="text-4xl md:text-5xl font-black text-white relative z-10">Ready to Start Earning?</h2>
            <p class="text-gray-300 mt-3 text-lg relative z-10 max-w-xl mx-auto">Join 50,000+ active users and turn your everyday steps into real rewards right now.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-6 relative z-10">
                <a href="#" class="px-10 py-5 main-cta-btn rounded-full text-white font-bold text-lg shadow-[0_0_40px_rgba(236,72,153,0.6)] hover:scale-105 transition transform"><i class="fas fa-download mr-3"></i> Download Now</a>
                <a href="{{ route('login') }}" class="px-10 py-5 border border-white/20 rounded-full text-white font-bold text-lg hover:bg-white/10 transition shadow-lg"><i class="fas fa-user-plus mr-3"></i> Create Account</a>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ====== إعلان 4: قبل التذييل (das_Inside) ====== -->
    <!-- data-ad-slot: 3419369784 -->
    <!-- ============================================================ -->
    <section class="relative z-20 container mx-auto px-6 py-8 border-t border-white/5">
        <div class="ad-container max-w-4xl mx-auto">
            <div class="ad-label">— Advertisement —</div>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-3446681960932357"
                 data-ad-slot="3419369784"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </section>

    <!-- ==================== قسم المجتمع ==================== -->
    <section class="relative z-20 container mx-auto px-6 py-16 border-t border-white/5">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-black text-white">Join Our <span class="text-transparent bg-clip-text bg-gradient-to-r from-neonpink to-neongold">Community</span></h2>
            <p class="text-gray-400 mt-3 max-w-xl mx-auto">Follow us on social media for challenges, updates, and exclusive giveaways!</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
            <a href="#" class="glass-card p-6 rounded-2xl border border-white/10 flex flex-col items-center justify-center gap-3 hover:border-neonpink/50 transition group">
                <i class="fab fa-facebook-f text-4xl text-white group-hover:text-neonpink transition"></i>
                <span class="text-sm font-bold text-gray-300">Facebook</span>
            </a>
            <a href="#" class="glass-card p-6 rounded-2xl border border-white/10 flex flex-col items-center justify-center gap-3 hover:border-neonpink/50 transition group">
                <i class="fab fa-instagram text-4xl text-white group-hover:text-neonpink transition"></i>
                <span class="text-sm font-bold text-gray-300">Instagram</span>
            </a>
            <a href="#" class="glass-card p-6 rounded-2xl border border-white/10 flex flex-col items-center justify-center gap-3 hover:border-neonpink/50 transition group">
                <i class="fab fa-x-twitter text-4xl text-white group-hover:text-neonpink transition"></i>
                <span class="text-sm font-bold text-gray-300">X (Twitter)</span>
            </a>
            <a href="#" class="glass-card p-6 rounded-2xl border border-white/10 flex flex-col items-center justify-center gap-3 hover:border-neonpink/50 transition group">
                <i class="fab fa-discord text-4xl text-white group-hover:text-neonpink transition"></i>
                <span class="text-sm font-bold text-gray-300">Discord</span>
            </a>
        </div>
    </section>

    <!-- ==================== قسم الأخبار ==================== -->
    <section class="relative z-20 container mx-auto px-6 py-16 border-t border-white/5">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-black text-white">Latest <span class="text-transparent bg-clip-text bg-gradient-to-r from-neongold to-neonpink">News</span></h2>
            <p class="text-gray-400 mt-3 max-w-xl mx-auto">Stay updated with the newest features and Fitcoin milestones.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="glass-card p-6 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md text-left group hover:border-neonpink/30">
                <div class="text-xs text-neonpink font-bold mb-2">NOV 15, 2024</div>
                <h3 class="text-xl font-bold text-white mb-2">Gold Tier Cash Out Now Live!</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Users in the Gold Tier can now instantly withdraw their Fitcoins to PayPal and Crypto.</p>
            </div>
            <div class="glass-card p-6 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md text-left group hover:border-neonpink/30 transform md:scale-105 z-10 relative">
                <div class="absolute -top-3 w-full h-1 bg-gradient-to-r from-neongold to-neonpink"></div>
                <div class="text-xs text-neongold font-bold mb-2">NOV 10, 2024</div>
                <h3 class="text-xl font-bold text-white mb-2">Global Walking Challenge</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Join the November Global Walking Challenge. Top 100 walkers split a 10,000 FC prize pool!</p>
            </div>
            <div class="glass-card p-6 rounded-2xl border border-white/10 bg-cardbg/50 backdrop-blur-md text-left group hover:border-neonpink/30">
                <div class="text-xs text-neonpink font-bold mb-2">NOV 05, 2024</div>
                <h3 class="text-xl font-bold text-white mb-2">App 2.0 Update Released</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Our new update includes a seamless UI, faster step detection, and an improved referral dashboard.</p>
            </div>
        </div>
    </section>

    <!-- ==================== تذييل الصفحة ==================== -->
    @include('partials.footer')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            tsParticles.load("tsparticles", {
                fpsLimit: 60,
                particles: {
                    number: { value: 60, density: { enable: true, value_area: 800 } },
                    color: { value: ["#ec4899", "#8b5cf6", "#ffffff"] },
                    shape: { type: "circle" },
                    opacity: { value: 0.6, random: true },
                    size: { value: 3, random: true },
                    move: { enable: true, speed: 1.5, direction: "none", out_mode: "out" }
                },
                retina_detect: true
            });
            
            const field = document.getElementById('sparkleField');
            if(field) {
                const numberOfSparks = 40; 
                for (let i = 0; i < numberOfSparks; i++) {
                    const spark = document.createElement('div');
                    spark.className = 'sparkle';
                    const size = Math.random() * 4 + 2;
                    spark.style.width = size + 'px'; spark.style.height = size + 'px';
                    const startX = Math.random() * 100; const startY = Math.random() * 100;
                    spark.style.left = startX + '%'; spark.style.top = startY + '%';
                    const angle = Math.random() * 360; const distance = Math.random() * 100 + 20;
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