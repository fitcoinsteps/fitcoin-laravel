<?php

namespace App\Services;

use App\Models\CryptoWithdrawal;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CryptoService
{
    protected $conversionService;

    public function __construct(ConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    /**
     * Get crypto options from config.
     */
    public function getCryptoOptions(): array
    {
        return $this->conversionService->getCryptoOptions();
    }

    /**
     * Check if crypto withdrawal is enabled.
     */
    public function isCryptoEnabled(): bool
    {
        return $this->conversionService->isCryptoEnabled();
    }

    /**
     * Check if amount is within limits.
     */
    public function isValidAmount(float $amount, string $currency): bool
    {
        $currencies = Config::get('fitcoin.crypto.currencies', []);
        
        if (!isset($currencies[$currency])) {
            return false;
        }

        $minAmount = $currencies[$currency]['min_amount'] ?? 0;
        $maxAmount = $currencies[$currency]['max_amount'] ?? PHP_FLOAT_MAX;

        return $amount >= $minAmount && $amount <= $maxAmount;
    }

    /**
     * Check if user has enough FIT coins.
     */
    public function hasEnoughFitcoins(User $user, float $fitcoinsNeeded): bool
    {
        return $user->fitcoin_balance >= $fitcoinsNeeded;
    }

    /**
     * Request crypto withdrawal.
     */
    public function requestWithdrawal(User $user, $data)
    {
        // Check if crypto is enabled
        if (!$this->isCryptoEnabled()) {
            throw new \Exception('Crypto withdrawals are currently disabled.');
        }

        // Check if amount is valid
        if (!$this->isValidAmount($data['crypto_amount'], $data['currency'])) {
            throw new \Exception('Invalid amount for this cryptocurrency.');
        }

        $fitcoinsNeeded = $data['crypto_amount'] * $data['fitcoins_per_unit'];

        // Check if user has enough FIT coins
        if (!$this->hasEnoughFitcoins($user, $fitcoinsNeeded)) {
            throw new \Exception('Insufficient FIT coins. You need ' . 
                number_format($fitcoinsNeeded) . ' FIT coins.');
        }

        return DB::transaction(function () use ($user, $data, $fitcoinsNeeded) {
            // Calculate admin fee
            $feePercentage = $this->conversionService->getAdminFeePercentage();
            $adminFee = ($data['crypto_amount'] * $feePercentage) / 100;

            $withdrawal = CryptoWithdrawal::create([
                'user_id' => $user->id,
                'fitcoins_spent' => $fitcoinsNeeded,
                'crypto_amount' => $data['crypto_amount'],
                'crypto_currency' => $data['currency'],
                'wallet_address' => $data['wallet_address'],
                'network' => $data['network'],
                'status' => 'pending',
                'admin_fee' => $adminFee,
            ]);

            $user->decrement('fitcoin_balance', $fitcoinsNeeded);

            Log::info("Crypto withdrawal requested", [
                'user_id' => $user->id,
                'withdrawal_id' => $withdrawal->id,
                'crypto_amount' => $data['crypto_amount'],
                'currency' => $data['currency'],
                'admin_fee' => $adminFee,
                'fitcoins_spent' => $fitcoinsNeeded,
            ]);

            return $withdrawal;
        });
    }

    /**
     * Get user withdrawal history.
     */
    public function getUserWithdrawals(User $user, $limit = 20)
    {
        return CryptoWithdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Process withdrawal (admin).
     */
    public function processWithdrawal($withdrawalId, $txHash = null)
    {
        $withdrawal = CryptoWithdrawal::findOrFail($withdrawalId);
        $withdrawal->markAsProcessing();

        // In production, call crypto exchange API here
        // For now, generate a mock transaction hash
        $txHash = $txHash ?? '0x' . bin2hex(random_bytes(32));
        $withdrawal->markAsCompleted($txHash);

        Log::info("Crypto withdrawal processed", [
            'withdrawal_id' => $withdrawal->id,
            'transaction_hash' => $txHash,
        ]);

        return $withdrawal;
    }

    /**
     * Fail withdrawal and refund user (admin).
     */
    public function failWithdrawal($withdrawalId, $reason = null)
    {
        $withdrawal = CryptoWithdrawal::findOrFail($withdrawalId);
        $withdrawal->markAsFailed($reason);

        // Refund user
        $user = User::find($withdrawal->user_id);
        $user->increment('fitcoin_balance', $withdrawal->fitcoins_spent);

        Log::info("Crypto withdrawal failed and refunded", [
            'withdrawal_id' => $withdrawal->id,
            'user_id' => $user->id,
            'reason' => $reason,
            'refunded_fitcoins' => $withdrawal->fitcoins_spent,
        ]);

        return $withdrawal;
    }

    /**
     * Get all withdrawals (admin).
     */
    public function getAllWithdrawals($perPage = 20)
    {
        return CryptoWithdrawal::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get withdrawal statistics.
     */
    public function getWithdrawalStats()
    {
        return [
            'total_withdrawals' => CryptoWithdrawal::count(),
            'pending_withdrawals' => CryptoWithdrawal::where('status', 'pending')->count(),
            'completed_withdrawals' => CryptoWithdrawal::where('status', 'completed')->count(),
            'failed_withdrawals' => CryptoWithdrawal::where('status', 'failed')->count(),
            'total_crypto_amount' => CryptoWithdrawal::where('status', 'completed')->sum('crypto_amount'),
            'total_fitcoins_spent' => CryptoWithdrawal::where('status', 'completed')->sum('fitcoins_spent'),
            'total_admin_fees' => CryptoWithdrawal::where('status', 'completed')->sum('admin_fee'),
        ];
    }

    /**
     * Get withdrawals grouped by currency.
     */
    public function getWithdrawalsByCurrency()
    {
        return CryptoWithdrawal::where('status', 'completed')
            ->selectRaw('crypto_currency, SUM(crypto_amount) as total_amount, COUNT(*) as count')
            ->groupBy('crypto_currency')
            ->get();
    }
}