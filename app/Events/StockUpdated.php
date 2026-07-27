<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Fired by ProductObserver any time stock_quantity changes, regardless of
 * *why* it changed (online sale, POS sale, manual admin edit, restock).
 * Every listening surface (Storefront, POS terminal, Admin table) subscribes
 * to the same public "inventory" channel and reacts identically — none of
 * them need to know or care which channel caused the change.
 *
 * ShouldBroadcastNow (not ShouldBroadcast) because inventory sync is the
 * whole point of the app: it must fire synchronously, not sit in a queue
 * behind other jobs.
 */
class StockUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(public Product $product)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('inventory'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'product_id' => $this->product->id,
            'sku' => $this->product->sku,
            'stock_quantity' => $this->product->stock_quantity,
            'stock_status' => $this->product->stock_status,
        ];
    }
}
