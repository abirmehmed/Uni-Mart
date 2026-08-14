<div>
    <livewire:admin.daily-sales-summary />

    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="mb-1 font-mono text-xs uppercase tracking-widest text-amber-dark">Ledger</p>
            <h1 class="font-display text-3xl font-bold uppercase tracking-tight text-ink">Products</h1>
            <p class="font-mono text-xs text-ink/40">{{ $products->total() }} total &middot; edits push live to storefront and POS instantly</p>
        </div>
        <button
            wire:click="$toggle('showCreateForm')"
            class="rounded-sm bg-ink px-4 py-2.5 font-mono text-xs uppercase tracking-widest text-white shadow-sm transition-colors hover:bg-amber-dark"
        >
            {{ $showCreateForm ? 'Cancel' : '+ Add product' }}
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 border border-ledger/30 bg-white px-4 py-3 font-mono text-xs text-ledger shadow-sm" wire:key="flash-{{ now()->timestamp }}">
            {{ session('success') }}
        </div>
    @endif

    @if ($showCreateForm)
        <div class="mb-6 border border-ink/10 bg-white shadow-lg shadow-ink/5">
            <div class="border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
                <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">New product</span>
            </div>
            <form wire:submit="create" class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-6">
                <div class="sm:col-span-2">
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Name</label>
                    <input type="text" wire:model="createForm.name" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                    @error('createForm.name') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">SKU</label>
                    <input type="text" wire:model="createForm.sku" class="mt-1.5 w-full rounded-sm border-ink/15 font-mono text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                    @error('createForm.sku') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Category</label>
                    <input type="text" wire:model="createForm.category" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                </div>
                <div>
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Price ($)</label>
                    <input type="number" step="0.01" min="0" wire:model="createForm.price" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                    @error('createForm.price') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Cost ($)</label>
                    <input type="number" step="0.01" min="0" wire:model="createForm.cost" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                    @error('createForm.cost') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Stock</label>
                    <input type="number" min="0" wire:model="createForm.stock_quantity" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                    @error('createForm.stock_quantity') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-6">
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Description (optional)</label>
                    <textarea wire:model="createForm.description" rows="3" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20"></textarea>
                    @error('createForm.description') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-6">
                    <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Image URL (optional)</label>
                    <input type="text" wire:model="createForm.image_url" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                </div>
                <div class="sm:col-span-6">
                    <button type="submit" class="rounded-sm bg-ink px-4 py-2.5 font-mono text-xs uppercase tracking-widest text-white shadow-sm transition-colors hover:bg-amber-dark">
                        Save product
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.400ms="search"
            placeholder="Search by name, SKU, or category..."
            class="w-full max-w-sm rounded-sm border-ink/15 font-mono text-sm shadow-sm placeholder:text-ink/30 transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20"
        >
    </div>

    <div class="border border-ink/10 bg-white shadow-lg shadow-ink/5">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-ink/10 bg-steel/60">
                    <th class="px-4 py-3 text-left font-mono text-[11px] uppercase tracking-widest text-ink/40">Name</th>
                    <th class="px-4 py-3 text-left font-mono text-[11px] uppercase tracking-widest text-ink/40">SKU</th>
                    <th class="px-4 py-3 text-left font-mono text-[11px] uppercase tracking-widest text-ink/40">Category</th>
                    <th class="px-4 py-3 text-right font-mono text-[11px] uppercase tracking-widest text-ink/40">Price</th>
                    <th class="px-4 py-3 text-right font-mono text-[11px] uppercase tracking-widest text-ink/40">Cost</th>
                    <th class="px-4 py-3 text-right font-mono text-[11px] uppercase tracking-widest text-ink/40">Stock</th>
                    <th class="px-4 py-3 text-left font-mono text-[11px] uppercase tracking-widest text-ink/40">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="product-{{ $product->id }}" class="border-b border-ink/[0.06] transition hover:bg-amber/10 {{ $product->trashed() ? 'bg-ink/[0.02] opacity-60' : '' }}">
                        @if ($editingId === $product->id)
                            <td class="px-4 py-3">
                                <input type="text" wire:model="editForm.name" class="w-full rounded-sm border-ink/15 text-sm focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                                @error('editForm.name') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-3 font-mono text-ink/30">{{ $product->sku }}</td>
                            <td class="px-4 py-3">
                                <input type="text" wire:model="editForm.category" class="w-full rounded-sm border-ink/15 text-sm focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0" wire:model="editForm.price" class="w-24 rounded-sm border-ink/15 text-right font-mono text-sm focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                                @error('editForm.price') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0" wire:model="editForm.cost" class="w-24 rounded-sm border-ink/15 text-right font-mono text-sm focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                                @error('editForm.cost') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" min="0" wire:model="editForm.stock_quantity" class="w-20 rounded-sm border-ink/15 text-right font-mono text-sm focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20">
                                @error('editForm.stock_quantity') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-3 font-mono text-ink/30">&mdash;</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-xs uppercase tracking-wide">
                                <button wire:click="saveEdit" class="mr-3 text-amber-dark hover:text-ink">Save</button>
                                <button wire:click="cancelEdit" class="text-ink/30 hover:text-ink">Cancel</button>
                            </td>
                        @else
                            <td class="px-4 py-3 font-medium text-ink">{{ $product->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-ink/40">{{ $product->sku }}</td>
                            <td class="px-4 py-3 text-ink/60">{{ $product->category ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono text-ink">${{ $product->price }}</td>
                            <td class="px-4 py-3 text-right font-mono text-ink/50">${{ $product->cost }}</td>
                            <td class="px-4 py-3 text-right font-mono text-ink" wire:key="stock-{{ $product->id }}-{{ $product->stock_quantity }}">
                                {{ $product->stock_quantity }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($product->trashed())
                                    <span class="font-mono text-[11px] uppercase tracking-wide text-ink/30">Deactivated</span>
                                @else
                                    <x-stock-tag :quantity="$product->stock_quantity" />
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-xs uppercase tracking-wide">
                                @if ($product->trashed())
                                    <button wire:click="restore({{ $product->id }})" class="text-amber-dark hover:text-ink">Restore</button>
                                @else
                                    <button wire:click="startEdit({{ $product->id }})" class="mr-3 text-amber-dark hover:text-ink">Edit</button>
                                    <button
                                        wire:click="delete({{ $product->id }})"
                                        wire:confirm="Deactivate {{ $product->name }}? Historical orders are kept."
                                        class="text-ink/30 hover:text-stamp"
                                    >Deactivate</button>
                                @endif
                            </td>
                        @endif
                    </tr>
                    @if ($editingId === $product->id)
                        <tr wire:key="desc-{{ $product->id }}" class="border-b border-ink/[0.06] bg-steel/40">
                            <td colspan="7" class="px-4 py-3">
                                <label class="font-mono text-[11px] uppercase tracking-wide text-ink/40">Description</label>
                                <textarea wire:model="editForm.description" rows="2" class="mt-1.5 w-full rounded-sm border-ink/15 text-sm transition-colors focus:border-amber focus:outline-none focus:ring-2 focus:ring-amber/20"></textarea>
                                @error('editForm.description') <p class="mt-1 font-mono text-xs text-stamp">{{ $message }}</p> @enderror
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center font-mono text-sm text-ink/30">No products match "{{ $search }}".</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
