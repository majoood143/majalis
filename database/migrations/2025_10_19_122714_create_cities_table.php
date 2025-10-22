<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->json('name'); // {"en": "Mutrah", "ar": "مطرح"}
            $table->string('code', 10)->unique();
            $table->json('description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['region_id', 'is_active']);
            $table->index('code');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
