<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Advance Payment Tracking to Bookings
 * 
 * This migration adds fields to track partial payments in bookings.
 * When a hall requires advance payment, customers pay in two stages:
 * 1. Advance payment (at booking time) - via Thawani gateway
 * 2. Balance payment (before event) - via bank transfer/cash
 * 
 * Payment Flow:
 * - Customer books hall requiring advance
 * - Pays advance_amount via Thawani
 * - payment_status becomes 'partial'
 * - Admin manually marks balance as paid later
 * - payment_status changes to 'paid'
 * 
 * @see App\Models\Booking
 * @see App\Models\Hall
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds advance payment tracking columns to bookings table:
     * - payment_type: Whether customer paid 'full' or 'advance'
     * - advance_amount: Actual advance paid (hall + services calculation)
     * - balance_due: Remaining amount to be paid
     * - balance_paid_at: When balance was received
     * - balance_payment_method: How balance was paid
     * - balance_payment_reference: Transaction/receipt reference
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Type of payment made at booking time
            $table->enum('payment_type', ['full', 'advance'])
                ->default('full')
                ->after('payment_status')
                ->comment('Whether customer paid full amount or advance only');

            // Amount paid as advance (calculated from hall settings)
            $table->decimal('advance_amount', 10, 3)
                ->nullable()
                ->after('payment_type')
                ->comment('Advance amount paid at booking (includes services)');

            // Remaining balance to be paid before event
            $table->decimal('balance_due', 10, 3)
                ->nullable()
                ->after('advance_amount')
                ->comment('Remaining balance to be paid before event');

            // When the balance payment was received
            $table->timestamp('balance_paid_at')
                ->nullable()
                ->after('balance_due')
                ->comment('When balance payment was received');

            // How the balance was paid (bank_transfer, cash, etc.)
            $table->string('balance_payment_method')
                ->nullable()
                ->after('balance_paid_at')
                ->comment('Method used for balance payment (bank_transfer, cash)');

            // Reference number or receipt for balance payment
            $table->string('balance_payment_reference')
                ->nullable()
                ->after('balance_payment_method')
                ->comment('Transaction reference or receipt number for balance');

            // Indexes for common queries
            $table->index('payment_type', 'bookings_payment_type_index');
            $table->index('balance_paid_at', 'bookings_balance_paid_at_index');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Removes all advance payment tracking columns from bookings table.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('bookings_payment_type_index');
            $table->dropIndex('bookings_balance_paid_at_index');

            // Drop all advance payment tracking columns
            $table->dropColumn([
                'payment_type',
                'advance_amount',
                'balance_due',
                'balance_paid_at',
                'balance_payment_method',
                'balance_payment_reference',
            ]);
        });
    }
};
