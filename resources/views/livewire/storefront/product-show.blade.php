<div class="mx-auto max-w-5xl">
    <a href="{{ route('storefront.home') }}" wire:navigate class="mb-6 inline-flex items-center gap-1 font-mono text-xs uppercase tracking-wide text-ink/50 hover:text-ink">
        &larr; Back to shop
    </a>

    @error('cart')
        <div class="mb-4 border border-stamp/30 bg-white px-4 py-3 font-mono text-xs text-stamp shadow-sm">{{ $message }}</div>
    @enderror

    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <div class="overflow-hidden border border-ink/10 bg-white shadow-lg shadow-ink/5">
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
            @else
                <div class="flex aspect-square w-full items-center justify-center bg-steel font-mono text-xs uppercase tracking-wide text-ink/30">No image</div>
            @endif
        </div>

        <div>
            @if ($product->category)
                <p class="mb-1 font-mono text-xs uppercase tracking-widest text-amber-dark">{{ $product->category }}</p>
            @endif
            <h1 class="mb-3 font-display text-3xl font-bold uppercase tracking-tight text-ink">{{ $product->name }}</h1>

            <div class="mb-4">
                <x-stock-tag :quantity="$product->stock_quantity" />
            </div>

            <p class="mb-6 font-display text-2xl font-bold text-ink">${{ $product->price }}</p>

            @if ($product->description)
                <p class="mb-6 whitespace-pre-line text-sm leading-relaxed text-ink/70">{{ $product->description }}</p>
            @else
                <p class="mb-6 font-mono text-xs text-ink/30">No description yet.</p>
            @endif

            <button
                wire:click="addToCart"
                @disabled($product->stock_quantity <= 0)
                class="w-full rounded-sm bg-ink px-4 py-3 font-mono text-sm uppercase tracking-widest text-white shadow-sm transition-colors hover:bg-amber-dark disabled:cursor-not-allowed disabled:bg-ink/20"
            >
                {{ $product->stock_quantity > 0 ? 'Add to cart' : 'Out of stock' }}
            </button>
        </div>
    </div>

    @if ($this->relatedProducts->isNotEmpty())
        <div class="mt-16">
            <p class="mb-4 font-mono text-xs uppercase tracking-widest text-ink/40">You might also like</p>
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">
                @foreach ($this->relatedProducts as $related)
                    <a href="{{ route('storefront.product', $related) }}" wire:navigate wire:key="related-{{ $related->id }}"
                        class="group block overflow-hidden border border-ink/10 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-ink/25 hover:shadow-md">
                        <div class="aspect-square overflow-hidden bg-steel">
                            @if ($related->image_url)
                                <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center font-mono text-[9px] uppercase text-ink/30">No image</div>
                            @endif
                        </div>
                        <div class="p-3">
                            <p class="truncate text-sm font-medium text-ink">{{ $related->name }}</p>
                            <p class="font-mono text-xs text-ink/50">${{ $related->price }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
