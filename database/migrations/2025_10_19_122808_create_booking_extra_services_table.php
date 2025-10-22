<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_extra_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('extra_service_id')->constrained()->cascadeOnDelete();

            // Store service details at time of booking (price might change later)
            $table->json('service_name'); // Snapshot
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 10, 2);

            $table->timestamps();

            $table->index(['booking_id', 'extra_service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_extra_services');
    }
};
