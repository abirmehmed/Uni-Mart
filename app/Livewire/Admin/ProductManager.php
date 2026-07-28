<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProductManager extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $editingId = null;

    public array $editForm = [
        'name' => '', 'category' => '', 'price' => '', 'stock_quantity' => '',
    ];

    public bool $showCreateForm = false;

    public array $createForm = [
        'name' => '', 'sku' => '', 'category' => '', 'price' => '', 'stock_quantity' => 0, 'image_url' => '',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('echo:inventory,stock.updated')]
    public function onStockBroadcast(): void
    {
        //
    }

    public function startEdit(int $id): void
    {
        $product = Product::withTrashed()->findOrFail($id);

        $this->editingId = $id;
        $this->editForm = [
            'name' => $product->name,
            'category' => $product->category,
            'price' => $product->price,
            'stock_quantity' => $product->stock_quantity,
        ];
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editForm.name' => 'required|string|max:255',
            'editForm.category' => 'nullable|string|max:255',
            'editForm.price' => 'required|numeric|min:0',
            'editForm.stock_quantity' => 'required|integer|min:0',
        ]);

        $product = Product::withTrashed()->findOrFail($this->editingId);

        $product->update([
            'name' => $this->editForm['name'],
            'category' => $this->editForm['category'],
            'price_cents' => (int) round(((float) $this->editForm['price']) * 100),
            'stock_quantity' => $this->editForm['stock_quantity'],
        ]);

        $this->editingId = null;
        session()->flash('success', "Updated {$product->name}.");
    }

    public function create(): void
    {
        $this->validate([
            'createForm.name' => 'required|string|max:255',
            'createForm.sku' => 'required|string|max:255|unique:products,sku',
            'createForm.category' => 'nullable|string|max:255',
            'createForm.price' => 'required|numeric|min:0',
            'createForm.stock_quantity' => 'required|integer|min:0',
            'createForm.image_url' => 'nullable|url',
        ]);

        Product::create([
            'name' => $this->createForm['name'],
            'sku' => $this->createForm['sku'],
            'category' => $this->createForm['category'] ?: null,
            'price_cents' => (int) round(((float) $this->createForm['price']) * 100),
            'stock_quantity' => $this->createForm['stock_quantity'],
            'image_url' => $this->createForm['image_url'] ?: null,
        ]);

        $this->reset('createForm');
        $this->showCreateForm = false;
        session()->flash('success', 'Product created.');
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Product deactivated.');
    }

    public function restore(int $id): void
    {
        Product::withTrashed()->findOrFail($id)->restore();
        session()->flash('success', 'Product restored.');
    }

    public function render()
    {
        $products = Product::query()
            ->withTrashed()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%")
                        ->orWhere('category', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.product-manager', [
            'products' => $products,
        ]);
    }
}
