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
     * Month-level KPIs for the summary strip: total revenue, total profit,
     * order count, and the single best day. Same current-cost caveat as
     * elsewhere in this component.
     */
    #[Computed]
    public function monthSummary(): array
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->endOfDay();

        $orders = Order::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->with('products')
            ->get();

        $revenueCents = $orders->sum('total_price_cents');

        $costCents = 0;
        foreach ($orders as $order) {
            foreach ($order->products as $product) {
                $costCents += $product->cost_cents * $product->pivot->quantity;
            }
        }

        $byDay = $orders
            ->groupBy(fn (Order $o) => $o->created_at->toDateString())
            ->map(fn ($group) => $group->sum('total_price_cents'));

        $bestDayDate = $byDay->sortDesc()->keys()->first();

        return [
            'revenueCents' => $revenueCents,
            'profitCents' => $revenueCents - $costCents,
            'orderCount' => $orders->count(),
            'bestDayDate' => $bestDayDate,
            'bestDayCents' => $bestDayDate ? $byDay[$bestDayDate] : 0,
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

    /**
     * Last 365 days of paid revenue, split per calendar month (GitHub-style
     * squares, Sun-Sat rows). Each month: label + flat cell list padded to
     * whole weeks. Cells are null padding or ['date', 'cents', 'level' 0-4].
     */
    #[Computed]
    public function yearHeatmap(): array
    {
        $end = now()->startOfDay();
        $start = $end->copy()->subDays(364);

        $totals = Order::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end->copy()->endOfDay()])
            ->get()
            ->groupBy(fn (Order $order) => $order->created_at->toDateString())
            ->map(fn (Collection $orders) => (int) $orders->sum('total_price_cents'));

        $max = max(1, (int) $totals->max());

        $months = [];

        for ($i = 0; $i < 365; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->format('Y-m');

            if (! isset($months[$key])) {
                $label = $day->format('M');
                if ($i === 0 || $day->month === 1) {
                    $label .= " '" . $day->format('y');
                }
                $months[$key] = [
                    'label' => $label,
                    'cells' => array_fill(0, $day->dayOfWeek, null),
                ];
            }

            $date = $day->toDateString();
            $cents = (int) $totals->get($date, 0);
            $ratio = $cents / $max;

            $months[$key]['cells'][] = [
                'date' => $date,
                'cents' => $cents,
                'level' => match (true) {
                    $cents === 0 => 0,
                    $ratio < 0.25 => 1,
                    $ratio < 0.5 => 2,
                    $ratio < 0.75 => 3,
                    default => 4,
                },
            ];
        }

        foreach ($months as &$m) {
            while (count($m['cells']) % 7 !== 0) {
                $m['cells'][] = null;
            }
        }
        unset($m);

        return [
            'months' => array_values($months),
            'totalCents' => (int) $totals->sum(),
            'activeDays' => $totals->filter(fn ($c) => $c > 0)->count(),
        ];
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
