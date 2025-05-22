<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_service_category', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_category_id')->constrained()->onDelete('cascade');
            $table->primary(['service_id', 'service_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_service_category');
    }
};