<?php

namespace App\Services;

use App\Models\GiftCard;
use App\Models\GiftCardRedemption;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiftCardService
{
    protected $conversionService;

    public function __construct(ConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    /**
     * Get available gift cards with values from config.
     */
    public function getAvailableGiftCards(): array
    {
        return $this->conversionService->getGiftCardProviders();
    }

    /**
     * Get gift cards with dynamic FIT costs.
     */
    public function getGiftCardsWithPrices(): array
    {
        $providers = Config::get('fitcoin.gift_cards.providers', []);
        $fitToUsdRate = $this->conversionService->getFITtoUSDRate();
        $result = [];

        foreach ($providers as $key => $provider) {
            if (!$provider['enabled']) continue;

            foreach ($provider['available_values'] as $value) {
                $result[] = [
                    'provider' => $key,
                    'provider_label' => $provider['name'],
                    'value' => $value,
                    'currency' => 'USD',
                    'fitcoin_cost' => $value * $fitToUsdRate,
                    'icon' => $provider['icon'],
                    'color' => $provider['color'],
                    'min_fitcoins' => $provider['min_fitcoins'],
                    'max_fitcoins' => $provider['max_fitcoins'],
                ];
            }
        }

        return $result;
    }

    /**
     * Check if user can redeem more today.
     */
    public function canRedeemToday(User $user): bool
    {
        $maxPerDay = $this->conversionService->getMaxRedemptionsPerDay();
        
        $todayRedemptions = GiftCardRedemption::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return $todayRedemptions < $maxPerDay;
    }

    /**
     * Check if user has minimum FIT coins.
     */
    public function hasMinFitcoins(User $user): bool
    {
        $minFitcoins = $this->conversionService->getMinFitcoinsForRedemption();
        return $user->fitcoin_balance >= $minFitcoins;
    }

    /**
     * Redeem gift card.
     */
    public function redeemGiftCard(User $user, $giftCardId)
    {
        // Check rate limits
        if (!$this->canRedeemToday($user)) {
            throw new \Exception('Daily redemption limit reached. Max ' . 
                $this->conversionService->getMaxRedemptionsPerDay() . ' per day.');
        }

        if (!$this->hasMinFitcoins($user)) {
            throw new \Exception('Minimum ' . 
                $this->conversionService->getMinFitcoinsForRedemption() . 
                ' FIT coins required to redeem.');
        }

        return DB::transaction(function () use ($user, $giftCardId) {
            $giftCard = GiftCard::available()
                ->where('id', $giftCardId)
                ->lockForUpdate()
                ->first();

            if (!$giftCard) {
                throw new \Exception('Gift card not available');
            }

            if ($user->fitcoin_balance < $giftCard->fitcoin_cost) {
                throw new \Exception('Insufficient FIT coins');
            }

            // Check if auto-complete is enabled
            $status = $this->conversionService->isAutoCompleteEnabled() 
                ? 'completed' 
                : 'pending';

            $redemption = GiftCardRedemption::create([
                'user_id' => $user->id,
                'gift_card_id' => $giftCard->id,
                'fitcoins_spent' => $giftCard->fitcoin_cost,
                'gift_card_value' => $giftCard->value,
                'gift_card_code' => $giftCard->code,
                'status' => $status,
            ]);

            $user->decrement('fitcoin_balance', $giftCard->fitcoin_cost);
            $giftCard->markAsUsed();

            Log::info("Gift card redeemed", [
                'user_id' => $user->id,
                'gift_card_id' => $giftCard->id,
                'redemption_id' => $redemption->id,
                'auto_completed' => $status === 'completed',
            ]);

            return $redemption;
        });
    }

    /**
     * Get user redemption history.
     */
    public function getUserRedemptions(User $user, $limit = 20)
    {
        return GiftCardRedemption::with('giftCard')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new gift card (admin).
     */
    public function createGiftCard($data)
    {
        return GiftCard::create(array_merge($data, ['purchased_at' => now()]));
    }

    /**
     * Get all redemptions (admin).
     */
    public function getAllRedemptions($perPage = 20)
    {
        return GiftCardRedemption::with(['user', 'giftCard'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Update redemption status (admin).
     */
    public function updateRedemptionStatus($redemptionId, $status, $notes = null)
    {
        $redemption = GiftCardRedemption::findOrFail($redemptionId);
        $redemption->status = $status;
        if ($notes) {
            $redemption->admin_notes = $notes;
        }
        if ($status === 'completed') {
            $redemption->completed_at = now();
        }
        $redemption->save();

        return $redemption;
    }

    /**
     * Get redemption statistics.
     */
    public function getRedemptionStats()
    {
        return [
            'total_redemptions' => GiftCardRedemption::where('status', 'completed')->count(),
            'pending_redemptions' => GiftCardRedemption::where('status', 'pending')->count(),
            'total_value' => GiftCardRedemption::where('status', 'completed')->sum('gift_card_value'),
            'total_fitcoins_spent' => GiftCardRedemption::where('status', 'completed')->sum('fitcoins_spent'),
            'today_redemptions' => GiftCardRedemption::whereDate('created_at', now()->toDateString())->count(),
        ];
    }
}