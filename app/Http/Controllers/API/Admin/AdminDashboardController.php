<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GiftCard;
use App\Models\GiftCardRedemption;
use App\Models\CryptoWithdrawal;
use App\Services\ConversionService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * @var ConversionService
     */
    protected $conversionService;

    /**
     * @var WalletService
     */
    protected $walletService;

    public function __construct(
        ConversionService $conversionService,
        WalletService $walletService
    ) {
        $this->conversionService = $conversionService;
        $this->walletService = $walletService;
        // ✅ No middleware here - handled in routes
    }

    public function index()
    {
        // Users
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();

        // Gift Cards
        $totalGiftCards = GiftCard::count();
        $availableGiftCards = GiftCard::available()->count();

        // Redemptions
        $totalRedemptions = GiftCardRedemption::where('status', 'completed')->count();
        $pendingRedemptions = GiftCardRedemption::where('status', 'pending')->count();
        $totalRedeemedValue = GiftCardRedemption::where('status', 'completed')->sum('gift_card_value');

        // Crypto Withdrawals
        $totalWithdrawals = CryptoWithdrawal::count();
        $pendingWithdrawals = CryptoWithdrawal::where('status', 'pending')->count();
        $totalCryptoAmount = CryptoWithdrawal::where('status', 'completed')->sum('crypto_amount');

        // FIT Coins
        $totalFitcoins = User::sum('fitcoin_balance');
        $fitToUsdRate = $this->conversionService->getFITtoUSDRate();

        // Recent Activity
        $recentRedemptions = GiftCardRedemption::with(['user', 'giftCard'])
            ->latest()
            ->limit(10)
            ->get();

        $recentWithdrawals = CryptoWithdrawal::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'total_users' => $totalUsers,
            'new_users_today' => $newUsersToday,
            'total_gift_cards' => $totalGiftCards,
            'available_gift_cards' => $availableGiftCards,
            'total_redemptions' => $totalRedemptions,
            'pending_redemptions' => $pendingRedemptions,
            'total_redeemed_value' => $totalRedeemedValue,
            'total_withdrawals' => $totalWithdrawals,
            'pending_withdrawals' => $pendingWithdrawals,
            'total_crypto_amount' => $totalCryptoAmount,
            'total_fitcoins' => $totalFitcoins,
            'fit_to_usd_rate' => $fitToUsdRate,
        ];

        return view('admin.dashboard', compact('stats', 'recentRedemptions', 'recentWithdrawals'));
    }

    /**
     * Get dashboard statistics as JSON (for API).
     */
    public function stats()
    {
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
            'total_gift_cards' => GiftCard::count(),
            'available_gift_cards' => GiftCard::available()->count(),
            'total_redemptions' => GiftCardRedemption::where('status', 'completed')->count(),
            'pending_redemptions' => GiftCardRedemption::where('status', 'pending')->count(),
            'total_redeemed_value' => GiftCardRedemption::where('status', 'completed')->sum('gift_card_value'),
            'total_withdrawals' => CryptoWithdrawal::count(),
            'pending_withdrawals' => CryptoWithdrawal::where('status', 'pending')->count(),
            'total_crypto_amount' => CryptoWithdrawal::where('status', 'completed')->sum('crypto_amount'),
            'total_fitcoins' => User::sum('fitcoin_balance'),
            'fit_to_usd_rate' => $this->conversionService->getFITtoUSDRate(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get recent activity as JSON (for API).
     */
    public function activity(Request $request)
    {
        $limit = $request->get('limit', 10);

        $redemptions = GiftCardRedemption::with(['user', 'giftCard'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'redemption',
                    'user' => $item->user?->name ?? 'Unknown',
                    'provider' => $item->giftCard?->provider_label ?? 'N/A',
                    'value' => (float) $item->gift_card_value,
                    'status' => $item->status,
                    'created_at' => $item->created_at?->toIso8601String(),
                ];
            });

        $withdrawals = CryptoWithdrawal::with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'withdrawal',
                    'user' => $item->user?->name ?? 'Unknown',
                    'currency' => $item->crypto_currency,
                    'amount' => (float) $item->crypto_amount,
                    'status' => $item->status,
                    'created_at' => $item->created_at?->toIso8601String(),
                ];
            });

        $activity = $redemptions->concat($withdrawals)
            ->sortByDesc('created_at')
            ->values()
            ->take($limit);

        return response()->json([
            'success' => true,
            'data' => $activity,
        ]);
    }
}