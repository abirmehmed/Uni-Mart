<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Test Widget', 'sku' => 'TW-001', 'category' => 'Test', 'price_cents' => 999, 'stock_quantity' => 4],
            ['name' => 'Classic Mug', 'sku' => 'MUG-001', 'category' => 'Home', 'price_cents' => 1299, 'stock_quantity' => 25],
            ['name' => 'Canvas Tote Bag', 'sku' => 'BAG-001', 'category' => 'Accessories', 'price_cents' => 1899, 'stock_quantity' => 15],
            ['name' => 'Notebook - Ruled', 'sku' => 'NB-001', 'category' => 'Stationery', 'price_cents' => 599, 'stock_quantity' => 40],
            ['name' => 'Wireless Mouse', 'sku' => 'MSE-001', 'category' => 'Electronics', 'price_cents' => 2499, 'stock_quantity' => 12],
            ['name' => 'Sticker Pack', 'sku' => 'STK-001', 'category' => 'Stationery', 'price_cents' => 399, 'stock_quantity' => 60],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'price_cents' => $product['price_cents'],
                    'stock_quantity' => $product['stock_quantity'],
                    'is_active' => true,
                ]
            );
        }
    }
}