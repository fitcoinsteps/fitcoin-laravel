<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fitcoin - User Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-black text-white min-h-screen overflow-x-hidden">
    <!-- User Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/50 backdrop-blur-lg border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="/user/dashboard" class="text-2xl font-bold bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    Fitcoin
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-7">
                    <a href="/user/dashboard" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Dashboard
                    </a>
                    <a href="/user/workouts" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Workouts
                    </a>
                    <a href="/user/progress" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Progress
                    </a>
                    <a href="/user/achievements" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Achievements
                    </a>
                    <a href="/user/profile" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Profile
                    </a>
                    <a href="/user/settings" class="text-sm font-medium text-gray-300 hover:text-white transition">
                        Settings
                    </a>
                </nav>

                <!-- User Info and Logout Buttons -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            U
                        </div>
                        <span id="user-name" class="text-sm text-gray-300 hidden sm:inline">Loading...</span>
                    </div>
                    
                    <button id="logout-btn" class="px-4 py-2 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Logout
                    </button>
                    
                    <button id="logout-all-btn" class="px-4 py-2 text-sm font-medium bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
                        Logout All
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- User Footer -->
    <footer class="bg-black py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent mb-4">Fitcoin</h3>
                    <p class="text-gray-400 text-sm">Your ultimate fitness companion. Transform your life today.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/user/dashboard" class="hover:text-white transition">Dashboard</a></li>
                        <li><a href="/user/workouts" class="hover:text-white transition">Workouts</a></li>
                        <li><a href="/user/progress" class="hover:text-white transition">Progress</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/user/profile" class="hover:text-white transition">Profile</a></li>
                        <li><a href="/user/achievements" class="hover:text-white transition">Achievements</a></li>
                        <li><a href="/user/settings" class="hover:text-white transition">Settings</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/contact" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center pt-8 border-t border-white/10">
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} Fitcoin. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Get token
        const token = localStorage.getItem('access_token');
        
        // Fetch user info
        if (token) {
            fetch('/api/me', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(user => {
                if (user.error) {
                    window.location.href = '/login';
                    return;
                }
                const roles = user.roles.map(r => r.slug);
                // If user is admin/super-admin, redirect to admin dashboard
                if (roles.includes('admin') || roles.includes('super-admin')) {
                    window.location.href = '/admin/dashboard';
                    return;
                }
                document.getElementById('user-name').textContent = `${user.first_name} ${user.last_name}`;
            })
            .catch(() => {
                window.location.href = '/login';
            });
        } else {
            window.location.href = '/login';
        }

        // Logout this device
        document.getElementById('logout-btn').addEventListener('click', function() {
            fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .catch(() => {})
            .finally(() => {
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                window.location.href = '/login';
            });
        });

        // Logout all devices
        document.getElementById('logout-all-btn').addEventListener('click', function() {
            if (!confirm('Are you sure you want to log out from all devices?')) return;
            
            fetch('/api/logout-all', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .catch(() => {})
            .finally(() => {
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                window.location.href = '/login';
            });
        });
    </script>

    @stack('scripts')
</body>
</html>