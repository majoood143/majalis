<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the owner_payouts table.
 *
 * This table stores payout records for hall owners, tracking commission deductions,
 * payment processing status, and bank transfer details.
 *
 * @package Database\Migrations
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the owner_payouts table with columns for:
     * - Payout identification and numbering
     * - Period tracking (start/end dates)
     * - Financial calculations (gross, commission, net)
     * - Payment processing status and details
     * - Bank transfer information
     * - Audit trail (processed_by, timestamps)
     */
    public function up(): void
    {
        Schema::create('owner_payouts', function (Blueprint $table): void {
            // Primary key
            $table->id();

            // Unique payout reference number (e.g., PO-2026-00001)
            $table->string('payout_number', 20)->unique();

            // Owner reference - links to users table
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Payout period dates
            $table->date('period_start')
                ->comment('Start date of the payout period');
            $table->date('period_end')
                ->comment('End date of the payout period');

            // Financial calculations (using decimal:3 for OMR precision)
            $table->decimal('gross_revenue', 12, 3)->default(0)
                ->comment('Total revenue before commission deduction');
            $table->decimal('commission_amount', 12, 3)->default(0)
                ->comment('Total commission deducted by platform');
            $table->decimal('commission_rate', 5, 2)->default(0)
                ->comment('Commission percentage applied');
            $table->decimal('net_payout', 12, 3)->default(0)
                ->comment('Final amount to be paid to owner (gross - commission)');
            $table->decimal('adjustments', 12, 3)->default(0)
                ->comment('Any adjustments (refunds, bonuses, penalties)');

            // Booking count for this period
            $table->unsignedInteger('bookings_count')->default(0)
                ->comment('Number of bookings included in this payout');

            // Payout status enum
            $table->string('status', 20)->default('pending')
                ->comment('pending, processing, completed, failed, cancelled, on_hold');

            // Payment method and bank details
            $table->string('payment_method', 50)->nullable()
                ->comment('bank_transfer, cheque, cash');
            $table->json('bank_details')->nullable()
                ->comment('Stored bank account info used for transfer');

            // Transaction tracking
            $table->string('transaction_reference', 100)->nullable()
                ->comment('Bank transaction reference number');

            // Processing timestamps
            $table->timestamp('processed_at')->nullable()
                ->comment('When payout processing started');
            $table->timestamp('completed_at')->nullable()
                ->comment('When payout was successfully completed');
            $table->timestamp('failed_at')->nullable()
                ->comment('When payout failed (if applicable)');

            // Audit information
            $table->foreignId('processed_by')->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin user who processed the payout');

            // Additional information
            $table->text('notes')->nullable()
                ->comment('Internal notes about this payout');
            $table->text('failure_reason')->nullable()
                ->comment('Reason for failure if payout failed');
            $table->string('receipt_path', 255)->nullable()
                ->comment('Path to generated receipt/statement PDF');

            // Standard timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['owner_id', 'status'], 'idx_owner_status');
            $table->index(['period_start', 'period_end'], 'idx_period_dates');
            $table->index('status', 'idx_status');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_payouts');
    }
};
