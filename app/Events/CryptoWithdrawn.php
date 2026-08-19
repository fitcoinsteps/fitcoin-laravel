<?php

namespace App\Events;

use App\Models\CryptoWithdrawal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CryptoWithdrawn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The withdrawal instance.
     *
     * @var CryptoWithdrawal
     */
    public $withdrawal;

    /**
     * Create a new event instance.
     */
    public function __construct(CryptoWithdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->withdrawal->user_id),
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
            'id' => $this->withdrawal->id,
            'user_id' => $this->withdrawal->user_id,
            'crypto_amount' => $this->withdrawal->crypto_amount,
            'currency' => $this->withdrawal->crypto_currency,
            'network' => $this->withdrawal->network,
            'wallet_address' => $this->withdrawal->wallet_address,
            'fitcoins_spent' => $this->withdrawal->fitcoins_spent,
            'status' => $this->withdrawal->status,
            'created_at' => $this->withdrawal->created_at->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'crypto.withdrawn';
    }
}