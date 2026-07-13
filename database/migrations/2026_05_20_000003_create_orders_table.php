<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kurir_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('delivery_method')->default('pickup'); // self_drop, pickup
            $table->text('pickup_address')->nullable();
            $table->dateTime('pickup_datetime')->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->dateTime('estimated_finish')->nullable();
            $table->string('status')->default('waiting'); // waiting, confirmed, picked_up, washing, done, ready, delivered
            $table->text('customer_notes')->nullable();
            $table->text('kurir_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
