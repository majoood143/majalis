<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();

            // Image Details
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->json('title')->nullable(); // {"en": "Main Hall", "ar": "القاعة الرئيسية"}
            $table->json('caption')->nullable();
            $table->string('alt_text')->nullable();

            // Image Type
            $table->string('type')->default('gallery'); // gallery, featured, floor_plan, 360_view

            // Metadata
            $table->integer('file_size')->nullable(); // in bytes
            $table->string('mime_type')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            // Display Settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['hall_id', 'is_active', 'order']);
            $table->index(['hall_id', 'type']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_images');
    }
};
