<div class="mx-auto max-w-2xl">
    @if ($placedOrderNumber)
        <div class="border border-ledger/30 bg-white p-10 text-center shadow-xl shadow-ink/5">
            <p class="mb-1 font-mono text-xs uppercase tracking-widest text-ledger">Order confirmed</p>
            <h1 class="mb-2 font-display text-4xl font-bold uppercase text-ink">Thanks!</h1>
            <p class="font-mono text-sm text-ink/60">
                Order <span class="font-semibold text-ink">{{ $placedOrderNumber }}</span> is confirmed.
                A receipt is on its way to your email.
            </p>
            <a href="{{ route('storefront.home') }}" wire:navigate class="mt-5 inline-block font-mono text-xs uppercase tracking-wide text-amber-dark hover:text-ink">
                &larr; Continue shopping
            </a>
        </div>
    @else
        <span class="mb-3 inline-flex -rotate-2 items-center gap-2 rounded-sm border border-amber-dark/40 bg-white px-2.5 py-1 font-mono text-[11px] font-medium uppercase tracking-widest text-amber-dark">
            <span class="h-1.5 w-1.5 rounded-full border border-current"></span>
            Final step
        </span>
        <h1 class="mb-8 font-display text-4xl font-bold uppercase tracking-tight text-ink">Checkout</h1>

        @error('cart')
            <div class="mb-4 border border-stamp/30 bg-white px-4 py-3 font-mono text-xs text-stamp shadow-sm">{{ $message }}</div>
        @enderror

        @if ($this->cartItems->isEmpty())
            <p class="font-mono text-sm text-ink/50">
                Your cart is empty.
                <a href="{{ route('storefront.home') }}" wire:navigate class="text-amber-dark hover:text-ink">Go shopping &rarr;</a>
            </p>
        @else
            <div class="mb-6 border border-ink/10 bg-white shadow-lg shadow-ink/5">
                <div class="border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
                    <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">Order summary</span>
                </div>
                <div class="space-y-2 p-5">
                    @foreach ($this->cartItems as $item)
                        <div class="flex items-baseline gap-1 text-sm">
                            <span class="text-ink/70">{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                            <span class="flex-1 border-b border-dotted border-ink/20"></span>
                            <span class="font-mono text-ink">${{ number_format($item->subtotal_cents / 100, 2) }}</span>
                        </div>
                    @endforeach
                    <div class="mt-3 flex justify-between border-t-2 border-dashed border-ink/15 pt-3 font-mono text-sm font-semibold text-ink">
                        <span class="uppercase tracking-wide">Total</span>
                        <span>${{ number_format($this->cartTotalCents / 100, 2) }}</span>
                    </div>
                </div>
            </div>

            <form wire:submit="placeOrder" class="space-y-5 border border-ink/10 bg-white p-7 shadow-lg shadow-ink/5">
                <div>
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Full name</label>
                    <input type="text" wire:model="customer_name" class="mt-1.5 w-full rounded-sm border-ink/15 py-2.5 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                    @error('customer_name') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Email</label>
                    <input type="email" wire:model="customer_email" class="mt-1.5 w-full rounded-sm border-ink/15 py-2.5 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                    @error('customer_email') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Shipping address</label>
                    <textarea wire:model="customer_address" rows="3" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20"></textarea>
                    @error('customer_address') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full rounded-sm bg-ink px-4 py-3 font-mono text-xs uppercase tracking-widest text-white shadow-sm transition-colors hover:bg-amber-dark disabled:opacity-60"
                >
                    <span wire:loading.remove>Place order</span>
                    <span wire:loading>Placing order&hellip;</span>
                </button>
            </form>
        @endif
    @endif
</div>
