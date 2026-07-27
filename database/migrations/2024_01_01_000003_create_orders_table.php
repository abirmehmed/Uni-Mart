<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g. UM-20260727-0001
            $table->string('customer_name')->nullable(); // nullable: POS walk-in sales may skip this
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->unsignedInteger('total_price_cents');
            $table->enum('source', ['online', 'pos'])->index();
            $table->enum('status', ['pending', 'paid', 'fulfilled', 'cancelled'])->default('pending');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete(); // set when source = pos
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
