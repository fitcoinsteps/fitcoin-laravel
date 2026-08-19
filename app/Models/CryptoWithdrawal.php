<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CryptoWithdrawal extends Model
{
    protected $fillable = [
        'user_id', 'fitcoins_spent', 'crypto_amount',
        'crypto_currency', 'wallet_address', 'network',
        'transaction_hash', 'admin_fee', 'status',
        'admin_notes', 'processed_at', 'completed_at'
    ];

    protected $casts = [
        'fitcoins_spent' => 'decimal:2',
        'crypto_amount' => 'decimal:8',
        'admin_fee' => 'decimal:2',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($withdrawal) {
            if (empty($withdrawal->uuid)) {
                $withdrawal->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsProcessing()
    {
        $this->update(['status' => 'processing', 'processed_at' => now()]);
    }

    public function markAsCompleted($txHash = null)
    {
        $this->update([
            'status' => 'completed',
            'transaction_hash' => $txHash ?? $this->transaction_hash,
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed($reason = null)
    {
        $this->update(['status' => 'failed', 'admin_notes' => $reason]);
    }
}