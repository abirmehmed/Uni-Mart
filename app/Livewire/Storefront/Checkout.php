<?php

namespace App\Livewire\Storefront;

use App\Events\OrderPlaced;
use App\Exceptions\InsufficientStockException;
use App\Mail\OrderReceipt;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Checkout extends Component
{
    public string $customer_name = '';
    public string $customer_email = '';
    public string $customer_address = '';

    public ?string $placedOrderNumber = null;

    protected function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_address' => 'required|string|max:500',
        ];
    }

    #[Computed]
    public function cartItems()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->map(fn (Product $product) => (object) [
                'product' => $product,
                'quantity' => $cart[$product->id],
                'subtotal_cents' => $product->price_cents * $cart[$product->id],
            ]);
    }

    #[Computed]
    public function cartTotalCents(): int
    {
        return $this->cartItems->sum('subtotal_cents');
    }

    public function placeOrder(): void
    {
        $this->validate();

        $cart = session('cart', []);

        if (empty($cart)) {
            $this->addError('cart', 'Your cart is empty.');

            return;
        }

        try {
            $order = DB::transaction(function () use ($cart) {
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber('online'),
                    'customer_name' => $this->customer_name,
                    'customer_email' => $this->customer_email,
                    'customer_address' => $this->customer_address,
                    'total_price_cents' => 0, // filled in below once line items are known
                    'source' => 'online',
                    'status' => 'paid', // no real payment gateway wired up yet — see README
                ]);

                $totalCents = 0;

                foreach ($cart as $productId => $quantity) {
                    // Product::sell() is the same choke point the Admin
                    // Dashboard and (later) POS use — row-locked, so a
                    // simultaneous POS sale on the same item can't oversell
                    // it out from under this checkout.
                    $product = Product::sell($productId, $quantity);

                    $order->products()->attach($productId, [
                        'quantity' => $quantity,
                        'price_at_time_cents' => $product->price_cents,
                    ]);

                    $totalCents += $product->price_cents * $quantity;
                }

                $order->update(['total_price_cents' => $totalCents]);

                return $order;
            });
        } catch (InsufficientStockException $e) {
            // Someone else bought the last one between "add to cart" and
            // "place order" — the transaction above rolled back entirely,
            // so no partial order/stock deduction happened.
            $this->addError('cart', $e->getMessage().' Please update your cart.');

            return;
        }

        event(new OrderPlaced($order));

        // Queued so the customer doesn't wait on SMTP during checkout —
        // requires QUEUE_CONNECTION=database and `php artisan queue:work`
        // running (see README). Falls back to sending synchronously if
        // queueing isn't configured, which is fine for a demo.
        Mail::to($order->customer_email)->queue(new OrderReceipt($order));

        session()->forget('cart');
        $this->placedOrderNumber = $order->order_number;
    }

    public function render()
    {
        return view('livewire.storefront.checkout', [
            'cartItems' => $this->cartItems,
            'cartTotalCents' => $this->cartTotalCents,
        ]);
    }
}
