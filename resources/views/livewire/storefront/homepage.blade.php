<div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <span class="mb-3 inline-flex -rotate-2 items-center gap-2 rounded-sm border border-ledger/40 bg-white px-2.5 py-1 font-mono text-[11px] font-medium uppercase tracking-widest text-ledger">
            <span class="h-1.5 w-1.5 animate-pulse rounded-full border border-current"></span>
            Current inventory — live
        </span>
        <h1 class="mb-8 font-display text-4xl font-bold uppercase tracking-tight text-ink">Shop</h1>

        @error('cart')
            <div class="mb-4 border border-stamp/30 bg-white px-4 py-3 font-mono text-xs text-stamp shadow-sm">{{ $message }}</div>
        @enderror

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <div wire:key="product-{{ $product->id }}" class="group relative flex flex-col overflow-hidden border border-ink/10 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-ink/25 hover:shadow-lg hover:shadow-ink/10">
                    <div class="overflow-hidden">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-105">
                        @else
                            <div class="flex aspect-square w-full items-center justify-center bg-steel font-mono text-[10px] uppercase tracking-wide text-ink/30">No image</div>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <div wire:key="status-{{ $product->id }}-{{ $product->stock_quantity }}" class="mb-2 self-start">
                            <x-stock-tag :quantity="$product->stock_quantity" />
                        </div>

                        <h2 class="font-semibold text-ink">{{ $product->name }}</h2>
                        <p class="mb-4 font-mono text-sm text-ink/50">${{ $product->price }}</p>

                        <button
                            wire:click="addToCart({{ $product->id }})"
                            @disabled($product->stock_quantity <= 0)
                            class="mt-auto rounded-sm bg-ink px-3 py-2.5 font-mono text-xs uppercase tracking-widest text-white shadow-sm transition hover:bg-amber-dark disabled:cursor-not-allowed disabled:bg-ink/20 disabled:shadow-none"
                        >
                            {{ $product->stock_quantity > 0 ? 'Add to cart' : 'Out of stock' }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>

    <div>
        <div class="sticky top-6 border border-ink/10 bg-white shadow-lg shadow-ink/5">
            <div class="border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
                <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">Your cart</span>
            </div>

            <div class="p-5">
                @if ($this->cartItems->isEmpty())
                    <p class="font-mono text-sm text-ink/30">— empty —</p>
                @else
                    <div class="space-y-3">
                        @foreach ($this->cartItems as $item)
                            <div wire:key="cart-item-{{ $item->product->id }}" class="text-sm">
                                <div class="flex items-baseline gap-1">
                                    <span class="truncate font-medium text-ink">{{ $item->product->name }}</span>
                                    <span class="flex-1 border-b border-dotted border-ink/25"></span>
                                    <span class="font-mono text-ink">${{ number_format($item->subtotal_cents / 100, 2) }}</span>
                                </div>
                                <div class="mt-1 flex items-center justify-between font-mono text-xs text-ink/40">
                                    <span>{{ $item->quantity }} &times; ${{ $item->product->price }}</span>
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="number"
                                            min="0"
                                            max="{{ $item->product->stock_quantity }}"
                                            value="{{ $item->quantity }}"
                                            wire:change="updateCartQuantity({{ $item->product->id }}, $event.target.value)"
                                            class="w-12 rounded-sm border-ink/15 py-0.5 text-center font-mono text-xs transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20"
                                        >
                                        <button wire:click="removeFromCart({{ $item->product->id }})" class="text-ink/30 hover:text-stamp">&times;</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t-2 border-dashed border-ink/15 pt-4 font-mono text-sm font-semibold text-ink">
                        <span class="uppercase tracking-wide">Total</span>
                        <span>${{ number_format($this->cartTotalCents / 100, 2) }}</span>
                    </div>

                    <a href="{{ route('storefront.checkout') }}" wire:navigate class="mt-4 block rounded-sm bg-ink px-4 py-2.5 text-center font-mono text-xs uppercase tracking-widest text-white shadow-sm transition hover:bg-amber-dark">Checkout</a>
                @endif
            </div>
        </div>
    </div>
</div>
