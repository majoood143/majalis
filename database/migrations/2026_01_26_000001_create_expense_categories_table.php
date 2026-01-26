<?php

declare(strict_types=1);

/**
 * Migration: Create Expense Categories Table
 * 
 * This migration creates the expense_categories table which allows hall owners
 * to categorize their expenses for better tracking and reporting.
 * Categories can be system-defined (global) or owner-specific.
 * 
 * @package Majalis\Database\Migrations
 * @author  Majalis Development Team
 * @version 1.0.0
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the expense_categories table with support for:
     * - Bilingual names (Arabic/English) via JSON
     * - Owner-specific or system-wide categories
     * - Color coding for visual distinction
     * - Icon support for Filament integration
     * - Soft deletes for data preservation
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table): void {
            // Primary key using unsigned big integer
            $table->id();
            
            // Optional owner relationship - null means system-wide category
            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Owner who created this category, null for system categories');
            
            // Translatable name field (supports Arabic & English)
            $table->json('name')
                ->comment('Category name in multiple languages {"en": "...", "ar": "..."}');
            
            // Translatable description field
            $table->json('description')
                ->nullable()
                ->comment('Category description in multiple languages');
            
            // Visual customization
            $table->string('color', 7)
                ->default('#6366f1')
                ->comment('Hex color code for UI display');
            
            $table->string('icon', 50)
                ->default('heroicon-o-banknotes')
                ->comment('Heroicon name for Filament UI');
            
            // Category type for filtering
            $table->enum('type', ['operational', 'event', 'maintenance', 'staff', 'utility', 'marketing', 'other'])
                ->default('operational')
                ->comment('Category classification type');
            
            // Status and ordering
            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->comment('Whether this category is available for selection');
            
            $table->boolean('is_system')
                ->default(false)
                ->comment('System categories cannot be deleted by owners');
            
            $table->unsignedInteger('order')
                ->default(0)
                ->comment('Display order in lists');
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['owner_id', 'is_active'], 'expense_categories_owner_active_index');
            $table->index('type', 'expense_categories_type_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
