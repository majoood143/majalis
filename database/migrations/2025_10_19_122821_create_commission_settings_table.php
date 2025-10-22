<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();

            // Can be global or per hall/owner
            $table->foreignId('hall_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->string('commission_type')->default('percentage'); // percentage or fixed
            $table->decimal('commission_value', 10, 2);
            $table->json('name')->nullable(); // Optional description
            $table->json('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->timestamps();

            $table->index(['hall_id', 'is_active']);
            $table->index(['owner_id', 'is_active']);
            $table->index('effective_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
    }
};
