<div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <p class="mb-1 font-mono text-xs uppercase tracking-widest text-amber-dark">Current inventory — live</p>
        <h1 class="mb-8 font-display text-4xl font-bold uppercase tracking-tight text-ink">Shop</h1>

        @error('cart')
            <div class="mb-4 border border-stamp/30 bg-white px-4 py-3 font-mono text-xs text-stamp">{{ $message }}</div>
        @enderror

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <div wire:key="product-{{ $product->id }}" class="group relative flex flex-col border border-ink/10 bg-white p-4 transition hover:border-ink/25">
                    <div class="mb-3">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-square w-full rounded-sm object-cover">
                        @else
                            <div class="flex aspect-square w-full items-center justify-center rounded-sm bg-steel font-mono text-[10px] uppercase tracking-wide text-ink/30">No image</div>
                        @endif
                    </div>

                    <div wire:key="status-{{ $product->id }}-{{ $product->stock_quantity }}" class="mb-2">
                        <x-stock-tag :quantity="$product->stock_quantity" />
                    </div>

                    <h2 class="font-semibold text-ink">{{ $product->name }}</h2>
                    <p class="mb-4 font-mono text-sm text-ink/50">${{ $product->price }}</p>

                    <button
                        wire:click="addToCart({{ $product->id }})"
                        @disabled($product->stock_quantity <= 0)
                        class="mt-auto rounded-sm bg-ink px-3 py-2 font-mono text-xs uppercase tracking-wide text-white transition hover:bg-amber-dark disabled:cursor-not-allowed disabled:bg-ink/20"
                    >
                        {{ $product->stock_quantity > 0 ? 'Add to cart' : 'Out of stock' }}
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>

    <div>
        <div class="sticky top-6 border border-ink/10 bg-white">
            <div class="border-t-2 border-dashed border-ink/15"></div>

            <div class="p-5">
                <p class="mb-4 font-mono text-xs uppercase tracking-widest text-ink/40">Your cart</p>

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
                                            class="w-12 rounded-sm border-ink/15 py-0.5 text-center font-mono text-xs"
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

                    <a href="{{ route('storefront.checkout') }}" wire:navigate class="mt-4 block rounded-sm bg-ink px-4 py-2.5 text-center font-mono text-xs uppercase tracking-wide text-white hover:bg-amber-dark">Checkout</a>
                @endif
            </div>
        </div>
    </div>
</div>
