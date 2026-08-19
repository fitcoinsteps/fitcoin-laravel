<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardResource extends JsonResource
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
            'value' => (float) $this->value,
            'currency' => $this->currency,
            'fitcoin_cost' => (int) $this->fitcoin_cost,
            'code' => $this->when($this->is_used, null, $this->code),
            'pin' => $this->when($this->is_used, null, $this->pin),
            'is_used' => (bool) $this->is_used,
            'is_expired' => $this->isExpired(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'icon' => $this->icon,
            'color' => $this->color,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}