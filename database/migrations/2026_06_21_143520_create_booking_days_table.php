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
        Schema::create('booking_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('time_slot'); // morning, afternoon, evening, full_day
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->index('booking_id');
            $table->unique(['hall_id', 'date', 'time_slot'], 'unique_booking_day_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_days');
    }
};
