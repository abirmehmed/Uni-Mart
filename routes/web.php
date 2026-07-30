<?php

use App\Livewire\Admin\ProductManager;
use App\Livewire\Pos\Terminal;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Homepage;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Homepage::class)->name('storefront.home');
Route::livewire('/checkout', Checkout::class)->name('storefront.checkout');

Route::livewire('/admin/products', ProductManager::class)->name('admin.products');

Route::livewire('/pos', Terminal::class)->name('pos.terminal');
