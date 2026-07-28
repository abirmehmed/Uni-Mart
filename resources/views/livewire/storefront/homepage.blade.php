<div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
    {{-- Product grid --}}
    <div class="lg:col-span-2">
        <h1 class="mb-6 text-2xl font-semibold text-slate-900">Shop</h1>

        @error('cart')
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <div wire:key="product-{{ $product->id }}" class="flex flex-col rounded-lg border border-slate-200 bg-white p-4">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mb-3 aspect-square w-full rounded-md object-cover">
                    @else
                        <div class="mb-3 flex aspect-square w-full items-center justify-center rounded-md bg-slate-100 text-xs text-slate-400">No image</div>
                    @endif

                    <h2 class="font-medium text-slate-900">{{ $product->name }}</h2>
                    <p class="mb-2 text-sm text-slate-500">${{ $product->price }}</p>

                    {{-- wire:key tied to stock_quantity so this badge visibly
                         refreshes the instant a broadcast changes the number --}}
                    <div wire:key="status-{{ $product->id }}-{{ $product->stock_quantity }}" class="mb-3">
                        @if ($product->stock_quantity > 0)
                            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">In Stock ({{ $product->stock_quantity }})</span>
                        @else
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700">Out of Stock</span>
                        @endif
                    </div>

                    <button
                        wire:click="addToCart({{ $product->id }})"
                        @disabled($product->stock_quantity <= 0)
                        class="mt-auto rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                    >
                        {{ $product->stock_quantity > 0 ? 'Add to cart' : 'Out of stock' }}
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

    {{-- Cart sidebar --}}
    <div>
        <div class="sticky top-6 rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="mb-4 font-semibold text-slate-900">Your cart</h2>

            @if ($this->cartItems->isEmpty())
                <p class="text-sm text-slate-400">Cart is empty.</p>
            @else
                <div class="space-y-3">
                    @foreach ($this->cartItems as $item)
                        <div wire:key="cart-item-{{ $item->product->id }}" class="flex items-center justify-between gap-2 text-sm">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-slate-900">{{ $item->product->name }}</p>
                                <p class="text-slate-400">${{ $item->product->price }} each</p>
                            </div>
                            <input
                                type="number"
                                min="0"
                                max="{{ $item->product->stock_quantity }}"
                                value="{{ $item->quantity }}"
                                wire:change="updateCartQuantity({{ $item->product->id }}, $event.target.value)"
                                class="w-16 rounded-md border-slate-300 text-center text-sm"
                            >
                            <button wire:click="removeFromCart({{ $item->product->id }})" class="text-slate-400 hover:text-red-600">&times;</button>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 text-sm font-semibold text-slate-900">
                    <span>Total</span>
                    <span>${{ number_format($this->cartTotalCents / 100, 2) }}</span>
                </div>

                <a
                    href="{{ route('storefront.checkout') }}"
                    wire:navigate
                    class="mt-4 block rounded-md bg-indigo-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-indigo-500"
                >
                    Checkout
                </a>
            @endif
        </div>
    </div>
</div>
