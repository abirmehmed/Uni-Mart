<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Backfills realistic-looking historical orders across the current month,
 * purely for demoing the Reports calendar. Does NOT touch product stock —
 * these are synthetic past sales, not live inventory-affecting transactions.
 */
class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::query()->where('is_active', true)->get();

        if ($products->isEmpty()) {
            $this->command?->warn('No active products found — run ProductSeeder first.');
            return;
        }

        $daysBack = 60;

        for ($i = 0; $i < $daysBack; $i++) {
            $date = Carbon::now()->subDays($i);

            // Skip some days randomly so the calendar isn't unrealistically solid
            if (random_int(1, 100) > 70) {
                continue;
            }

            $ordersToday = random_int(1, 4);

            for ($o = 0; $o < $ordersToday; $o++) {
                $source = random_int(0, 1) ? 'online' : 'pos';
                $lineItems = $products->random(min(random_int(1, 3), $products->count()));

                $totalCents = 0;
                $pivotData = [];

                foreach ($lineItems as $product) {
                    $qty = random_int(1, 3);
                    $lineTotal = $product->price_cents * $qty;
                    $totalCents += $lineTotal;

                    $pivotData[$product->id] = [
                        'quantity' => $qty,
                        'price_at_time_cents' => $product->price_cents,
                    ];
                }

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber($source),
                    'customer_name' => $source === 'online' ? 'Demo Customer' : null,
                    'customer_email' => $source === 'online' ? 'demo@example.com' : null,
                    'customer_address' => $source === 'online' ? '123 Demo St' : null,
                    'total_price_cents' => $totalCents,
                    'source' => $source,
                    'status' => 'paid',
                    'cashier_id' => $source === 'pos' ? 1 : null,
                ]);

                $order->products()->attach($pivotData);

                // Backdate to spread realistically through the day
                $order->created_at = $date->copy()->setTime(random_int(9, 20), random_int(0, 59));
                $order->updated_at = $order->created_at;
                $order->save();
            }
        }

        $this->command?->info('Demo orders backfilled across the last '.$daysBack.' days.');
    }
}
