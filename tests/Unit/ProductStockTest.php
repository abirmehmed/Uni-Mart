<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_selling_decrements_stock(): void
    {
        $product = Product::create([
            'name' => 'Test Item',
            'sku' => 'TEST-1',
            'price_cents' => 1000,
            'stock_quantity' => 5,
        ]);

        Product::sell($product->id, 2);

        $this->assertSame(3, $product->fresh()->stock_quantity);
    }

    public function test_selling_more_than_available_throws(): void
    {
        $product = Product::create([
            'name' => 'Test Item',
            'sku' => 'TEST-2',
            'price_cents' => 1000,
            'stock_quantity' => 1,
        ]);

        $this->expectException(InsufficientStockException::class);

        Product::sell($product->id, 5);
    }

    public function test_stock_never_goes_negative(): void
    {
        $product = Product::create([
            'name' => 'Test Item',
            'sku' => 'TEST-3',
            'price_cents' => 1000,
            'stock_quantity' => 1,
        ]);

        try {
            Product::sell($product->id, 5);
        } catch (InsufficientStockException) {
            // expected
        }

        $this->assertSame(1, $product->fresh()->stock_quantity);
    }
}
