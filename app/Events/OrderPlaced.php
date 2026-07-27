<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched from the checkout/POS action handlers (not a model observer,
 * since "an order was placed" is a business event, not a row mutation).
 * The cashier's Alpine.js layer listens for source=online events specifically
 * to trigger the popup + sound while they're mid-sale.
 */
class OrderPlaced implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('orders')];
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'order_number' => $this->order->order_number,
            'source' => $this->order->source,
            'total_price' => number_format($this->order->total_price_cents / 100, 2),
        ];
    }
}
