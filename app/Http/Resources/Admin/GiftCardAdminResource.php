<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardAdminResource extends JsonResource
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
            'provider' => $this->provider,
            'provider_label' => $this->provider_label,
            'code' => $this->code,
            'pin' => $this->pin,
            'value' => (float) $this->value,
            'currency' => $this->currency,
            'fitcoin_cost' => (int) $this->fitcoin_cost,
            'sku' => $this->sku,
            'is_used' => (bool) $this->is_used,
            'is_expired' => $this->isExpired(),
            'used_at' => $this->used_at?->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'purchased_at' => $this->purchased_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'icon' => $this->icon,
            'color' => $this->color,
            'redemption' => $this->when($this->redemption, function () {
                return [
                    'id' => $this->redemption->id,
                    'user_id' => $this->redemption->user_id,
                    'user_name' => $this->redemption->user?->name,
                    'user_email' => $this->redemption->user?->email,
                    'status' => $this->redemption->status,
                    'fitcoins_spent' => (float) $this->redemption->fitcoins_spent,
                    'created_at' => $this->redemption->created_at?->toIso8601String(),
                ];
            }),
        ];
    }
}