<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the tickets table
 * 
 * This table stores customer support tickets and claims related to bookings.
 * Supports multiple ticket types, priorities, and status tracking with full audit trail.
 * 
 * @package Database\Migrations
 * @version 1.0.0
 * @created 2024-11-10
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the tickets table with:
     * - Relationship to bookings and users
     * - Comprehensive status and priority tracking
     * - Support for multiple ticket types (claim, complaint, inquiry, refund)
     * - SLA tracking with response and resolution times
     * - JSON metadata for extensibility
     * - Soft deletes for data retention
     * - Full-text search capabilities on title and description
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Unique ticket reference for customer-facing display
            $table->string('ticket_number', 20)->unique()->index();
            
            // Foreign keys - relationships
            $table->foreignId('booking_id')
                  ->nullable() // Not all tickets need to be related to bookings
                  ->constrained('bookings')
                  ->nullOnDelete(); // Keep ticket if booking is deleted
            
            $table->foreignId('user_id') // Customer who created the ticket
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            $table->foreignId('assigned_to') // Staff member assigned to handle ticket
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            
            // Ticket classification
            $table->enum('type', [
                'claim',       // Claim for compensation or issue
                'complaint',   // General complaint
                'inquiry',     // Question or information request
                'refund',      // Refund request
                'cancellation',// Cancellation request
                'technical',   // Technical issue
                'feedback',    // General feedback
                'other'        // Other types
            ])->default('inquiry')->index();
            
            // Priority level for ticket handling
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent'
            ])->default('medium')->index();
            
            // Status tracking with comprehensive workflow
            $table->enum('status', [
                'open',           // New ticket, not yet assigned
                'pending',        // Awaiting customer response
                'in_progress',    // Being actively worked on
                'on_hold',        // Temporarily paused
                'resolved',       // Issue resolved, awaiting confirmation
                'closed',         // Ticket closed/completed
                'cancelled',      // Ticket cancelled by customer
                'escalated'       // Escalated to higher authority
            ])->default('open')->index();
            
            // Ticket content
            $table->string('subject', 200);
            $table->text('description'); // Detailed description of the issue
            
            // Resolution and internal notes
            $table->text('resolution')->nullable(); // Solution provided to customer
            $table->text('internal_notes')->nullable(); // Staff-only notes
            
            // Customer satisfaction rating (1-5 stars)
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('feedback')->nullable(); // Customer feedback after resolution
            
            // SLA (Service Level Agreement) tracking
            $table->timestamp('first_response_at')->nullable(); // When first response was sent
            $table->timestamp('resolved_at')->nullable(); // When marked as resolved
            $table->timestamp('closed_at')->nullable(); // When finally closed
            
            // Due date for resolution (based on priority and SLA)
            $table->timestamp('due_date')->nullable()->index();
            
            // Metadata for extensibility (store additional custom fields as JSON)
            $table->json('metadata')->nullable();
            
            // Tracking fields
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at for soft delete capability
            
            // Indexes for performance optimization
            $table->index(['status', 'priority']); // Common filter combination
            $table->index(['assigned_to', 'status']); // For staff dashboard queries
            $table->index(['user_id', 'created_at']); // For customer ticket history
            $table->index('created_at'); // For sorting by date
            
            // Full-text search index for searching tickets
            $table->fullText(['subject', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drops the tickets table completely.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
