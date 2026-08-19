<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    protected $fillable = [
        'provider', 'code', 'pin', 'value', 'currency',
        'fitcoin_cost', 'sku', 'is_used', 'used_at',
        'expires_at', 'purchased_at'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'fitcoin_cost' => 'integer',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'purchased_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($giftCard) {
            if (empty($giftCard->uuid)) {
                $giftCard->uuid = (string) Str::uuid();
            }
        });
    }

    public function redemption()
    {
        return $this->hasOne(GiftCardRedemption::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function markAsUsed()
    {
        $this->update(['is_used' => true, 'used_at' => now()]);
    }

    public function getProviderLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->provider));
    }

    public function getIconAttribute()
    {
        return match($this->provider) {
            'amazon' => 'shopping_bag_outlined',
            'google_play' => 'android',
            'steam' => 'gamepad',
            'apple' => 'apple',
            default => 'card_giftcard',
        };
    }

    public function getColorAttribute()
    {
        return match($this->provider) {
            'amazon' => '#FF9900',
            'google_play' => '#34A853',
            'steam' => '#171A21',
            'apple' => '#555555',
            default => '#D946EF',
        };
    }
}