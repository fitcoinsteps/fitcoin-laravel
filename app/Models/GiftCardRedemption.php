<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCardRedemption extends Model
{
    protected $fillable = [
        'user_id', 'gift_card_id', 'fitcoins_spent',
        'gift_card_value', 'gift_card_code', 'status',
        'admin_notes', 'completed_at'
    ];

    protected $casts = [
        'fitcoins_spent' => 'decimal:2',
        'gift_card_value' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($redemption) {
            if (empty($redemption->uuid)) {
                $redemption->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed', 'completed_at' => now()]);
    }

    public function markAsFailed($reason = null)
    {
        $this->update(['status' => 'failed', 'admin_notes' => $reason]);
    }
}