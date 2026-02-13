<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Service Fee Settings Table
 *
 * Service fees are customer-facing charges added ON TOP of the booking price.
 * Unlike commissions (which are deducted from the owner's payout and invisible
 * to customers), service fees are explicitly shown in the price breakdown.
 *
 * Scoping priority (same as commission_settings):
 *   1. Hall-specific   (highest priority)
 *   2. Owner-specific
 *   3. Global           (fallback)
 *
 * @see \App\Models\ServiceFeeSetting
 * @see \App\Models\CommissionSetting (sister table for owner-side charges)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_fee_settings', function (Blueprint $table): void {
            $table->id();

            // ── Scope: Global, Owner-specific, or Hall-specific ──
            $table->foreignId('hall_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            // ── Fee configuration ──
            $table->string('fee_type')->default('percentage');  // 'percentage' or 'fixed'
            $table->decimal('fee_value', 10, 2);                // e.g., 5.00 (%) or 2.500 (OMR)

            // ── Bilingual metadata ──
            $table->json('name')->nullable();                   // {"en": "...", "ar": "..."}
            $table->json('description')->nullable();            // {"en": "...", "ar": "..."}

            // ── Visibility & scheduling ──
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->timestamps();

            // ── Indexes for fast lookups ──
            $table->index(['hall_id', 'is_active'], 'sfs_hall_active_idx');
            $table->index(['owner_id', 'is_active'], 'sfs_owner_active_idx');
            $table->index('effective_from', 'sfs_effective_from_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_fee_settings');
    }
};
