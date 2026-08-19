<?php

namespace App\Services;

use App\Models\ConversionRate;
use Illuminate\Support\Facades\Config;

class ConversionService
{
    /**
     * Get FIT to USD rate from config.
     */
    public function getFITtoUSDRate(): int
    {
        return Config::get('fitcoin.fit_to_usd_rate', 100);
    }

    /**
     * Convert FIT to USD.
     */
    public function fitToUSD($fitcoins): float
    {
        $rate = $this->getFITtoUSDRate();
        return $fitcoins / $rate;
    }

    /**
     * Convert USD to FIT.
     */
    public function usdToFIT($usd): float
    {
        $rate = $this->getFITtoUSDRate();
        return $usd * $rate;
    }

    /**
     * Get FIT to crypto rate from config.
     */
    public function getFITtoCryptoRate(string $currency): ?int
    {
        $currencies = Config::get('fitcoin.crypto.currencies', []);
        
        if (isset($currencies[$currency])) {
            return $currencies[$currency]['fitcoins_per_unit'] ?? null;
        }
        
        return null;
    }

    /**
     * Convert FIT to crypto.
     */
    public function fitToCrypto($fitcoins, string $currency): ?float
    {
        $rate = $this->getFITtoCryptoRate($currency);
        if (!$rate) return null;
        return $fitcoins / $rate;
    }

    /**
     * Convert crypto to FIT.
     */
    public function cryptoToFIT($amount, string $currency): ?float
    {
        $rate = $this->getFITtoCryptoRate($currency);
        if (!$rate) return null;
        return $amount * $rate;
    }

    /**
     * Get all crypto options from config.
     */
    public function getCryptoOptions(): array
    {
        $currencies = Config::get('fitcoin.crypto.currencies', []);
        $result = [];

        foreach ($currencies as $key => $currency) {
            if (!$currency['enabled']) continue;
            
            $result[$key] = [
                'name' => $currency['name'],
                'symbol' => $currency['symbol'],
                'icon' => $currency['icon'],
                'color' => $currency['color'],
                'networks' => $currency['networks'],
                'fitcoins_per_unit' => $currency['fitcoins_per_unit'],
                'min_amount' => $currency['min_amount'],
                'max_amount' => $currency['max_amount'],
                'min_fitcoins' => $currency['min_fitcoins'],
                'max_fitcoins' => $currency['max_fitcoins'],
            ];
        }

        return $result;
    }

    /**
     * Get gift card providers from config.
     */
    public function getGiftCardProviders(): array
    {
        $providers = Config::get('fitcoin.gift_cards.providers', []);
        $fitToUsdRate = $this->getFITtoUSDRate();
        $result = [];

        foreach ($providers as $key => $provider) {
            if (!$provider['enabled']) continue;
            
            $result[$key] = [
                'name' => $provider['name'],
                'icon' => $provider['icon'],
                'color' => $provider['color'],
                'values' => array_map(function ($value) use ($fitToUsdRate) {
                    return [
                        'value' => $value,
                        'fitcoins' => $value * $fitToUsdRate,
                        'currency' => 'USD',
                    ];
                }, $provider['available_values']),
                'min_fitcoins' => $provider['min_fitcoins'],
                'max_fitcoins' => $provider['max_fitcoins'],
            ];
        }

        return $result;
    }

    /**
     * Get admin fee percentage.
     */
    public function getAdminFeePercentage(): int
    {
        return Config::get('fitcoin.crypto.admin_fee_percentage', 2);
    }

    /**
     * Check if crypto withdrawal is enabled.
     */
    public function isCryptoEnabled(): bool
    {
        return Config::get('fitcoin.crypto.enabled', true);
    }

    /**
     * Get max redemptions per day.
     */
    public function getMaxRedemptionsPerDay(): int
    {
        return Config::get('fitcoin.redemption.max_per_day', 5);
    }

    /**
     * Get minimum FIT coins for redemption.
     */
    public function getMinFitcoinsForRedemption(): int
    {
        return Config::get('fitcoin.redemption.min_fitcoins', 100);
    }

    /**
     * Check if auto-complete is enabled.
     */
    public function isAutoCompleteEnabled(): bool
    {
        return Config::get('fitcoin.redemption.auto_complete', true);
    }

    /**
     * Get min withdrawal amount.
     */
    public function getMinWithdrawalAmount(): float
    {
        return Config::get('fitcoin.crypto.min_withdrawal_amount', 5);
    }

    /**
     * Get max withdrawal amount.
     */
    public function getMaxWithdrawalAmount(): float
    {
        return Config::get('fitcoin.crypto.max_withdrawal_amount', 1000);
    }

    /**
     * Get rate limits for redemptions.
     */
    public function getRedemptionRateLimit(): array
    {
        return [
            'max_attempts' => Config::get('fitcoin.rate_limits.redemptions.max_attempts', 10),
            'decay_minutes' => Config::get('fitcoin.rate_limits.redemptions.decay_minutes', 60),
        ];
    }

    /**
     * Get rate limits for withdrawals.
     */
    public function getWithdrawalRateLimit(): array
    {
        return [
            'max_attempts' => Config::get('fitcoin.rate_limits.withdrawals.max_attempts', 5),
            'decay_minutes' => Config::get('fitcoin.rate_limits.withdrawals.decay_minutes', 60),
        ];
    }
}