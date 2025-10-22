<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_features', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"en": "Air Conditioning", "ar": "تكييف هواء"}
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // Heroicon name or emoji
            $table->json('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('slug');
            $table->index(['is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_features');
    }
};
