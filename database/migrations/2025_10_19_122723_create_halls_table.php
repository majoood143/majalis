<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            // Basic Info
            $table->json('name'); // {"en": "Grand Palace Hall", "ar": "قصر الكبرى"}
            $table->string('slug')->unique();
            $table->json('description');

            // Location
            $table->text('address');
            $table->json('address_localized')->nullable(); // {"en": "...", "ar": "..."}
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('google_maps_url')->nullable();

            // Capacity
            $table->integer('capacity_min')->default(0);
            $table->integer('capacity_max');

            // Pricing
            $table->decimal('price_per_slot', 10, 2); // Base price per time slot
            $table->json('pricing_override')->nullable(); // Different prices per slot
            // Example: {"morning": 100, "afternoon": 150, "evening": 200, "full_day": 400}

            // Contact
            $table->string('phone', 20);
            $table->string('whatsapp', 20)->nullable();
            $table->string('email')->nullable();

            // Media
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable(); // Array of image paths
            $table->string('video_url')->nullable();
            $table->json('virtual_tour_url')->nullable();

            // Features (stored as JSON array of feature IDs)
            $table->json('features')->nullable(); // [1, 2, 3, 4]

            // Settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_approval')->default(false);
            $table->integer('cancellation_hours')->default(24); // Hours before booking
            $table->decimal('cancellation_fee_percentage', 5, 2)->default(0);

            // Stats
            $table->integer('total_bookings')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);

            // SEO
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['city_id', 'is_active']);
            $table->index('owner_id');
            $table->index('slug');
            $table->index('is_featured');
            $table->index('average_rating');
            $table->index(['capacity_min', 'capacity_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halls');
    }
};
