<?php

namespace App\Events;

use App\Models\GiftCardRedemption;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GiftCardRedeemed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The redemption instance.
     *
     * @var GiftCardRedemption
     */
    public $redemption;

    /**
     * Create a new event instance.
     */
    public function __construct(GiftCardRedemption $redemption)
    {
        $this->redemption = $redemption;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->redemption->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->redemption->id,
            'user_id' => $this->redemption->user_id,
            'gift_card_code' => $this->redemption->gift_card_code,
            'value' => $this->redemption->gift_card_value,
            'provider' => $this->redemption->giftCard->provider_label ?? 'Unknown',
            'fitcoins_spent' => $this->redemption->fitcoins_spent,
            'status' => $this->redemption->status,
            'created_at' => $this->redemption->created_at->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'gift-card.redeemed';
    }
}