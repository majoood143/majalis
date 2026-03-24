<?php

// Migration for hall_types table
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hall_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // 'conference', 'banquet', etc.
            $table->json('name'); // Translatable name
            $table->json('description')->nullable(); // Translatable description
            $table->string('icon')->nullable(); // FontAwesome or Heroicon name
            $table->string('color')->nullable(); // For UI badges
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for many-to-many relationship
        Schema::create('hall_hall_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->onDelete('cascade');
            $table->foreignId('hall_type_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Optional: Categories table if you want to group types
        Schema::create('hall_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add category_id to hall_types if using categories
        Schema::table('hall_types', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('hall_categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hall_hall_type');
        Schema::dropIfExists('hall_types');
        Schema::dropIfExists('hall_categories');
    }
};
