<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Test Widget',      'sku' => 'TW-001',  'category' => 'Test',        'price_cents' => 999,  'stock_quantity' => 4,  'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=60'],
            ['name' => 'Classic Mug',      'sku' => 'MUG-001', 'category' => 'Home',        'price_cents' => 1299, 'stock_quantity' => 25, 'image_url' => 'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?w=400&q=60'],
            ['name' => 'Canvas Tote Bag',  'sku' => 'BAG-001', 'category' => 'Accessories', 'price_cents' => 1899, 'stock_quantity' => 15, 'image_url' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=400&q=60'],
            ['name' => 'Notebook - Ruled', 'sku' => 'NB-001',  'category' => 'Stationery',  'price_cents' => 599,  'stock_quantity' => 40, 'image_url' => 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400&q=60'],
            ['name' => 'Wireless Mouse',   'sku' => 'MSE-001', 'category' => 'Electronics', 'price_cents' => 2499, 'stock_quantity' => 12, 'image_url' => 'https://images.unsplash.com/photo-1587145820266-a5951ee6f620?w=400&q=60'],
            ['name' => 'Sticker Pack',     'sku' => 'STK-001', 'category' => 'Stationery',  'price_cents' => 399,  'stock_quantity' => 60, 'image_url' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=400&q=60'],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['sku' => $product['sku']], [
                'name' => $product['name'],
                'category' => $product['category'],
                'price_cents' => $product['price_cents'],
                'stock_quantity' => $product['stock_quantity'],
                'image_url' => $product['image_url'],
                'is_active' => true,
            ]);
        }
    }
}
