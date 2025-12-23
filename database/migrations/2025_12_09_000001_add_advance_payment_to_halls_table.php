<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Advance Payment Feature to Halls
 * 
 * This migration adds the ability for hall owners to require advance payments
 * when customers book their halls. Supports both fixed amounts and percentage-based.
 * 
 * Business Logic:
 * - Hall owners can enable/disable advance payment per hall
 * - Two types: Fixed amount (e.g., 500 OMR) or Percentage (e.g., 20%)
 * - Minimum advance ensures reasonable upfront payment
 * - Advance calculated on TOTAL booking (hall + services)
 * 
 * @see App\Models\Hall
 * @see App\Models\Booking
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds advance payment configuration columns to halls table:
     * - allows_advance_payment: Toggle feature on/off
     * - advance_payment_type: 'fixed' or 'percentage'
     * - advance_payment_amount: For fixed type (in OMR)
     * - advance_payment_percentage: For percentage type (0-100)
     * - minimum_advance_payment: Safety minimum amount
     */
    public function up(): void
    {
        Schema::table('halls', function (Blueprint $table) {
            // Enable/disable advance payment for this hall
            $table->boolean('allows_advance_payment')
                ->default(false)
                ->after('cancellation_fee_percentage')
                ->comment('Whether this hall requires advance payment');

            // Type of advance payment calculation
            $table->enum('advance_payment_type', ['fixed', 'percentage'])
                ->default('percentage')
                ->after('allows_advance_payment')
                ->comment('How to calculate advance: fixed amount or percentage of total');

            // Fixed amount in Omani Rials (3 decimals for precision)
            $table->decimal('advance_payment_amount', 10, 3)
                ->nullable()
                ->after('advance_payment_type')
                ->comment('Fixed advance amount in OMR (e.g., 500.000)');

            // Percentage of total booking (2 decimals, e.g., 20.00%)
            $table->decimal('advance_payment_percentage', 5, 2)
                ->nullable()
                ->after('advance_payment_amount')
                ->comment('Percentage of total for advance (e.g., 20.00)');

            // Minimum advance amount as safety net
            $table->decimal('minimum_advance_payment', 10, 3)
                ->nullable()
                ->after('advance_payment_percentage')
                ->comment('Minimum advance amount required (safety minimum)');

            // Index for queries filtering halls with advance payment
            $table->index('allows_advance_payment', 'halls_allows_advance_payment_index');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Removes all advance payment columns from halls table.
     */
    public function down(): void
    {
        Schema::table('halls', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex('halls_allows_advance_payment_index');

            // Drop all advance payment columns
            $table->dropColumn([
                'allows_advance_payment',
                'advance_payment_type',
                'advance_payment_amount',
                'advance_payment_percentage',
                'minimum_advance_payment',
            ]);
        });
    }
};
