<?php

namespace App\Services;

use App\Models\User;
use App\Models\GiftCardRedemption;
use App\Models\CryptoWithdrawal;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Get admin wallet balance (total FIT coins)
     */
    public function getAdminWalletBalance()
    {
        // Get total FIT coins held by admin users
        return User::whereHas('roles', function ($query) {
            $query->where('slug', 'super-admin');
        })->sum('fitcoin_balance');
    }

    /**
     * Get total FIT coins in circulation
     */
    public function getTotalFITCirculation()
    {
        return User::sum('fitcoin_balance');
    }

    /**
     * Get total redeemed gift cards value
     */
    public function getTotalRedeemedGiftCards()
    {
        return DB::table('gift_card_redemptions')
            ->where('status', 'completed')
            ->sum('gift_card_value');
    }

    /**
     * Get total crypto withdrawn
     */
    public function getTotalCryptoWithdrawn()
    {
        return DB::table('crypto_withdrawals')
            ->where('status', 'completed')
            ->sum('crypto_amount');
    }

    /**
     * Get user wallet summary
     */
    public function getUserWalletSummary($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $totalRedeemed = DB::table('gift_card_redemptions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('gift_card_value');

        $totalCryptoWithdrawn = DB::table('crypto_withdrawals')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('crypto_amount');

        $totalFitSpent = DB::table('gift_card_redemptions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('fitcoins_spent') +
            DB::table('crypto_withdrawals')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('fitcoins_spent');

        return [
            'fitcoin_balance' => $user->fitcoin_balance,
            'total_redeemed_gift_cards' => $totalRedeemed,
            'total_crypto_withdrawn' => $totalCryptoWithdrawn,
            'total_fit_spent' => $totalFitSpent,
        ];
    }

    /**
     * Get daily wallet statistics
     */
    public function getDailyStats($days = 7)
    {
        $stats = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            
            $stats[] = [
                'date' => $date,
                'redemptions' => GiftCardRedemption::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('gift_card_value'),
                'withdrawals' => CryptoWithdrawal::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('crypto_amount'),
                'users' => User::whereDate('created_at', $date)->count(),
            ];
        }
        
        return $stats;
    }

    /**
     * Get top users by FIT coins
     */
    public function getTopUsers($limit = 10)
    {
        return User::orderBy('fitcoin_balance', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'fitcoin_balance']);
    }

    /**
     * Get wallet transactions for a user
     */
    public function getUserTransactions($userId, $limit = 20)
    {
        // Gift card redemptions
        $redemptions = GiftCardRedemption::where('user_id', $userId)
            ->where('status', 'completed')
            ->select(
                'id',
                DB::raw("'gift_card' as type"),
                'fitcoins_spent as amount',
                'gift_card_value as value',
                'created_at'
            );

        // Crypto withdrawals
        $withdrawals = CryptoWithdrawal::where('user_id', $userId)
            ->where('status', 'completed')
            ->select(
                'id',
                DB::raw("'crypto' as type"),
                'fitcoins_spent as amount',
                'crypto_amount as value',
                'created_at'
            );

        // Combine and order
        return $redemptions->union($withdrawals)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}