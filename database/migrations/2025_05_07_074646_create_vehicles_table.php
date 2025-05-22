<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone', 15);
            $table->enum('type', ['motor', 'sepeda']);
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->year('year');
            $table->string('license_plate', 20)->unique();
            $table->string('color', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};