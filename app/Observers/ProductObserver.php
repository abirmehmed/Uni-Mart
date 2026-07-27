<?php

namespace App\Observers;

use App\Events\StockUpdated;
use App\Models\Product;

class ProductObserver
{
    /**
     * Fires on every Product::save() that touches an existing row — this
     * covers Product::sell(), Product::restock(), AND a plain admin edit
     * via the Livewire inline-editing table. One observer, every code path.
     */
    public function updated(Product $product): void
    {
        if ($product->wasChanged('stock_quantity')) {
            event(new StockUpdated($product));
        }
    }

    /**
     * A newly created product should also appear live on the storefront/POS
     * without a refresh, so treat creation as a stock event too.
     */
    public function created(Product $product): void
    {
        event(new StockUpdated($product));
    }
}
