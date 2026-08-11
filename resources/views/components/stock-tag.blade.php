@props(['quantity'])
@php
    $inStock = $quantity > 0;
    $lowStock = $inStock && $quantity <= 5;
    $critical = ! $inStock || $lowStock;
@endphp
<span
    {{ $attributes->merge(['class' =>
        'relative inline-flex items-center gap-2 rounded-sm px-2.5 py-1 pl-4 '
        . 'font-mono tracking-wide '
        . ($critical
            ? 'rotate-2 border border-stamp bg-stamp py-1.5 text-[13px] font-bold text-white shadow-md'
            : '-rotate-2 border border-ledger/40 bg-white text-[11px] font-medium text-ledger')
    ]) }}
>
    <span class="absolute left-1.5 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full border border-current"></span>
    {{ $inStock ? $quantity.' IN STOCK' : 'OUT OF STOCK' }}
</span>
