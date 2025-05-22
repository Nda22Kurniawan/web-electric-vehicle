<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('part_number', 100)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->enum('vehicle_type', ['motor', 'sepeda', 'keduanya'])->default('keduanya');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};