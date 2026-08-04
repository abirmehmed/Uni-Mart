<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class DailySalesSummary extends Component
{
    #[On('echo:orders,.order.placed')]
    public function onOrderPlaced(): void
    {
        //
    }

    #[Computed]
    public function todayOrders()
    {
        return Order::query()
            ->whereDate('created_at', today())
            ->where('status', 'paid')
            ->get();
    }

    #[Computed]
    public function onlineRevenueCents(): int
    {
        return $this->todayOrders->where('source', 'online')->sum('total_price_cents');
    }

    #[Computed]
    public function posRevenueCents(): int
    {
        return $this->todayOrders->where('source', 'pos')->sum('total_price_cents');
    }

    #[Computed]
    public function totalRevenueCents(): int
    {
        return $this->onlineRevenueCents + $this->posRevenueCents;
    }

    public function render()
    {
        return view('livewire.admin.daily-sales-summary');
    }
}
