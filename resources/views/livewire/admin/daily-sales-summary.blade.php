<div class="mb-6 grid grid-cols-3 gap-4">
    <div class="rounded-lg border border-slate-200 bg-white p-4" wire:key="online-{{ $this->onlineRevenueCents }}">
        <p class="text-xs font-medium text-slate-400">Today &middot; Online</p>
        <p class="mt-1 text-2xl font-semibold text-slate-900">${{ number_format($this->onlineRevenueCents / 100, 2) }}</p>
        <p class="text-xs text-slate-400">{{ $this->todayOrders->where('source', 'online')->count() }} orders</p>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-4" wire:key="pos-{{ $this->posRevenueCents }}">
        <p class="text-xs font-medium text-slate-400">Today &middot; POS</p>
        <p class="mt-1 text-2xl font-semibold text-slate-900">${{ number_format($this->posRevenueCents / 100, 2) }}</p>
        <p class="text-xs text-slate-400">{{ $this->todayOrders->where('source', 'pos')->count() }} sales</p>
    </div>

    <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4" wire:key="total-{{ $this->totalRevenueCents }}">
        <p class="text-xs font-medium text-indigo-500">Today &middot; Combined</p>
        <p class="mt-1 text-2xl font-semibold text-indigo-900">${{ number_format($this->totalRevenueCents / 100, 2) }}</p>
        <p class="text-xs text-indigo-500">{{ $this->todayOrders->count() }} total orders</p>
    </div>
</div>
