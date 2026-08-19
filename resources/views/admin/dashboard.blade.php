@extends('layouts.admin')

@section('title', 'Dashboard - Fitcoin Admin')

@section('content')
<div class="px-4 py-6">
    <!-- Welcome Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white">
            Welcome back, <span class="bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent">{{ auth()->user()->first_name ?? 'Admin' }}</span> 👋
        </h1>
        <p class="text-gray-400 mt-1">Here's what's happening with your platform today.</p>
    </div>

    <!-- ==================== STATS CARDS ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Users -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5 hover:border-indigo-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Total Users</p>
                    <p class="text-white text-2xl font-bold mt-1">{{ number_format($stats['total_users'] ?? 0) }}</p>
                    <p class="text-green-400 text-xs mt-1">+{{ $stats['new_users_today'] ?? 0 }} today</p>
                </div>
                <div class="bg-indigo-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Gift Cards -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5 hover:border-pink-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Gift Cards</p>
                    <p class="text-white text-2xl font-bold mt-1">{{ number_format($stats['total_gift_cards'] ?? 0) }}</p>
                    <p class="text-green-400 text-xs mt-1">{{ $stats['available_gift_cards'] ?? 0 }} available</p>
                </div>
                <div class="bg-pink-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Redemptions -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5 hover:border-yellow-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Redemptions</p>
                    <p class="text-white text-2xl font-bold mt-1">{{ number_format($stats['total_redemptions'] ?? 0) }}</p>
                    <p class="text-yellow-400 text-xs mt-1">{{ $stats['pending_redemptions'] ?? 0 }} pending</p>
                </div>
                <div class="bg-yellow-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Crypto Withdrawals -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5 hover:border-purple-500/50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Withdrawals</p>
                    <p class="text-white text-2xl font-bold mt-1">{{ number_format($stats['total_withdrawals'] ?? 0) }}</p>
                    <p class="text-yellow-400 text-xs mt-1">{{ $stats['pending_withdrawals'] ?? 0 }} pending</p>
                </div>
                <div class="bg-purple-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== SECOND ROW STATS ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total FIT Coins -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">FIT Coins Issued</p>
                    <p class="text-white text-2xl font-bold mt-1">{{ number_format($stats['total_fitcoins'] ?? 0) }}</p>
                </div>
                <div class="bg-emerald-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Value Redeemed -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Gift Card Value Redeemed</p>
                    <p class="text-white text-2xl font-bold mt-1">${{ number_format($stats['total_redeemed_value'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-rose-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Crypto Withdrawn -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Crypto Withdrawn</p>
                    <p class="text-white text-2xl font-bold mt-1">{{ number_format($stats['total_crypto_amount'] ?? 0, 4) }}</p>
                </div>
                <div class="bg-blue-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">FIT to USD Rate</p>
                    <p class="text-white text-2xl font-bold mt-1">{{ $stats['fit_to_usd_rate'] ?? 100 }} FIT</p>
                    <p class="text-gray-400 text-xs mt-1">= $1 USD</p>
                </div>
                <div class="bg-amber-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-6 3v-3m-6 3h18M5 10h14M5 14h14"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== RECENT ACTIVITY ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Redemptions -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-white font-semibold text-lg">
                    <span class="text-pink-400">🎁</span> Recent Redemptions
                </h3>
                <a href="{{ route('admin.redemptions.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">
                    View All →
                </a>
            </div>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($recentRedemptions ?? [] as $redemption)
                    <div class="flex items-center justify-between py-2 border-b border-gray-700/50">
                        <div>
                            <p class="text-white text-sm font-medium">{{ $redemption->user->name ?? 'Unknown' }}</p>
                            <p class="text-gray-400 text-xs">
                                {{ $redemption->giftCard->provider_label ?? 'N/A' }} 
                                - ${{ number_format($redemption->gift_card_value, 2) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($redemption->status === 'completed') bg-green-500/20 text-green-400
                                @elseif($redemption->status === 'pending') bg-yellow-500/20 text-yellow-400
                                @elseif($redemption->status === 'processing') bg-blue-500/20 text-blue-400
                                @else bg-red-500/20 text-red-400 @endif">
                                {{ ucfirst($redemption->status) }}
                            </span>
                            <p class="text-gray-500 text-xs mt-1">{{ $redemption->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm text-center py-4">No recent redemptions</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Withdrawals -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-white font-semibold text-lg">
                    <span class="text-purple-400">💰</span> Recent Withdrawals
                </h3>
                <a href="{{ route('admin.withdrawals.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">
                    View All →
                </a>
            </div>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($recentWithdrawals ?? [] as $withdrawal)
                    <div class="flex items-center justify-between py-2 border-b border-gray-700/50">
                        <div>
                            <p class="text-white text-sm font-medium">{{ $withdrawal->user->name ?? 'Unknown' }}</p>
                            <p class="text-gray-400 text-xs">
                                {{ $withdrawal->crypto_currency }} 
                                - {{ number_format($withdrawal->crypto_amount, 6) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($withdrawal->status === 'completed') bg-green-500/20 text-green-400
                                @elseif($withdrawal->status === 'pending') bg-yellow-500/20 text-yellow-400
                                @elseif($withdrawal->status === 'processing') bg-blue-500/20 text-blue-400
                                @else bg-red-500/20 text-red-400 @endif">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                            <p class="text-gray-500 text-xs mt-1">{{ $withdrawal->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm text-center py-4">No recent withdrawals</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ==================== QUICK ACTIONS ==================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <a href="{{ route('admin.gift-cards.create') }}" class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 hover:border-indigo-500/50 transition text-center">
            <div class="text-2xl mb-2">➕</div>
            <h4 class="text-white font-medium">Add Gift Card</h4>
            <p class="text-gray-400 text-sm">Add new gift cards to inventory</p>
        </a>
        <a href="{{ route('admin.gift-cards.bulk-upload') }}" class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 hover:border-indigo-500/50 transition text-center">
            <div class="text-2xl mb-2">📤</div>
            <h4 class="text-white font-medium">Bulk Upload</h4>
            <p class="text-gray-400 text-sm">Upload multiple gift cards at once</p>
        </a>
        <a href="{{ route('admin.redemptions.index') }}" class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 hover:border-indigo-500/50 transition text-center">
            <div class="text-2xl mb-2">📊</div>
            <h4 class="text-white font-medium">View Reports</h4>
            <p class="text-gray-400 text-sm">See detailed redemption reports</p>
        </a>
    </div>
</div>
@endsection