<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained();
            $table->foreignId('work_order_id')->nullable()->constrained();
            $table->integer('quantity');
            $table->enum('transaction_type', ['purchase', 'sales', 'adjustment', 'return'])->default('purchase');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};