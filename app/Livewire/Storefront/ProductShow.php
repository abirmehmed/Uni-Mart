<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProductShow extends Component
{
    public Product $product;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function addToCart(): void
    {
        $cart = session('cart', []);
        $current = $cart[$this->product->id] ?? 0;

        if ($current + 1 > $this->product->stock_quantity) {
            $this->addError('cart', "Only {$this->product->stock_quantity} of \"{$this->product->name}\" available.");
            return;
        }

        $cart[$this->product->id] = $current + 1;
        session(['cart' => $cart]);

        $this->redirectRoute('storefront.home', navigate: true);
    }

    /**
     * Related products, same category, ranked by units sold historically
     * (paid orders only). Falls back to a random pick from the same
     * category if there isn't enough sales history yet to fill 4 slots.
     */
    #[Computed]
    public function relatedProducts()
    {
        if (! $this->product->category) {
            return collect();
        }

        $soldCounts = DB::table('order_product')
            ->join('orders', 'orders.id', '=', 'order_product.order_id')
            ->join('products', 'products.id', '=', 'order_product.product_id')
            ->where('orders.status', 'paid')
            ->where('products.category', $this->product->category)
            ->where('products.id', '!=', $this->product->id)
            ->where('products.is_active', true)
            ->select('order_product.product_id', DB::raw('SUM(order_product.quantity) as total_sold'))
            ->groupBy('order_product.product_id')
            ->orderByDesc('total_sold')
            ->limit(4)
            ->pluck('total_sold', 'order_product.product_id');

        $ranked = Product::query()
            ->whereIn('id', $soldCounts->keys())
            ->get()
            ->sortByDesc(fn (Product $p) => $soldCounts[$p->id])
            ->values();

        $remaining = 4 - $ranked->count();

        if ($remaining > 0) {
            $filler = Product::query()
                ->where('category', $this->product->category)
                ->where('id', '!=', $this->product->id)
                ->where('is_active', true)
                ->whereNotIn('id', $ranked->pluck('id'))
                ->inRandomOrder()
                ->limit($remaining)
                ->get();

            $ranked = $ranked->concat($filler);
        }

        return $ranked;
    }

    public function render()
    {
        return view('livewire.storefront.product-show');
    }
}
