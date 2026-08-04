<div>
    <livewire:admin.daily-sales-summary />

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Products</h1>
            <p class="text-sm text-slate-500">{{ $products->total() }} total &middot; edits push live to storefront and POS instantly</p>
        </div>
        <button
            wire:click="$toggle('showCreateForm')"
            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
        >
            {{ $showCreateForm ? 'Cancel' : '+ Add product' }}
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800" wire:key="flash-{{ now()->timestamp }}">
            {{ session('success') }}
        </div>
    @endif

    @if ($showCreateForm)
        <form wire:submit="create" class="mb-6 grid grid-cols-1 gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-6">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500">Name</label>
                <input type="text" wire:model="createForm.name" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                @error('createForm.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">SKU</label>
                <input type="text" wire:model="createForm.sku" class="mt-1 w-full rounded-md border-slate-300 font-mono text-sm">
                @error('createForm.sku') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Category</label>
                <input type="text" wire:model="createForm.category" class="mt-1 w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Price ($)</label>
                <input type="number" step="0.01" min="0" wire:model="createForm.price" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                @error('createForm.price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Stock</label>
                <input type="number" min="0" wire:model="createForm.stock_quantity" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                @error('createForm.stock_quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-6">
                <label class="block text-xs font-medium text-slate-500">Image URL (optional)</label>
                <input type="text" wire:model="createForm.image_url" class="mt-1 w-full rounded-md border-slate-300 text-sm">
            </div>
            <div class="sm:col-span-6">
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                    Save product
                </button>
            </div>
        </form>
    @endif

    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.400ms="search"
            placeholder="Search by name, SKU, or category..."
            class="w-full max-w-sm rounded-md border-slate-300 text-sm"
        >
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-slate-500">Name</th>
                    <th class="px-4 py-2 text-left font-medium text-slate-500">SKU</th>
                    <th class="px-4 py-2 text-left font-medium text-slate-500">Category</th>
                    <th class="px-4 py-2 text-right font-medium text-slate-500">Price</th>
                    <th class="px-4 py-2 text-right font-medium text-slate-500">Stock</th>
                    <th class="px-4 py-2 text-left font-medium text-slate-500">Status</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    <tr wire:key="product-{{ $product->id }}" class="{{ $product->trashed() ? 'bg-slate-50 opacity-60' : '' }}">
                        @if ($editingId === $product->id)
                            <td class="px-4 py-2">
                                <input type="text" wire:model="editForm.name" class="w-full rounded-md border-slate-300 text-sm">
                                @error('editForm.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2 font-mono text-slate-400">{{ $product->sku }}</td>
                            <td class="px-4 py-2">
                                <input type="text" wire:model="editForm.category" class="w-full rounded-md border-slate-300 text-sm">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0" wire:model="editForm.price" class="w-24 rounded-md border-slate-300 text-right text-sm">
                                @error('editForm.price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" min="0" wire:model="editForm.stock_quantity" class="w-20 rounded-md border-slate-300 text-right text-sm">
                                @error('editForm.stock_quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2 text-slate-400">—</td>
                            <td class="whitespace-nowrap px-4 py-2 text-right">
                                <button wire:click="saveEdit" class="mr-2 font-medium text-indigo-600 hover:text-indigo-500">Save</button>
                                <button wire:click="cancelEdit" class="text-slate-400 hover:text-slate-600">Cancel</button>
                            </td>
                        @else
                            <td class="px-4 py-2 font-medium text-slate-900">{{ $product->name }}</td>
                            <td class="px-4 py-2 font-mono text-xs text-slate-500">{{ $product->sku }}</td>
                            <td class="px-4 py-2 text-slate-600">{{ $product->category ?? '—' }}</td>
                            <td class="px-4 py-2 text-right text-slate-900">${{ $product->price }}</td>
                            <td class="px-4 py-2 text-right font-mono text-slate-900" wire:key="stock-{{ $product->id }}-{{ $product->stock_quantity }}">
                                {{ $product->stock_quantity }}
                            </td>
                            <td class="px-4 py-2">
                                @if ($product->trashed())
                                    <span class="rounded-full bg-slate-200 px-2 py-0.5 text-xs text-slate-600">Deactivated</span>
                                @elseif ($product->stock_quantity > 0)
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">In Stock</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700">Out of Stock</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-2 text-right">
                                @if ($product->trashed())
                                    <button wire:click="restore({{ $product->id }})" class="font-medium text-indigo-600 hover:text-indigo-500">Restore</button>
                                @else
                                    <button wire:click="startEdit({{ $product->id }})" class="mr-3 font-medium text-indigo-600 hover:text-indigo-500">Edit</button>
                                    <button
                                        wire:click="delete({{ $product->id }})"
                                        wire:confirm="Deactivate {{ $product->name }}? Historical orders are kept."
                                        class="text-slate-400 hover:text-red-600"
                                    >Deactivate</button>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400">No products match "{{ $search }}".</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
