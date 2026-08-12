<?php

namespace App\Livewire\Pos;

use App\Events\OrderPlaced;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.pos')]
class Terminal extends Component
{
    public array $cart = [];

    public ?string $selectedCategory = null;

    public ?int $activeLineProductId = null;

    public ?string $lastCompletedOrderNumber = null;

    public string $lookupSearch = '';

    #[On('echo:inventory,.stock.updated')]
    public function onStockBroadcast(): void
    {
        //
    }

    #[Computed]
    public function categories()
    {
        return Product::query()
            ->where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->where('is_active', true)
            ->when($this->selectedCategory, fn ($q) => $q->where('category', $this->selectedCategory))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function lookupResults()
    {
        if ($this->lookupSearch === '') {
            return collect();
        }

        return Product::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->lookupSearch}%")
                    ->orWhere('sku', 'like', "%{$this->lookupSearch}%");
            })
            ->limit(8)
            ->get();
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
            ->map(fn (Product $product) => (object) [
                'product' => $product,
                'quantity' => $this->cart[$product->id],
                'subtotal_cents' => $product->price_cents * $this->cart[$product->id],
            ]);
    }

    #[Computed]
    public function totalCents(): int
    {
        return $this->cartItems->sum('subtotal_cents');
    }

    /**
     * Real stock minus whatever's currently sitting in this sale — display
     * only. Actual stock_quantity is untouched until payAndComplete() runs
     * Product::sell(), same as the storefront's per-user available count.
     */
    public function availableStock(Product $product): int
    {
        return $product->stock_quantity - ($this->cart[$product->id] ?? 0);
    }

    public function selectCategory(?string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $current = $this->cart[$productId] ?? 0;

        if ($current + 1 > $product->stock_quantity) {
            $this->addError('cart', "Only {$product->stock_quantity} of \"{$product->name}\" left.");

            return;
        }

        $this->cart[$productId] = $current + 1;
        $this->activeLineProductId = $productId;
    }

    public function addToCartBySku(string $sku): void
    {
        $product = Product::where('sku', $sku)->where('is_active', true)->first();

        if (! $product) {
            $this->addError('cart', "No product found for SKU \"{$sku}\".");

            return;
        }

        $this->addToCart($product->id);
    }

    public function setActiveLine(int $productId): void
    {
        $this->activeLineProductId = $productId;
    }

    public function applyKeypadQuantity(int $quantity): void
    {
        if (! $this->activeLineProductId) {
            return;
        }

        if ($quantity <= 0) {
            unset($this->cart[$this->activeLineProductId]);
            $this->activeLineProductId = null;

            return;
        }

        $product = Product::findOrFail($this->activeLineProductId);
        $this->cart[$this->activeLineProductId] = min($quantity, $product->stock_quantity);
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);

        if ($this->activeLineProductId === $productId) {
            $this->activeLineProductId = null;
        }
    }

    public function clearSale(): void
    {
        $this->cart = [];
        $this->activeLineProductId = null;
        $this->lastCompletedOrderNumber = null;
    }

    public function payAndComplete(): void
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Current sale is empty.');

            return;
        }

        try {
            $order = DB::transaction(function () {
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber('pos'),
                    'total_price_cents' => 0,
                    'source' => 'pos',
                    'status' => 'paid',
                    'cashier_id' => auth()->id(),
                ]);

                $totalCents = 0;

                foreach ($this->cart as $productId => $quantity) {
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
            $this->addError('cart', $e->getMessage());

            return;
        }

        event(new OrderPlaced($order));

        $this->lastCompletedOrderNumber = $order->order_number;
        $this->cart = [];
        $this->activeLineProductId = null;
    }

    public function render()
    {
        return view('livewire.pos.terminal');
    }
}
