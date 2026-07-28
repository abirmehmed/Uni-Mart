<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Homepage extends Component
{
    // Cart lives in the session, not the database — there's no customer
    // account system in this MVP, so the session IS the cart's identity.
    // Shape: [product_id => quantity]
    public array $cart = [];

    public function mount(): void
    {
        $this->cart = session('cart', []);
    }

    /**
     * Re-renders the grid whenever ANY stock change broadcasts — a sale on
     * the POS terminal or another browser tab shows up here instantly,
     * same mechanism as the Admin Dashboard.
     */
    #[On('echo:inventory,stock.updated')]
    public function onStockBroadcast(): void
    {
        //
    }

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $currentQtyInCart = $this->cart[$productId] ?? 0;

        // Soft guard here for UX (instant feedback); the hard guarantee
        // against overselling still happens at checkout via Product::sell(),
        // since stock can change between "add to cart" and "place order".
        if ($currentQtyInCart + 1 > $product->stock_quantity) {
            $this->addError('cart', "Only {$product->stock_quantity} of \"{$product->name}\" available.");

            return;
        }

        $this->cart[$productId] = $currentQtyInCart + 1;
        session(['cart' => $this->cart]);
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
        session(['cart' => $this->cart]);
    }

    public function updateCartQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);

            return;
        }

        $product = Product::findOrFail($productId);

        // Clamp to available stock rather than rejecting outright — nicer
        // UX than an error for "you typed one too many".
        $this->cart[$productId] = min($quantity, $product->stock_quantity);
        session(['cart' => $this->cart]);
    }

    #[Computed]
    public function cartItems()
    {
        if (empty($this->cart)) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', array_keys($this->cart))
            ->get()
            ->map(function (Product $product) {
                $qty = $this->cart[$product->id];

                return (object) [
                    'product' => $product,
                    'quantity' => $qty,
                    'subtotal_cents' => $product->price_cents * $qty,
                ];
            });
    }

    #[Computed]
    public function cartTotalCents(): int
    {
        return $this->cartItems->sum('subtotal_cents');
    }

    public function render()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.storefront.homepage', [
            'products' => $products,
        ]);
    }
}
