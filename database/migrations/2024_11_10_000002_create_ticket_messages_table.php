<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the ticket_messages table
 * 
 * This table stores all messages/responses in a ticket conversation thread.
 * Supports file attachments, read receipts, and message type tracking.
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
     * Creates the ticket_messages table for tracking all communications
     * within a ticket including customer responses, staff replies, and system notes.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('ticket_messages', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Foreign key to parent ticket
            $table->foreignId('ticket_id')
                  ->constrained('tickets')
                  ->cascadeOnDelete(); // Delete all messages when ticket is deleted
            
            // Message author
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            // Message type classification
            $table->enum('type', [
                'customer_reply',    // Response from customer
                'staff_reply',       // Response from support staff
                'internal_note',     // Internal note (not visible to customer)
                'status_change',     // Automated status change notification
                'system_message'     // System-generated message
            ])->default('customer_reply')->index();
            
            // Message content
            $table->text('message');
            
            // Attachment support (JSON array of file paths)
            $table->json('attachments')->nullable();
            
            // Read receipt tracking
            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('read_at')->nullable();
            
            // Internal flag - messages marked as internal are not visible to customers
            $table->boolean('is_internal')->default(false)->index();
            
            // IP address and user agent for audit trail
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Metadata for extensibility
            $table->json('metadata')->nullable();
            
            // Tracking fields
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['ticket_id', 'created_at']); // For chronological message display
            $table->index(['user_id', 'type']); // For user activity tracking
            $table->index(['is_read', 'type']); // For unread message queries
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drops the ticket_messages table.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
