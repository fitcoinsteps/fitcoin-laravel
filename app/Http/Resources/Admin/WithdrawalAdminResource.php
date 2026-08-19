<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalAdminResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'fitcoin_balance' => (float) $this->user?->fitcoin_balance,
            ],
            'fitcoins_spent' => (float) $this->fitcoins_spent,
            'crypto_amount' => (float) $this->crypto_amount,
            'crypto_currency' => $this->crypto_currency,
            'wallet_address' => $this->wallet_address,
            'network' => $this->network,
            'transaction_hash' => $this->transaction_hash,
            'admin_fee' => (float) $this->admin_fee,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),
            'status_color' => $this->getStatusColor(),
            'admin_notes' => $this->admin_notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'processed_at' => $this->processed_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
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