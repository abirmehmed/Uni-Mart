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
        class="fixed right-6 top-6 z-50 rounded-lg bg-slate-900 px-5 py-3 text-sm font-medium text-white shadow-lg"
        style="display: none;"
    >
        🔔 <span x-text="toastMessage"></span>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-slate-900">Register</h1>
        <button @click="lookupOpen = true" class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">
            🔍 Quick lookup
        </button>
    </div>

    @error('cart')
        <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>
    @enderror

    <div class="grid grid-cols-5 gap-4" style="height: 620px;">
        <div class="col-span-3 flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white">
            <div class="flex flex-wrap gap-2 border-b border-slate-200 p-3">
                <button
                    wire:click="selectCategory(null)"
                    class="rounded-full px-3 py-1.5 text-xs font-medium {{ $selectedCategory === null ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}"
                >
                    All
                </button>
                @foreach ($this->categories as $category)
                    <button
                        wire:click="selectCategory('{{ $category }}')"
                        class="rounded-full px-3 py-1.5 text-xs font-medium capitalize {{ $selectedCategory === $category ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}"
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
                        class="flex flex-col items-start rounded-md border border-slate-200 p-3 text-left hover:border-indigo-400 hover:bg-indigo-50 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        <span class="text-sm font-medium text-slate-900">{{ $product->name }}</span>
                        <span class="text-xs text-slate-500">${{ $product->price }}</span>
                        <span
                            wire:key="pos-stock-{{ $product->id }}-{{ $product->stock_quantity }}"
                            class="mt-1 text-[11px] {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-500' }}"
                        >
                            {{ $product->stock_quantity > 0 ? $product->stock_quantity.' in stock' : 'Out of stock' }}
                        </span>
                    </button>
                @empty
                    <p class="col-span-3 py-10 text-center text-sm text-slate-400">No products in this category.</p>
                @endforelse
            </div>
        </div>

        <div class="col-span-2 flex flex-col rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 p-3">
                <h2 class="text-sm font-semibold text-slate-900">Current sale</h2>
            </div>

            @if ($lastCompletedOrderNumber)
                <div class="m-3 rounded-md bg-green-50 p-4 text-center">
                    <p class="text-sm font-semibold text-green-800">Sale complete</p>
                    <p class="mt-1 font-mono text-xs text-green-700">{{ $lastCompletedOrderNumber }}</p>
                    <button wire:click="clearSale" class="mt-3 rounded-md bg-slate-900 px-3 py-1.5 text-xs font-medium text-white">
                        Start next sale
                    </button>
                </div>
            @else
                <div class="flex-1 overflow-y-auto p-3">
                    @forelse ($this->cartItems as $item)
                        <div
                            wire:key="cart-{{ $item->product->id }}"
                            wire:click="setActiveLine({{ $item->product->id }})"
                            class="mb-2 flex cursor-pointer items-center justify-between rounded-md p-2 text-sm {{ $activeLineProductId === $item->product->id ? 'bg-indigo-50 ring-1 ring-indigo-300' : 'hover:bg-slate-50' }}"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-slate-900">{{ $item->product->name }}</p>
                                <p class="text-xs text-slate-400">{{ $item->quantity }} &times; ${{ $item->product->price }}</p>
                            </div>
                            <span class="font-mono text-sm text-slate-900">${{ number_format($item->subtotal_cents / 100, 2) }}</span>
                            <button
                                wire:click.stop="removeFromCart({{ $item->product->id }})"
                                class="ml-2 text-slate-300 hover:text-red-600"
                            >&times;</button>
                        </div>
                    @empty
                        <p class="py-10 text-center text-sm text-slate-400">Tap a product to start a sale.</p>
                    @endforelse
                </div>

                <div class="border-t border-slate-200 p-3">
                    <div class="mb-2 flex items-center justify-between text-xs text-slate-500">
                        <span>
                            @if ($activeLineProductId)
                                Editing qty for line
                            @else
                                Tap a line to edit quantity
                            @endif
                        </span>
                        <span class="font-mono text-sm text-slate-900" x-text="keypadBuffer || '&mdash;'"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ([1,2,3,4,5,6,7,8,9] as $digit)
                            <button
                                @click="pressKey('{{ $digit }}')"
                                :disabled="{{ $activeLineProductId ? 'false' : 'true' }}"
                                class="rounded-md bg-slate-100 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 disabled:opacity-40"
                            >{{ $digit }}</button>
                        @endforeach
                        <button @click="clearKeypad()" class="rounded-md bg-slate-100 py-2 text-sm font-medium text-slate-500 hover:bg-slate-200">C</button>
                        <button
                            @click="pressKey('0')"
                            :disabled="{{ $activeLineProductId ? 'false' : 'true' }}"
                            class="rounded-md bg-slate-100 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 disabled:opacity-40"
                        >0</button>
                        <button
                            @click="confirmKeypad()"
                            :disabled="{{ $activeLineProductId ? 'false' : 'true' }}"
                            class="rounded-md bg-indigo-600 py-2 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-40"
                        >Enter</button>
                    </div>
                </div>

                <div class="border-t border-slate-200 p-3">
                    <div class="mb-3 flex items-center justify-between text-sm font-semibold text-slate-900">
                        <span>Total</span>
                        <span>${{ number_format($this->totalCents / 100, 2) }}</span>
                    </div>
                    <button
                        wire:click="payAndComplete"
                        wire:loading.attr="disabled"
                        class="w-full rounded-md bg-green-600 px-4 py-3 text-sm font-semibold text-white hover:bg-green-500 disabled:opacity-60"
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
        class="fixed inset-0 z-40 flex items-start justify-center bg-black/30 pt-24"
        style="display: none;"
    >
        <div @click.outside="lookupOpen = false" class="w-full max-w-md rounded-lg bg-white p-4 shadow-xl">
            <input
                type="text"
                wire:model.live.debounce.300ms="lookupSearch"
                placeholder="Search by name or SKU..."
                class="w-full rounded-md border-slate-300 text-sm"
                x-ref="lookupInput"
                x-init="$watch('lookupOpen', value => value && setTimeout(() => $refs.lookupInput.focus(), 50))"
            >
            <div class="mt-3 max-h-72 space-y-1 overflow-y-auto">
                @foreach ($this->lookupResults as $product)
                    <button
                        wire:click="addToCart({{ $product->id }}); $wire.lookupSearch = ''"
                        @click="lookupOpen = false"
                        class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm hover:bg-slate-50"
                    >
                        <span>{{ $product->name }}</span>
                        <span class="font-mono text-xs text-slate-400">{{ $product->sku }}</span>
                    </button>
                @endforeach
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
