<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Livewire\Admin\ProductManager;
Route::livewire('/admin/products', ProductManager::class)->name('admin.products');