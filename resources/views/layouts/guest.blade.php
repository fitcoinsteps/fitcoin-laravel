<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fitcoin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-black text-white min-h-screen overflow-x-hidden">
    <!-- Guest Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/50 backdrop-blur-lg border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="/" class="text-2xl font-bold bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    Fitcoin
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-7">
                    <a href="/" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Home
                    </a>

                    <a href="/about" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        About
                    </a>

                    <a href="/pages/how-it-works" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        How It Works
                    </a>

                    <a href="/pages/ways-to-earn" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Ways to Earn
                    </a>

                    <a href="/pages/rewards" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Rewards
                    </a>

                    <a href="/pages/tiers" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Tiers
                    </a>

                    <a href="/pages/faq" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        FAQ
                    </a>

                    <a href="/contact" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Contact
                    </a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    <a href="/login" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition">
                        Login
                    </a>

                    <a href="/register" class="px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition shadow-lg shadow-purple-500/20">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Guest Footer -->
    <footer class="bg-black py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent mb-4">Fitcoin</h3>
                    <p class="text-gray-400 text-sm">Your ultimate fitness companion. Transform your life today.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Features</a></li>
                        <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition">Download</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/about" class="hover:text-white transition">About</a></li>
                        <li><a href="/contact" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="text-xl">𝕏</span>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="text-xl">f</span>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="text-xl">📷</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="text-center pt-8 border-t border-white/10">
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} Fitcoin. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>