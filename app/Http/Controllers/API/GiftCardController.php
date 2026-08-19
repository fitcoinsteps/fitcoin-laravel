<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GiftCardService;
use App\Http\Requests\Api\RedeemGiftCardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class GiftCardController extends Controller
{
    /**
     * @var GiftCardService
     */
    protected $giftCardService;

    public function __construct(GiftCardService $giftCardService)
    {
        $this->giftCardService = $giftCardService;
        // ✅ Middleware handled in routes
    }

    /**
     * Get available gift card providers.
     */
    public function providers()
    {
        try {
            $providers = $this->giftCardService->getAvailableGiftCards();
            
            return response()->json([
                'success' => true,
                'data' => $providers,
                'config' => [
                    'min_fitcoins' => Config::get('fitcoin.redemption.min_fitcoins', 100),
                    'max_per_day' => Config::get('fitcoin.redemption.max_per_day', 5),
                    'auto_complete' => Config::get('fitcoin.redemption.auto_complete', true),
                    'fit_to_usd_rate' => Config::get('fitcoin.fit_to_usd_rate', 100),
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
     * Redeem a gift card.
     */
    public function redeem(RedeemGiftCardRequest $request)
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
            
            $redemption = $this->giftCardService->redeemGiftCard($user, $request->gift_card_id);

            return response()->json([
                'success' => true,
                'message' => 'Gift card redeemed successfully! 🎉',
                'data' => [
                    'redemption_id' => $redemption->id,
                    'gift_card_code' => $redemption->gift_card_code,
                    'value' => (float) $redemption->gift_card_value,
                    'provider' => $redemption->giftCard->provider_label ?? 'Unknown',
                    'fitcoins_spent' => (float) $redemption->fitcoins_spent,
                    'status' => $redemption->status,
                    'created_at' => $redemption->created_at?->toIso8601String(),
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
     * Get user redemption history.
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
            $history = $this->giftCardService->getUserRedemptions($user, $limit);

            return response()->json([
                'success' => true,
                'data' => $history->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'gift_card_code' => $item->gift_card_code,
                        'value' => (float) $item->gift_card_value,
                        'provider' => $item->giftCard->provider_label ?? 'Unknown',
                        'fitcoins_spent' => (float) $item->fitcoins_spent,
                        'status' => $item->status,
                        'created_at' => $item->created_at?->toIso8601String(),
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
     * Get redemption stats for current user.
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
            
            $canRedeemToday = $this->giftCardService->canRedeemToday($user);
            $hasMinFitcoins = $this->giftCardService->hasMinFitcoins($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'can_redeem_today' => $canRedeemToday,
                    'has_min_fitcoins' => $hasMinFitcoins,
                    'fitcoin_balance' => (float) $user->fitcoin_balance,
                    'min_fitcoins_required' => Config::get('fitcoin.redemption.min_fitcoins', 100),
                    'max_per_day' => Config::get('fitcoin.redemption.max_per_day', 5),
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
     * Get available gift card values with FIT costs.
     */
    public function values()
    {
        try {
            $values = $this->giftCardService->getGiftCardsWithPrices();
            
            return response()->json([
                'success' => true,
                'data' => $values,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}