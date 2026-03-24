<?php
// database/migrations/2024_01_01_000000_add_function_hours_to_halls_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('halls', function (Blueprint $table) {
            // Option 1: Simple JSON column for all hours
            $table->json('function_hours')->nullable();
            
            // Option 2: Individual columns per day (alternative)
            // $table->json('monday_hours')->nullable();
            // $table->json('tuesday_hours')->nullable();
            // $table->json('wednesday_hours')->nullable();
            // $table->json('thursday_hours')->nullable();
            // $table->json('friday_hours')->nullable();
            // $table->json('saturday_hours')->nullable();
            // $table->json('sunday_hours')->nullable();
            
            // Additional fields
            $table->boolean('is_24_hours')->default(false);
            $table->text('special_hours_note')->nullable(); // For holidays, special events
        });
    }

    public function down(): void
    {
        Schema::table('halls', function (Blueprint $table) {
            $table->dropColumn([
                'function_hours',
                'is_24_hours',
                'special_hours_note'
            ]);
        });
    }
};