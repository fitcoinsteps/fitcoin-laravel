<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CryptoWithdrawalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'crypto_amount' => (float) $this->crypto_amount,
            'crypto_currency' => $this->crypto_currency,
            'fitcoins_spent' => (float) $this->fitcoins_spent,
            'wallet_address' => $this->wallet_address,
            'network' => $this->network,
            'transaction_hash' => $this->transaction_hash,
            'admin_fee' => (float) $this->admin_fee,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),
            'status_color' => $this->getStatusColor(),
            'created_at' => $this->created_at?->toIso8601String(),
            'processed_at' => $this->processed_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
        ];
    }

    /**
     * Get status color.
     */
    protected function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => '#F59E0B', // Yellow
            'processing' => '#3B82F6', // Blue
            'completed' => '#10B981', // Green
            'failed' => '#EF4444', // Red
            default => '#6B7280', // Gray
        };
    }
}