<div class="mb-8 grid grid-cols-3 gap-4">
    <div class="border border-ink/10 bg-white p-4" wire:key="online-{{ $this->onlineRevenueCents }}">
        <p class="font-mono text-[11px] uppercase tracking-widest text-ink/40">Today &middot; Online</p>
        <p class="mt-1 font-mono text-2xl font-semibold text-ink">${{ number_format($this->onlineRevenueCents / 100, 2) }}</p>
        <p class="font-mono text-[11px] text-ink/40">{{ $this->todayOrders->where('source', 'online')->count() }} orders</p>
    </div>

    <div class="border border-ink/10 bg-white p-4" wire:key="pos-{{ $this->posRevenueCents }}">
        <p class="font-mono text-[11px] uppercase tracking-widest text-ink/40">Today &middot; POS</p>
        <p class="mt-1 font-mono text-2xl font-semibold text-ink">${{ number_format($this->posRevenueCents / 100, 2) }}</p>
        <p class="font-mono text-[11px] text-ink/40">{{ $this->todayOrders->where('source', 'pos')->count() }} sales</p>
    </div>

    <div class="relative rotate-1 border-2 border-dashed border-amber-dark bg-white p-4" wire:key="total-{{ $this->totalRevenueCents }}">
        <p class="font-mono text-[11px] uppercase tracking-widest text-amber-dark">Today &middot; Combined</p>
        <p class="mt-1 font-mono text-2xl font-semibold text-ink">${{ number_format($this->totalRevenueCents / 100, 2) }}</p>
        <p class="font-mono text-[11px] text-amber-dark">{{ $this->todayOrders->count() }} total orders</p>
    </div>
</div>
