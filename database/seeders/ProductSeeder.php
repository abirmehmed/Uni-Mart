<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Test Widget',      'sku' => 'TW-001',  'category' => 'Test',        'price_cents' => 999,  'cost_cents' => 400,  'stock_quantity' => 4,  'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=60'],
            ['name' => 'Classic Mug',      'sku' => 'MUG-001', 'category' => 'Home',        'price_cents' => 1299, 'cost_cents' => 500,  'stock_quantity' => 25, 'image_url' => 'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?w=400&q=60'],
            ['name' => 'Canvas Tote Bag',  'sku' => 'BAG-001', 'category' => 'Accessories', 'price_cents' => 1899, 'cost_cents' => 800,  'stock_quantity' => 15, 'image_url' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=400&q=60'],
            ['name' => 'Notebook - Ruled', 'sku' => 'NB-001',  'category' => 'Stationery',  'price_cents' => 599,  'cost_cents' => 250,  'stock_quantity' => 40, 'image_url' => 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400&q=60'],
            ['name' => 'Wireless Mouse',   'sku' => 'MSE-001', 'category' => 'Electronics', 'price_cents' => 2499, 'cost_cents' => 1200, 'stock_quantity' => 12, 'image_url' => 'https://images.unsplash.com/photo-1587145820266-a5951ee6f620?w=400&q=60'],
            ['name' => 'Sticker Pack',     'sku' => 'STK-001', 'category' => 'Stationery',  'price_cents' => 399,  'cost_cents' => 150,  'stock_quantity' => 60, 'image_url' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=400&q=60'],

            // Groceries / daily essentials
            ['name' => 'Tea Cup',      'sku' => 'CUP-001',  'category' => 'Home',      'price_cents' => 899,  'cost_cents' => 350, 'stock_quantity' => 20, 'image_url' => 'https://images.unsplash.com/photo-1523920290228-4f321a939b4c?w=400&q=60'],
            ['name' => 'Corn (each)',  'sku' => 'GRO-001',  'category' => 'Grocery',   'price_cents' => 79,   'cost_cents' => 30,  'stock_quantity' => 80, 'image_url' => 'https://images.unsplash.com/photo-1649251037566-6881b4956615?w=400&q=60'],
            ['name' => 'Rice (5kg)',   'sku' => 'GRO-002',  'category' => 'Grocery',   'price_cents' => 1299, 'cost_cents' => 900, 'stock_quantity' => 30, 'image_url' => null],
            ['name' => 'Eggs (dozen)', 'sku' => 'GRO-003',  'category' => 'Grocery',   'price_cents' => 449,  'cost_cents' => 300, 'stock_quantity' => 50, 'image_url' => null],
            ['name' => 'Beef (1kg)',   'sku' => 'GRO-004',  'category' => 'Grocery',   'price_cents' => 1899, 'cost_cents' => 1400,'stock_quantity' => 15, 'image_url' => null],
            ['name' => 'Pencil (pack of 12)', 'sku' => 'STA-001', 'category' => 'Stationery', 'price_cents' => 349, 'cost_cents' => 120, 'stock_quantity' => 45, 'image_url' => null],
            ['name' => 'Sun Hat',      'sku' => 'APP-001',  'category' => 'Apparel',   'price_cents' => 1599, 'cost_cents' => 700, 'stock_quantity' => 18, 'image_url' => null],
            ['name' => 'Clothes Hanger (pack of 10)', 'sku' => 'HOM-001', 'category' => 'Home', 'price_cents' => 699, 'cost_cents' => 250, 'stock_quantity' => 35, 'image_url' => null],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'price_cents' => $product['price_cents'],
                    'cost_cents' => $product['cost_cents'],
                    'stock_quantity' => $product['stock_quantity'],
                    'image_url' => $product['image_url'],
                    'is_active' => true,
                ]
            );
        }
    }
}
