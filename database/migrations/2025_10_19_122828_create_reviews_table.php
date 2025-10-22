<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Rating (1-5 stars)
            $table->tinyInteger('rating')->unsigned();

            // Review Content
            $table->text('comment')->nullable();
            $table->json('photos')->nullable(); // Array of uploaded images

            // Detailed Ratings (optional)
            $table->tinyInteger('cleanliness_rating')->nullable();
            $table->tinyInteger('service_rating')->nullable();
            $table->tinyInteger('value_rating')->nullable();
            $table->tinyInteger('location_rating')->nullable();

            // Moderation
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->text('admin_notes')->nullable();

            // Response from owner
            $table->text('owner_response')->nullable();
            $table->timestamp('owner_response_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['hall_id', 'is_approved']);
            $table->index(['user_id', 'booking_id']);
            $table->index('rating');
            $table->index('is_featured');
            $table->unique(['booking_id', 'user_id']); // One review per booking per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
