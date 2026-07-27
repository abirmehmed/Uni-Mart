<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // restrict, not cascade: never let a product deletion silently wipe historical sales rows
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('price_at_time_cents'); // snapshot — price changes later must not rewrite history
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
