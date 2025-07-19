<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('mechanic_id')->constrained('users');
            $table->string('work_order_number', 20)->unique();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('diagnosis')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};