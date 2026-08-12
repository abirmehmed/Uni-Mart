<?php

namespace App\Models;

use App\Exceptions\InsufficientStockException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'category', 'description', 'price_cents', 'cost_cents', 'stock_quantity', 'image_url', 'is_active',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'cost_cents' => 'integer',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['price', 'cost', 'stock_status'];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity', 'price_at_time_cents')
            ->withTimestamps();
    }

    // Convenience accessor: price_cents / 100 → "12.99" for Blade/Livewire display
    public function getPriceAttribute(): string
    {
        return number_format($this->price_cents / 100, 2);
    }

    public function getCostAttribute(): string
    {
        return number_format($this->cost_cents / 100, 2);
    }

    public function getStockStatusAttribute(): string
    {
        return $this->stock_quantity > 0 ? 'In Stock' : 'Out of Stock';
    }

    /**
     * Atomically decrement stock for a sale. Used by both the online checkout
     * and the POS "Pay & Complete" action — this is the single choke point
     * that guarantees no overselling regardless of which channel sells the item.
     *
     * lockForUpdate() takes a row-level lock inside the transaction so two
     * simultaneous sales (one online, one at the register) can't both read
     * "stock = 1" and both succeed.
     *
     * @throws InsufficientStockException
     */
    public static function sell(int $productId, int $quantity): self
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $product = self::query()->lockForUpdate()->findOrFail($productId);

            if ($product->stock_quantity < $quantity) {
                throw new InsufficientStockException($product, $quantity);
            }

            $product->decrement('stock_quantity', $quantity);

            return $product->fresh();
        });
    }

    /**
     * Restock (e.g. order cancelled/refunded). Also atomic for symmetry.
     */
    public static function restock(int $productId, int $quantity): self
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $product = self::query()->lockForUpdate()->findOrFail($productId);
            $product->increment('stock_quantity', $quantity);

            return $product->fresh();
        });
    }
}
