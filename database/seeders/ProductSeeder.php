<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Classic Mug',                'sku' => 'MUG-001', 'category' => 'Home',        'price_cents' => 1299, 'cost_cents' => 500,  'stock_quantity' => 25, 'image' => 'mug-001.jpg'],
            ['name' => 'Tea Cup',                    'sku' => 'CUP-001', 'category' => 'Home',        'price_cents' => 899,  'cost_cents' => 350,  'stock_quantity' => 20, 'image' => 'cup-001.jpg'],
            ['name' => 'Water Bottle',                'sku' => 'BOT-001', 'category' => 'Home',        'price_cents' => 1499, 'cost_cents' => 600,  'stock_quantity' => 20, 'image' => 'bot-001.jpg'],
            ['name' => 'Clothes Hanger (pack of 10)', 'sku' => 'HOM-001', 'category' => 'Home',        'price_cents' => 699,  'cost_cents' => 250,  'stock_quantity' => 35, 'image' => 'hom-001.jpg'],
            ['name' => 'Kitchen Towel Set',           'sku' => 'HOM-002', 'category' => 'Home',        'price_cents' => 999,  'cost_cents' => 400,  'stock_quantity' => 25, 'image' => 'hom-002.jpg'],
            ['name' => 'Canvas Tote Bag',             'sku' => 'BAG-001', 'category' => 'Accessories', 'price_cents' => 1899, 'cost_cents' => 800,  'stock_quantity' => 15, 'image' => 'bag-001.jpg'],
            ['name' => 'Leather Wallet',              'sku' => 'ACC-001', 'category' => 'Accessories', 'price_cents' => 2499, 'cost_cents' => 1100, 'stock_quantity' => 12, 'image' => 'acc-001.jpg'],
            ['name' => 'Sunglasses',                  'sku' => 'ACC-002', 'category' => 'Accessories', 'price_cents' => 1699, 'cost_cents' => 700,  'stock_quantity' => 18, 'image' => 'acc-002.jpg'],
            ['name' => 'Wristwatch',                  'sku' => 'ACC-003', 'category' => 'Accessories', 'price_cents' => 3499, 'cost_cents' => 1600, 'stock_quantity' => 10, 'image' => 'acc-003.jpg'],
            ['name' => 'Sun Hat',                     'sku' => 'APP-001', 'category' => 'Apparel',     'price_cents' => 1599, 'cost_cents' => 700,  'stock_quantity' => 18, 'image' => 'app-001.jpg'],
            ['name' => 'Cotton T-Shirt',              'sku' => 'APP-002', 'category' => 'Apparel',     'price_cents' => 1299, 'cost_cents' => 550,  'stock_quantity' => 30, 'image' => 'app-002.jpg'],
            ['name' => 'Wool Socks',                  'sku' => 'APP-003', 'category' => 'Apparel',     'price_cents' => 699,  'cost_cents' => 250,  'stock_quantity' => 40, 'image' => 'app-003.jpg'],
            ['name' => 'Notebook - Ruled',            'sku' => 'NB-001',  'category' => 'Stationery',  'price_cents' => 599,  'cost_cents' => 250,  'stock_quantity' => 40, 'image' => 'nb-001.jpg'],
            ['name' => 'Pencil (pack of 12)',         'sku' => 'STA-001', 'category' => 'Stationery',  'price_cents' => 349,  'cost_cents' => 120,  'stock_quantity' => 45, 'image' => 'sta-001.jpg'],
            ['name' => 'Ballpoint Pen (pack of 5)',   'sku' => 'STA-002', 'category' => 'Stationery',  'price_cents' => 299,  'cost_cents' => 100,  'stock_quantity' => 50, 'image' => 'sta-002.jpg'],
            ['name' => 'Sticker Pack',                'sku' => 'STK-001', 'category' => 'Stationery',  'price_cents' => 399,  'cost_cents' => 150,  'stock_quantity' => 60, 'image' => 'stk-001.jpg'],
            ['name' => 'Wireless Mouse',              'sku' => 'MSE-001', 'category' => 'Electronics', 'price_cents' => 2499, 'cost_cents' => 1200, 'stock_quantity' => 12, 'image' => 'mse-001.jpg'],
            ['name' => 'USB Flash Drive (32GB)',      'sku' => 'ELE-001', 'category' => 'Electronics', 'price_cents' => 999,  'cost_cents' => 400,  'stock_quantity' => 25, 'image' => 'ele-001.jpg'],
            ['name' => 'Phone Charger Cable',         'sku' => 'ELE-002', 'category' => 'Electronics', 'price_cents' => 799,  'cost_cents' => 300,  'stock_quantity' => 30, 'image' => 'ele-002.jpg'],
            ['name' => 'Corn (each)',                 'sku' => 'GRO-001', 'category' => 'Grocery',     'price_cents' => 79,   'cost_cents' => 30,   'stock_quantity' => 80, 'image' => 'gro-001.jpg'],
            ['name' => 'Rice (5kg)',                  'sku' => 'GRO-002', 'category' => 'Grocery',     'price_cents' => 1299, 'cost_cents' => 900,  'stock_quantity' => 30, 'image' => 'gro-002.jpg'],
            ['name' => 'Eggs (dozen)',                'sku' => 'GRO-003', 'category' => 'Grocery',     'price_cents' => 449,  'cost_cents' => 300,  'stock_quantity' => 50, 'image' => 'gro-003.jpg'],
            ['name' => 'Beef (1kg)',                  'sku' => 'GRO-004', 'category' => 'Grocery',     'price_cents' => 1899, 'cost_cents' => 1400, 'stock_quantity' => 15, 'image' => 'gro-004.jpg'],
            ['name' => 'Milk (1L)',                   'sku' => 'GRO-005', 'category' => 'Grocery',     'price_cents' => 249,  'cost_cents' => 160,  'stock_quantity' => 40, 'image' => 'gro-005.jpg'],
            ['name' => 'Bread Loaf',                  'sku' => 'GRO-006', 'category' => 'Grocery',     'price_cents' => 329,  'cost_cents' => 140,  'stock_quantity' => 20, 'image' => 'gro-006.jpg'],
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
                    'image_url' => $product['image'] ? asset('images/products/'.$product['image']) : null,
                    'is_active' => true,
                ]
            );
        }
    }
}
