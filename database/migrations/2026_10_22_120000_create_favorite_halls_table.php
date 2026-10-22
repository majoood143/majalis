<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Halls a customer saved in the mobile app ("Saved" tab).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_halls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'hall_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_halls');
    }
};
