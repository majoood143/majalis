<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();

            $table->json('name'); // {"en": "Catering Service", "ar": "خدمة تقديم الطعام"}
            $table->json('description');
            $table->decimal('price', 10, 2);
            $table->string('unit')->nullable(); // 'per_person', 'per_item', 'fixed'
            $table->integer('minimum_quantity')->default(1);
            $table->integer('maximum_quantity')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false); // Auto-added to booking
            $table->integer('order')->default(0);

            $table->timestamps();

            $table->index(['hall_id', 'is_active']);
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_services');
    }
};
