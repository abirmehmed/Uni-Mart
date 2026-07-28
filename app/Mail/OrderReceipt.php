<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderReceipt extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        // $order is loaded with its products for the receipt line items —
        // load eagerly here so the queued job doesn't trigger N+1 queries
        // when it's picked up later.
        $this->order->loadMissing('products');
    }

    public function build(): self
    {
        return $this
            ->subject("Your UniMart order {$this->order->order_number}")
            ->view('emails.order-receipt');
    }
}
