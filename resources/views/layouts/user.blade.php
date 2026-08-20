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
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/50 backdrop-blur-lg border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="/user/dashboard" class="text-2xl font-bold bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                    Fitcoin
                </a>
                <nav class="hidden md:flex items-center gap-7">
                    <a href="/user/dashboard" class="text-sm font-medium text-gray-300 hover:text-white transition">Dashboard</a>
                    <a href="#" class="text-sm font-medium text-gray-300 hover:text-white transition">Workouts</a>
                    <a href="#" class="text-sm font-medium text-gray-300 hover:text-white transition">Progress</a>
                    <a href="#" class="text-sm font-medium text-gray-300 hover:text-white transition">Achievements</a>
                    <a href="#" class="text-sm font-medium text-gray-300 hover:text-white transition">Profile</a>
                </nav>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">U</div>
                    <span id="user-name" class="text-sm text-gray-300 hidden sm:inline">Loading...</span>
                    <button id="logout-btn" class="px-4 py-2 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Logout</button>
                    <button id="logout-all-btn" class="px-4 py-2 text-sm font-medium bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">Logout All</button>
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <script>
        fetch('/me', { headers: { 'Accept': 'application/json' } })
        .then(response => response.json())
        .then(user => {
            if (user.error) {
                window.location.href = '/login';
                return;
            }
            if (user.role === 'super-admin') {
                window.location.href = '/admin/dashboard';
                return;
            }
            document.getElementById('user-name').textContent = `${user.first_name} ${user.last_name}`;
        })
        .catch(() => {
            window.location.href = '/login';
        });

        document.getElementById('logout-btn').addEventListener('click', function() {
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .catch(() => {})
            .finally(() => window.location.href = '/login');
        });

        document.getElementById('logout-all-btn').addEventListener('click', function() {
            if (!confirm('Logout from all devices?')) return;
            fetch('/logout-all', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .catch(() => {})
            .finally(() => window.location.href = '/login');
        });
    </script>
    @stack('scripts')
</body>
</html>