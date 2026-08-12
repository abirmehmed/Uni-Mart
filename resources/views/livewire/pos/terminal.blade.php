<div
    x-data="{
        keypadBuffer: '',
        lookupOpen: false,
        toastVisible: false,
        toastMessage: '',
        pressKey(digit) {
            this.keypadBuffer += digit;
        },
        clearKeypad() {
            this.keypadBuffer = '';
        },
        confirmKeypad() {
            $wire.applyKeypadQuantity(parseInt(this.keypadBuffer || '0', 10));
            this.keypadBuffer = '';
        },
        showOrderToast(message) {
            this.toastMessage = message;
            this.toastVisible = true;
            playBeep();
            setTimeout(() => { this.toastVisible = false; }, 6000);
        },
    }"
    x-init="
        Echo.channel('orders').listen('.order.placed', (e) => {
            if (e.source === 'online') {
                showOrderToast('New online order ' + e.order_number + ' — $' + e.total_price);
            }
        });
    "
    class="relative py-6"
>
    <div
        x-show="toastVisible"
        x-transition
        class="fixed right-6 top-6 z-50 border-2 border-amber-dark bg-ink px-5 py-3 font-mono text-xs uppercase tracking-wide text-white shadow-xl"
        style="display: none;"
    >
        <span x-text="toastMessage"></span>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <div>
            <p class="mb-1 font-mono text-xs uppercase tracking-widest text-amber-dark">Register</p>
            <h1 class="font-display text-2xl font-bold uppercase tracking-tight text-ink">New sale</h1>
        </div>
        <button @click="lookupOpen = true" class="rounded-sm border border-ink/15 bg-white px-3 py-2 font-mono text-xs uppercase tracking-wide text-ink/60 shadow-sm transition-colors hover:bg-steel">
            Quick lookup
        </button>
    </div>

    @error('cart')
        <div class="mb-4 border border-stamp/30 bg-white px-4 py-3 font-mono text-xs text-stamp shadow-sm">{{ $message }}</div>
    @enderror

    <div class="grid grid-cols-5 gap-4" style="height: 620px;">
        <div class="col-span-3 flex flex-col overflow-hidden border border-ink/10 bg-white shadow-lg shadow-ink/5">
            <div class="flex flex-wrap gap-2 border-b border-ink/10 p-3">
                <button
                    wire:click="selectCategory(null)"
                    class="rounded-sm px-3 py-1.5 font-mono text-[11px] uppercase tracking-wide transition-colors {{ $selectedCategory === null ? 'bg-ink text-white shadow-sm' : 'border border-ink/15 text-ink/50 hover:border-ink/30' }}"
                >
                    All
                </button>
                @foreach ($this->categories as $category)
                    <button
                        wire:click="selectCategory('{{ $category }}')"
                        class="rounded-sm px-3 py-1.5 font-mono text-[11px] uppercase tracking-wide transition-colors {{ $selectedCategory === $category ? 'bg-ink text-white shadow-sm' : 'border border-ink/15 text-ink/50 hover:border-ink/30' }}"
                    >
                        {{ $category }}
                    </button>
                @endforeach
            </div>

            <div class="grid flex-1 auto-rows-min grid-cols-3 gap-3 overflow-y-auto p-3">
                @forelse ($this->products as $product)
                    <button
                        wire:click="addToCart({{ $product->id }})"
                        wire:key="pos-product-{{ $product->id }}"
                        @disabled($product->stock_quantity <= 0)
                        class="flex items-center gap-3 rounded-sm border border-ink/10 p-2 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-amber-dark hover:bg-amber/5 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0 disabled:hover:shadow-sm"
                    >
                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-sm bg-steel">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center font-mono text-[8px] uppercase text-ink/30">No image</div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-semibold text-ink">{{ $product->name }}</span>
                            <span class="font-mono text-xs text-ink/50">${{ $product->price }}</span>
                            <span
                                wire:key="pos-stock-{{ $product->id }}-{{ $product->stock_quantity }}"
                                class="mt-0.5 block font-mono text-[10px] font-medium uppercase tracking-wide {{ $product->stock_quantity > 0 ? 'text-ledger' : 'text-stamp' }}"
                            >
                                {{ $product->stock_quantity > 0 ? $product->stock_quantity.' in stock' : 'out of stock' }}
                            </span>
                        </div>
                    </button>
                @empty
                    <p class="col-span-3 py-10 text-center font-mono text-sm text-ink/30">No products in this category.</p>
                @endforelse
            </div>
        </div>

        <div class="col-span-2 flex flex-col border border-ink/10 bg-white shadow-lg shadow-ink/5">
            <div class="border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
                <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">Current sale</span>
            </div>

            @if ($lastCompletedOrderNumber)
                <div class="m-3 border border-ledger/30 bg-ledger/5 p-4 text-center shadow-sm">
                    <p class="font-mono text-xs uppercase tracking-widest text-ledger">Sale complete</p>
                    <p class="mt-2 font-mono text-sm font-semibold text-ink">{{ $lastCompletedOrderNumber }}</p>
                    <button wire:click="clearSale" class="mt-4 rounded-sm bg-ink px-3 py-1.5 font-mono text-[11px] uppercase tracking-wide text-white shadow-sm transition-colors hover:bg-amber-dark">
                        Start next sale
                    </button>
                </div>
            @else
                <div class="flex-1 overflow-y-auto p-3 font-mono">
                    @forelse ($this->cartItems as $item)
                        <div
                            wire:key="cart-{{ $item->product->id }}"
                            wire:click="setActiveLine({{ $item->product->id }})"
                            class="mb-2 flex cursor-pointer items-center justify-between rounded-sm p-2 text-sm transition-colors {{ $activeLineProductId === $item->product->id ? 'bg-amber/10 ring-1 ring-amber-dark' : 'hover:bg-steel' }}"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-sans font-medium text-ink">{{ $item->product->name }}</p>
                                <p class="text-xs text-ink/40">{{ $item->quantity }} &times; ${{ $item->product->price }}</p>
                            </div>
                            <span class="text-ink">${{ number_format($item->subtotal_cents / 100, 2) }}</span>
                            <button
                                wire:click.stop="removeFromCart({{ $item->product->id }})"
                                class="ml-2 text-ink/25 hover:text-stamp"
                            >&times;</button>
                        </div>
                    @empty
                        <p class="py-10 text-center font-sans text-sm text-ink/30">Tap a product to start a sale.</p>
                    @endforelse
                </div>

                <div class="border-t-2 border-dashed border-ink/15 p-3">
                    <div class="mb-2 flex items-center justify-between font-mono text-xs text-ink/40">
                        <span>
                            @if ($activeLineProductId)
                                Editing qty
                            @else
                                Tap a line to edit
                            @endif
                        </span>
                        <span class="text-sm font-semibold text-ink" x-text="keypadBuffer || '&mdash;'"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ([1,2,3,4,5,6,7,8,9] as $digit)
                            <button
                                @click="pressKey('{{ $digit }}')"
                                :disabled="{{ $activeLineProductId ? 'false' : 'true' }}"
                                class="rounded-sm border border-ink/15 py-2.5 font-mono text-base font-medium text-ink shadow-sm transition-colors hover:bg-steel disabled:opacity-30 disabled:shadow-none"
                            >{{ $digit }}</button>
                        @endforeach
                        <button @click="clearKeypad()" class="rounded-sm border border-ink/15 py-2.5 font-mono text-base text-ink/50 shadow-sm transition-colors hover:bg-steel">C</button>
                        <button
                            @click="pressKey('0')"
                            :disabled="{{ $activeLineProductId ? 'false' : 'true' }}"
                            class="rounded-sm border border-ink/15 py-2.5 font-mono text-base font-medium text-ink shadow-sm transition-colors hover:bg-steel disabled:opacity-30 disabled:shadow-none"
                        >0</button>
                        <button
                            @click="confirmKeypad()"
                            :disabled="{{ $activeLineProductId ? 'false' : 'true' }}"
                            class="rounded-sm bg-amber-dark py-2.5 font-mono text-base font-medium text-white shadow-sm transition-colors hover:bg-ink disabled:opacity-30 disabled:shadow-none"
                        >Enter</button>
                    </div>
                </div>

                <div class="border-t-2 border-dashed border-ink/15 p-3">
                    <div class="mb-3 flex items-center justify-between font-mono text-sm font-semibold text-ink">
                        <span class="uppercase tracking-wide">Total</span>
                        <span>${{ number_format($this->totalCents / 100, 2) }}</span>
                    </div>
                    <button
                        wire:click="payAndComplete"
                        wire:loading.attr="disabled"
                        class="w-full rounded-sm bg-ledger px-4 py-3 font-mono text-sm uppercase tracking-widest text-white shadow-sm transition-colors hover:bg-ink disabled:opacity-60"
                    >
                        Pay &amp; Complete
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div
        x-show="lookupOpen"
        x-transition
        @keydown.escape.window="lookupOpen = false"
        class="fixed inset-0 z-40 flex items-start justify-center bg-ink/40 pt-24"
        style="display: none;"
    >
        <div @click.outside="lookupOpen = false" class="w-full max-w-md border border-ink/10 bg-white shadow-2xl">
            <div class="border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
                <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">Quick lookup</span>
            </div>
            <div class="p-4">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="lookupSearch"
                    placeholder="Search by name or SKU..."
                    class="w-full rounded-sm border-ink/15 font-mono text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20"
                    x-ref="lookupInput"
                    x-init="$watch('lookupOpen', value => value && setTimeout(() => $refs.lookupInput.focus(), 50))"
                >
                <div class="mt-3 max-h-72 space-y-1 overflow-y-auto">
                    @foreach ($this->lookupResults as $product)
                        <button
                            wire:click="addToCart({{ $product->id }}); $wire.lookupSearch = ''"
                            @click="lookupOpen = false"
                            class="flex w-full items-center justify-between rounded-sm px-3 py-2 text-left text-sm transition-colors hover:bg-steel"
                        >
                            <span>{{ $product->name }}</span>
                            <span class="font-mono text-xs text-ink/40">{{ $product->sku }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function playBeep() {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 880;
        gain.gain.setValueAtTime(0.15, ctx.currentTime);
        osc.start();
        osc.stop(ctx.currentTime + 0.15);
    }

    (function ($) {
        let buffer = '';
        let lastKeyTime = 0;

        $(document).on('keydown', function (e) {
            const now = Date.now();
            const gap = now - lastKeyTime;
            lastKeyTime = now;

            if (e.key === 'Enter') {
                if (buffer.length >= 3) {
                    @this.call('addToCartBySku', buffer);
                }
                buffer = '';
                return;
            }

            if (e.key.length === 1) {
                buffer = gap < 60 ? buffer + e.key : e.key;
            }
        });
    })(jQuery);
</script>
