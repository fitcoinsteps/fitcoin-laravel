<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CryptoWithdrawal;
use App\Models\User;
use App\Services\CryptoService;
use App\Services\ConversionService;
use App\Http\Requests\Api\CryptoWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CryptoWithdrawalController extends Controller
{
    /**
     * @var CryptoService
     */
    protected $cryptoService;

    /**
     * @var ConversionService
     */
    protected $conversionService;

    public function __construct(
        CryptoService $cryptoService,
        ConversionService $conversionService
    ) {
        $this->cryptoService = $cryptoService;
        $this->conversionService = $conversionService;
        // ✅ Middleware handled in routes
    }

    /**
     * Get crypto withdrawal options.
     */
    public function options()
    {
        try {
            $options = $this->cryptoService->getCryptoOptions();
            
            return response()->json([
                'success' => true,
                'data' => $options,
                'config' => [
                    'enabled' => $this->cryptoService->isCryptoEnabled(),
                    'admin_fee_percentage' => $this->conversionService->getAdminFeePercentage(),
                    'min_withdrawal' => $this->conversionService->getMinWithdrawalAmount(),
                    'max_withdrawal' => $this->conversionService->getMaxWithdrawalAmount(),
                    'fit_to_usd_rate' => $this->conversionService->getFITtoUSDRate(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request crypto withdrawal.
     */
    public function request(CryptoWithdrawalRequest $request)
    {
        try {
            /** @var User $user */
            $user = auth('api')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }
            
            $withdrawal = $this->cryptoService->requestWithdrawal($user, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully!',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'crypto_amount' => (float) $withdrawal->crypto_amount,
                    'currency' => $withdrawal->crypto_currency,
                    'network' => $withdrawal->network,
                    'wallet_address' => $withdrawal->wallet_address,
                    'status' => $withdrawal->status,
                    'admin_fee' => (float) $withdrawal->admin_fee,
                    'fitcoins_spent' => (float) $withdrawal->fitcoins_spent,
                    'created_at' => $withdrawal->created_at?->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get user withdrawal history.
     */
    public function history(Request $request)
    {
        try {
            /** @var User $user */
            $user = auth('api')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }
            
            $limit = $request->get('limit', 20);
            $history = $this->cryptoService->getUserWithdrawals($user, $limit);

            return response()->json([
                'success' => true,
                'data' => $history->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'crypto_amount' => (float) $item->crypto_amount,
                        'currency' => $item->crypto_currency,
                        'network' => $item->network,
                        'wallet_address' => $item->wallet_address,
                        'status' => $item->status,
                        'admin_fee' => (float) $item->admin_fee,
                        'fitcoins_spent' => (float) $item->fitcoins_spent,
                        'created_at' => $item->created_at?->toIso8601String(),
                        'completed_at' => $item->completed_at?->toIso8601String(),
                    ];
                }),
                'meta' => [
                    'total' => $history->count(),
                    'limit' => $limit,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get withdrawal stats for current user.
     */
    public function stats()
    {
        try {
            /** @var User $user */
            $user = auth('api')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }
            
            $totalWithdrawn = CryptoWithdrawal::where('user_id', $user->id)
                ->where('status', 'completed')
                ->sum('crypto_amount');

            $pendingWithdrawals = CryptoWithdrawal::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count();

            $minFitcoinsRequired = $this->conversionService->getMinWithdrawalAmount() * 
                $this->conversionService->getFITtoUSDRate();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_withdrawn' => (float) $totalWithdrawn,
                    'pending_withdrawals' => $pendingWithdrawals,
                    'fitcoin_balance' => (float) $user->fitcoin_balance,
                    'can_withdraw' => $this->cryptoService->hasEnoughFitcoins(
                        $user, 
                        $minFitcoinsRequired
                    ),
                    'min_fitcoins_required' => $minFitcoinsRequired,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available cryptocurrencies for withdrawal.
     */
    public function currencies()
    {
        try {
            $currencies = Config::get('fitcoin.crypto.currencies', []);
            $available = [];

            foreach ($currencies as $key => $currency) {
                if ($currency['enabled'] ?? false) {
                    $available[] = [
                        'code' => $key,
                        'name' => $currency['name'],
                        'symbol' => $currency['symbol'],
                        'icon' => $currency['icon'],
                        'color' => $currency['color'],
                        'networks' => $currency['networks'] ?? ['ERC-20'],
                        'min_amount' => $currency['min_amount'] ?? 0,
                        'max_amount' => $currency['max_amount'] ?? 0,
                        'fitcoins_per_unit' => $currency['fitcoins_per_unit'] ?? 0,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $available,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}