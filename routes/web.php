<?php

use App\Livewire\Admin\ProductManager;
use App\Livewire\Auth\Login;
use App\Livewire\Pos\Terminal;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Homepage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Homepage::class)->name('storefront.home');
Route::livewire('/checkout', Checkout::class)->name('storefront.checkout');

Route::livewire('/login', Login::class)->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::livewire('/admin/products', ProductManager::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.products');

Route::livewire('/pos', Terminal::class)
    ->middleware(['auth', 'role:admin,cashier'])
    ->name('pos.terminal');
