<?php

declare(strict_types=1);

/**
 * Migration: Create Expenses Table
 * 
 * This migration creates the main expenses table which allows hall owners
 * to track both booking-specific and general operational expenses.
 * Supports receipt attachments, recurring expenses, and detailed categorization.
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
     * Creates the expenses table with support for:
     * - Booking-linked and general expenses
     * - Multiple payment methods
     * - Receipt/document attachments
     * - Recurring expense tracking
     * - Approval workflow (optional)
     * - Omani Rial currency with 3 decimal places
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table): void {
            // Primary key
            $table->id();
            
            // Unique expense reference number for tracking
            $table->string('expense_number', 20)
                ->unique()
                ->comment('Unique expense reference (e.g., EXP-2026-00001)');
            
            // Owner relationship (required)
            $table->foreignId('owner_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Hall owner who recorded this expense');
            
            // Optional hall relationship (for hall-specific expenses)
            $table->foreignId('hall_id')
                ->nullable()
                ->constrained('halls')
                ->onDelete('set null')
                ->comment('Specific hall this expense relates to, null for general expenses');
            
            // Optional booking relationship (for booking-specific expenses)
            $table->foreignId('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->onDelete('set null')
                ->comment('Specific booking this expense relates to');
            
            // Category relationship
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('expense_categories')
                ->onDelete('set null')
                ->comment('Expense category for classification');
            
            // Expense type classification
            $table->enum('expense_type', ['booking', 'operational', 'recurring', 'one_time'])
                ->default('operational')
                ->comment('Type: booking-specific, operational, recurring, or one-time');
            
            // Bilingual title and description
            $table->json('title')
                ->comment('Expense title {"en": "...", "ar": "..."}');
            
            $table->json('description')
                ->nullable()
                ->comment('Detailed description {"en": "...", "ar": "..."}');
            
            // Financial details - Using decimal(12,3) for Omani Rial
            $table->decimal('amount', 12, 3)
                ->comment('Expense amount in OMR');
            
            $table->string('currency', 3)
                ->default('OMR')
                ->comment('Currency code (default: OMR)');
            
            // Tax handling (VAT if applicable in future)
            $table->decimal('tax_amount', 12, 3)
                ->default(0.000)
                ->comment('Tax/VAT amount if applicable');
            
            $table->decimal('total_amount', 12, 3)
                ->storedAs('amount + tax_amount')
                ->comment('Total amount including tax');
            
            // Payment information
            $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'cheque', 'other'])
                ->default('cash')
                ->comment('How this expense was paid');
            
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'cancelled'])
                ->default('paid')
                ->comment('Payment status of this expense');
            
            $table->string('payment_reference', 100)
                ->nullable()
                ->comment('Payment reference or receipt number');
            
            // Date tracking
            $table->date('expense_date')
                ->comment('Date the expense was incurred');
            
            $table->date('due_date')
                ->nullable()
                ->comment('Payment due date for pending expenses');
            
            $table->timestamp('paid_at')
                ->nullable()
                ->comment('When the expense was actually paid');
            
            // Vendor/Supplier information
            $table->string('vendor_name', 255)
                ->nullable()
                ->comment('Name of the vendor/supplier');
            
            $table->string('vendor_phone', 20)
                ->nullable()
                ->comment('Vendor contact phone');
            
            $table->string('vendor_email', 255)
                ->nullable()
                ->comment('Vendor contact email');
            
            // Document attachments (receipts, invoices)
            $table->json('attachments')
                ->nullable()
                ->comment('Array of file paths for receipts/documents');
            
            // Recurring expense settings
            $table->boolean('is_recurring')
                ->default(false)
                ->comment('Whether this is a recurring expense');
            
            $table->enum('recurring_frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])
                ->nullable()
                ->comment('Frequency for recurring expenses');
            
            $table->date('recurring_start_date')
                ->nullable()
                ->comment('Start date for recurring expenses');
            
            $table->date('recurring_end_date')
                ->nullable()
                ->comment('End date for recurring expenses (null = indefinite)');
            
            $table->unsignedInteger('recurring_count')
                ->default(0)
                ->comment('Number of times this recurring expense has been generated');
            
            // Parent expense for recurring series
            $table->foreignId('parent_expense_id')
                ->nullable()
                ->constrained('expenses')
                ->onDelete('set null')
                ->comment('Parent expense if this is part of a recurring series');
            
            // Status and approval (optional workflow)
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'archived'])
                ->default('approved')
                ->comment('Expense status for approval workflow');
            
            $table->text('notes')
                ->nullable()
                ->comment('Internal notes about this expense');
            
            $table->text('rejection_reason')
                ->nullable()
                ->comment('Reason if expense was rejected');
            
            // Audit fields
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User who created this expense');
            
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('User who approved this expense');
            
            $table->timestamp('approved_at')
                ->nullable()
                ->comment('When the expense was approved');
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Comprehensive indexes for query optimization
            $table->index('expense_number', 'expenses_number_index');
            $table->index(['owner_id', 'expense_date'], 'expenses_owner_date_index');
            $table->index(['owner_id', 'status'], 'expenses_owner_status_index');
            $table->index(['hall_id', 'expense_date'], 'expenses_hall_date_index');
            $table->index(['booking_id'], 'expenses_booking_index');
            $table->index(['category_id'], 'expenses_category_index');
            $table->index(['expense_type'], 'expenses_type_index');
            $table->index(['payment_status'], 'expenses_payment_status_index');
            $table->index(['expense_date'], 'expenses_date_index');
            $table->index(['is_recurring', 'recurring_frequency'], 'expenses_recurring_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
