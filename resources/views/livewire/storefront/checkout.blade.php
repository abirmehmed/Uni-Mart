<div class="mx-auto max-w-2xl">
    @if ($placedOrderNumber)
        {{-- Confirmation state --}}
        <div class="rounded-lg border border-green-200 bg-green-50 p-8 text-center">
            <h1 class="mb-2 text-xl font-semibold text-green-800">Order placed!</h1>
            <p class="text-sm text-green-700">
                Order <span class="font-mono font-semibold">{{ $placedOrderNumber }}</span> is confirmed.
                A receipt is on its way to your email.
            </p>
            <a href="{{ route('storefront.home') }}" wire:navigate class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">
                &larr; Continue shopping
            </a>
        </div>
    @else
        <h1 class="mb-6 text-2xl font-semibold text-slate-900">Checkout</h1>

        @error('cart')
            <div class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        @if ($this->cartItems->isEmpty())
            <p class="text-sm text-slate-500">
                Your cart is empty.
                <a href="{{ route('storefront.home') }}" wire:navigate class="text-indigo-600 hover:text-indigo-500">Go shopping &rarr;</a>
            </p>
        @else
            {{-- Order summary --}}
            <div class="mb-6 rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Order summary</h2>
                <div class="space-y-2 text-sm">
                    @foreach ($this->cartItems as $item)
                        <div class="flex justify-between text-slate-600">
                            <span>{{ $item->product->name }} &times; {{ $item->quantity }}</span>
                            <span>${{ number_format($item->subtotal_cents / 100, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 flex justify-between border-t border-slate-100 pt-3 text-sm font-semibold text-slate-900">
                    <span>Total</span>
                    <span>${{ number_format($this->cartTotalCents / 100, 2) }}</span>
                </div>
            </div>

            {{-- Customer form --}}
            <form wire:submit="placeOrder" class="space-y-4 rounded-lg border border-slate-200 bg-white p-5">
                <div>
                    <label class="block text-xs font-medium text-slate-500">Full name</label>
                    <input type="text" wire:model="customer_name" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                    @error('customer_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">Email</label>
                    <input type="email" wire:model="customer_email" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                    @error('customer_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">Shipping address</label>
                    <textarea wire:model="customer_address" rows="3" class="mt-1 w-full rounded-md border-slate-300 text-sm"></textarea>
                    @error('customer_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-500 disabled:opacity-60"
                >
                    <span wire:loading.remove>Place order</span>
                    <span wire:loading>Placing order...</span>
                </button>
            </form>
        @endif
    @endif
</div>
