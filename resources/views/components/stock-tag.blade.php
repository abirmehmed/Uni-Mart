@props(['quantity'])

@php $inStock = $quantity > 0; @endphp

<span
    {{ $attributes->merge(['class' =>
        'relative inline-flex -rotate-2 items-center gap-2 rounded-sm border bg-white px-2.5 py-1 pl-4 '
        . 'font-mono text-[11px] font-medium tracking-wide '
        . ($inStock ? 'border-ledger/40 text-ledger' : 'border-stamp/40 text-stamp')
    ]) }}
>
    <span class="absolute left-1.5 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full border border-current"></span>
    {{ $inStock ? $quantity.' IN STOCK' : 'OUT OF STOCK' }}
</span>
