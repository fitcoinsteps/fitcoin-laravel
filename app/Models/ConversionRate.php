<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionRate extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'currency',
        'provider',
        'fitcoins_per_unit',
        'min_fitcoins',
        'max_fitcoins',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'fitcoins_per_unit' => 'decimal:2',
        'min_fitcoins' => 'decimal:2',
        'max_fitcoins' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
    ];

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'gift_card' => 'Gift Card',
            'crypto' => 'Cryptocurrency',
            default => ucfirst($this->type),
        };
    }

    /**
     * Scope a query to only include active rates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>', now());
            });
    }

    /**
     * Scope a query to only include gift card rates.
     */
    public function scopeGiftCards($query)
    {
        return $query->where('type', 'gift_card');
    }

    /**
     * Scope a query to only include crypto rates.
     */
    public function scopeCrypto($query)
    {
        return $query->where('type', 'crypto');
    }

    /**
     * Get rate for a specific type and currency.
     */
    public static function getRate(string $type, string $currency, ?string $provider = null): ?float
    {
        $rate = self::active()
            ->where('type', $type)
            ->where('currency', strtoupper($currency))
            ->when($provider, function ($query) use ($provider) {
                return $query->where('provider', $provider);
            })
            ->first();

        return $rate ? (float) $rate->fitcoins_per_unit : null;
    }

    /**
     * Get the FIT coins needed for a specific amount.
     */
    public static function getFitcoinsForAmount(
        string $type,
        float $amount,
        string $currency,
        ?string $provider = null
    ): ?float {
        $rate = self::getRate($type, $currency, $provider);
        
        if (!$rate) {
            return null;
        }

        return $amount * $rate;
    }

    /**
     * Get the value of FIT coins in the specified currency.
     */
    public static function getValueForFitcoins(
        float $fitcoins,
        string $type,
        string $currency,
        ?string $provider = null
    ): ?float {
        $rate = self::getRate($type, $currency, $provider);
        
        if (!$rate) {
            return null;
        }

        return $fitcoins / $rate;
    }

    /**
     * Check if a FIT coin amount is within limits.
     */
    public function isValidAmount(float $fitcoins): bool
    {
        if ($this->min_fitcoins && $fitcoins < $this->min_fitcoins) {
            return false;
        }

        if ($this->max_fitcoins && $fitcoins > $this->max_fitcoins) {
            return false;
        }

        return true;
    }

    /**
     * Get all gift card providers with their rates.
     */
    public static function getGiftCardProviders(): array
    {
        $rates = self::active()
            ->giftCards()
            ->get();

        $result = [];
        foreach ($rates as $rate) {
            $provider = $rate->provider ?? 'default';
            if (!isset($result[$provider])) {
                $result[$provider] = [];
            }
            $result[$provider][] = [
                'currency' => $rate->currency,
                'fitcoins_per_unit' => $rate->fitcoins_per_unit,
                'min_fitcoins' => $rate->min_fitcoins,
                'max_fitcoins' => $rate->max_fitcoins,
            ];
        }

        return $result;
    }

    /**
     * Get all crypto rates.
     */
    public static function getCryptoRates(): array
    {
        return self::active()
            ->crypto()
            ->get()
            ->map(function ($rate) {
                return [
                    'currency' => $rate->currency,
                    'fitcoins_per_unit' => $rate->fitcoins_per_unit,
                    'min_fitcoins' => $rate->min_fitcoins,
                    'max_fitcoins' => $rate->max_fitcoins,
                ];
            })
            ->toArray();
    }

    /**
     * Create default rates if none exist.
     */
    public static function createDefaultRates(): void
    {
        $defaults = [
            // Gift card rates
            [
                'type' => 'gift_card',
                'currency' => 'USD',
                'provider' => null,
                'fitcoins_per_unit' => 100,
                'min_fitcoins' => 10,
                'max_fitcoins' => 10000,
                'is_active' => true,
                'effective_from' => now(),
                'effective_to' => null,
            ],
            // Crypto rates
            [
                'type' => 'crypto',
                'currency' => 'USDT',
                'provider' => null,
                'fitcoins_per_unit' => 1000,
                'min_fitcoins' => 100,
                'max_fitcoins' => 100000,
                'is_active' => true,
                'effective_from' => now(),
                'effective_to' => null,
            ],
            [
                'type' => 'crypto',
                'currency' => 'USDC',
                'provider' => null,
                'fitcoins_per_unit' => 1000,
                'min_fitcoins' => 100,
                'max_fitcoins' => 100000,
                'is_active' => true,
                'effective_from' => now(),
                'effective_to' => null,
            ],
            [
                'type' => 'crypto',
                'currency' => 'BTC',
                'provider' => null,
                'fitcoins_per_unit' => 50000,
                'min_fitcoins' => 1000,
                'max_fitcoins' => 1000000,
                'is_active' => true,
                'effective_from' => now(),
                'effective_to' => null,
            ],
        ];

        foreach ($defaults as $default) {
            if (!self::where('type', $default['type'])
                ->where('currency', $default['currency'])
                ->where('provider', $default['provider'])
                ->exists()) {
                self::create($default);
            }
        }
    }
}