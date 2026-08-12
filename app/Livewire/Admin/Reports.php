<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Reports extends Component
{
    public int $year;
    public int $month;
    public ?string $selectedDate = null;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
        $this->selectedDate = now()->toDateString();
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
    }

    /**
     * Revenue per day for the visible month, keyed by Y-m-d.
     * Used to draw the small activity dot under each calendar cell.
     */
    #[Computed]
    public function dailyTotals(): Collection
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->endOfDay();

        return Order::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn (Order $order) => $order->created_at->toDateString())
            ->map(fn (Collection $orders) => $orders->sum('total_price_cents'));
    }

    /**
     * Full breakdown for the currently selected day: revenue, profit, order
     * count, source split, and top-selling product.
     *
     * Profit uses each product's CURRENT cost_cents against historical sale
     * quantities — not a snapshot of cost at the time of sale (only price is
     * snapshotted, via price_at_time_cents). Close enough for a dashboard,
     * but a price/cost change today will slightly shift past days' profit
     * figures if recalculated. A cost_at_time_cents column on order_product
     * would fix this properly if needed later.
     */
    #[Computed]
    public function selectedDaySummary(): array
    {
        if (! $this->selectedDate) {
            return $this->emptySummary();
        }

        $start = Carbon::parse($this->selectedDate)->startOfDay();
        $end = $start->copy()->endOfDay();

        $orders = Order::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->with('products')
            ->get();

        if ($orders->isEmpty()) {
            return $this->emptySummary();
        }

        $revenueCents = $orders->sum('total_price_cents');
        $onlineCents = $orders->where('source', 'online')->sum('total_price_cents');
        $posCents = $orders->where('source', 'pos')->sum('total_price_cents');

        $costCents = 0;
        $productQuantities = [];

        foreach ($orders as $order) {
            foreach ($order->products as $product) {
                $qty = $product->pivot->quantity;
                $costCents += $product->cost_cents * $qty;

                $productQuantities[$product->id] ??= ['name' => $product->name, 'qty' => 0];
                $productQuantities[$product->id]['qty'] += $qty;
            }
        }

        $topProduct = collect($productQuantities)->sortByDesc('qty')->first();

        return [
            'hasOrders' => true,
            'orderCount' => $orders->count(),
            'revenueCents' => $revenueCents,
            'onlineCents' => $onlineCents,
            'posCents' => $posCents,
            'costCents' => $costCents,
            'profitCents' => $revenueCents - $costCents,
            'topProductName' => $topProduct['name'] ?? null,
            'topProductQty' => $topProduct['qty'] ?? null,
        ];
    }

    protected function emptySummary(): array
    {
        return [
            'hasOrders' => false,
            'orderCount' => 0,
            'revenueCents' => 0,
            'onlineCents' => 0,
            'posCents' => 0,
            'costCents' => 0,
            'profitCents' => 0,
            'topProductName' => null,
            'topProductQty' => null,
        ];
    }

    /**
     * Daily revenue + profit across the whole visible month, for the trend
     * chart. Same current-cost-vs-historical-quantity caveat as
     * selectedDaySummary() applies here.
     */
    #[Computed]
    public function monthlyTrend(): array
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->endOfDay();

        $orders = Order::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->with('products')
            ->get()
            ->groupBy(fn (Order $order) => $order->created_at->day);

        $trend = [];

        for ($day = 1; $day <= $start->daysInMonth; $day++) {
            $dayOrders = $orders->get($day, collect());
            $revenueCents = $dayOrders->sum('total_price_cents');

            $costCents = 0;
            foreach ($dayOrders as $order) {
                foreach ($order->products as $product) {
                    $costCents += $product->cost_cents * $product->pivot->quantity;
                }
            }

            $trend[] = [
                'day' => $day,
                'revenue' => round($revenueCents / 100, 2),
                'profit' => round(($revenueCents - $costCents) / 100, 2),
            ];
        }

        return $trend;
    }

    /**
     * Total units sold per product across the visible month, for the
     * "top products" bar chart. Top 10 by quantity.
     */
    #[Computed]
    public function productsSoldThisMonth(): array
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->endOfDay();

        $quantities = [];

        Order::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->with('products')
            ->get()
            ->each(function (Order $order) use (&$quantities) {
                foreach ($order->products as $product) {
                    $quantities[$product->name] = ($quantities[$product->name] ?? 0) + $product->pivot->quantity;
                }
            });

        arsort($quantities);

        return array_slice($quantities, 0, 10, true);
    }

    #[Computed]
    public function calendarWeeks(): array
    {
        $firstOfMonth = Carbon::create($this->year, $this->month, 1);
        $daysInMonth = $firstOfMonth->daysInMonth;
        $startOffset = $firstOfMonth->dayOfWeek; // 0 = Sunday

        $cells = [];

        for ($i = 0; $i < $startOffset; $i++) {
            $cells[] = null;
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $cells[] = Carbon::create($this->year, $this->month, $day)->toDateString();
        }

        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }

        return array_chunk($cells, 7);
    }

    public function render()
    {
        return view('livewire.admin.reports');
    }
}
