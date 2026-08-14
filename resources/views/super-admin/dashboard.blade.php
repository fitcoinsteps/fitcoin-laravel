<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin Dashboard - Fitcoin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        neonpink: '#ec4899',
                        neonpurple: '#8b5cf6',
                        neongold: '#d4af37',
                        darkbg: '#0a0512',
                        cardbg: '#1c0929'
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
            min-height: 100vh;
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
        .gradient-text {
            background: linear-gradient(135deg, #ec4899, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar {
            background: rgba(10, 5, 18, 0.95);
            border-right: 1px solid rgba(236, 72, 153, 0.1);
        }
        .nav-link {
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.5);
        }
        .nav-link:hover, .nav-link.active {
            color: #ec4899;
            background: rgba(236, 72, 153, 0.1);
        }
    </style>
</head>
<body>
    <div class="flex h-screen">
        <div class="sidebar w-64 p-6 flex flex-col">
            <div class="mb-8">
                <h1 class="text-2xl font-extrabold">
                    <span class="gradient-text"><i class="fas fa-bolt text-neonpink mr-2"></i>Fitcoin</span>
                    <span class="text-xs text-gray-500 block mt-1">Super Admin Panel</span>
                </h1>
            </div>

            <nav class="flex-1 space-y-1">
                <a href="#" class="nav-link active flex items-center gap-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-user-tie"></i> Admins
                </a>
                <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-shield-alt"></i> Roles & Permissions
                </a>
                <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
                <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>

            <div class="border-t border-gray-800 pt-4">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg w-full text-left">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold">Welcome back, {{ $user->first_name }}!</h2>
                    <p class="text-gray-400 mt-1">Here's what's happening with your platform today.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-neonpink to-neonpurple border-2 border-neonpink/50">
                        <img src="https://i.pravatar.cc/150?img={{ $user->id }}" class="w-full h-full rounded-full object-cover">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="glass-card p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Total Users</p>
                            <p class="text-2xl font-bold">1,234</p>
                        </div>
                        <div class="w-12 h-12 bg-neonpink/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-neonpink text-xl"></i>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm mt-2"><i class="fas fa-arrow-up"></i> 12% increase</p>
                </div>

                <div class="glass-card p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Active Sessions</p>
                            <p class="text-2xl font-bold">567</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-circle text-green-500 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm mt-2"><i class="fas fa-arrow-up"></i> 8% increase</p>
                </div>

                <div class="glass-card p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Total Admins</p>
                            <p class="text-2xl font-bold">3</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-purple-500 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">2 active, 1 inactive</p>
                </div>

                <div class="glass-card p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Revenue</p>
                            <p class="text-2xl font-bold">$12,345</p>
                        </div>
                        <div class="w-12 h-12 bg-gold-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-coins text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-green-400 text-sm mt-2"><i class="fas fa-arrow-up"></i> 23% increase</p>
                </div>
            </div>

            <div class="glass-card p-6 rounded-xl">
                <h3 class="text-xl font-bold mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-neonpink/20 flex items-center justify-center">
                                <i class="fas fa-user text-neonpink"></i>
                            </div>
                            <div>
                                <p class="font-medium">New user registered</p>
                                <p class="text-sm text-gray-400">John Doe created an account</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-400">2 min ago</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div>
                                <p class="font-medium">Admin login</p>
                                <p class="text-sm text-gray-400">Admin accessed the dashboard</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-400">15 min ago</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                            </div>
                            <div>
                                <p class="font-medium">Permission updated</p>
                                <p class="text-sm text-gray-400">Super Admin modified admin permissions</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-400">1 hour ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>