<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->string('time_slot'); // morning, afternoon, evening, full_day

            $table->boolean('is_available')->default(true);
            $table->string('reason')->nullable(); // 'maintenance', 'blocked', 'custom'
            $table->text('notes')->nullable();

            // Optional: Custom pricing for specific dates
            $table->decimal('custom_price', 10, 2)->nullable();

            $table->timestamps();

            // Indexes
            $table->unique(['hall_id', 'date', 'time_slot']);
            $table->index(['hall_id', 'date', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_availability');
    }
};
