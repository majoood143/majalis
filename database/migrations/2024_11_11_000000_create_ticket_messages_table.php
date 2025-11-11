<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Ticket Messages Table Migration
 * 
 * Creates the table for storing conversation messages within support tickets.
 * Supports:
 * - Multiple message types (customer reply, staff reply, internal note)
 * - File attachments stored as JSON metadata
 * - Read/unread tracking
 * - Soft deletes for message history
 * 
 * @package Database\Migrations
 * @version 1.0.0
 * @author Majid Al Abri
 * @compatibility Laravel 12, PHP 8.4.12
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the ticket_messages table with all necessary columns,
     * indexes, and foreign key constraints.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('ticket_messages', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Keys
            // Ticket this message belongs to
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete()  // Delete messages if ticket is deleted
                ->cascadeOnUpdate(); // Update if ticket ID changes

            // User who created this message
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete()  // Prevent deleting users with messages
                ->cascadeOnUpdate();

            // Message Type
            // Using string to store enum value
            // Examples: 'customer_reply', 'staff_reply', 'internal_note'
            $table->string('type', 50)
                ->default('staff_reply')
                ->comment('Message type: customer_reply, staff_reply, internal_note');

            // Message Content
            // Using TEXT for long messages (up to 65,535 characters)
            $table->text('message')
                ->comment('The actual message content');

            // File Attachments
            // Stores JSON array of attachment metadata
            // Structure: [{'path': '...', 'original_name': '...', 'mime_type': '...', 'size': 123, 'uploaded_at': '...'}]
            $table->json('attachments')
                ->nullable()
                ->comment('JSON array of file attachment metadata');

            // Internal Flag
            // Determines if message is visible to customers
            // true = internal note (staff only)
            // false = visible to customer
            $table->boolean('is_internal')
                ->default(false)
                ->index() // Index for filtering internal notes
                ->comment('Whether this is an internal staff-only note');

            // Read Status
            // Tracks if message has been read by recipient
            // Used for notification badges and unread counts
            $table->boolean('is_read')
                ->default(false)
                ->index() // Index for filtering unread messages
                ->comment('Whether this message has been read');

            // Timestamps
            // created_at: When message was posted
            // updated_at: When message was last edited
            $table->timestamps();

            // Soft Deletes
            // Allows "deleting" messages while keeping history
            // Deleted messages are hidden but remain in database
            $table->softDeletes();

            // Indexes for Performance
            // Composite index for common query patterns
            $table->index(['ticket_id', 'created_at'], 'idx_ticket_messages_ticket_created');
            $table->index(['ticket_id', 'is_internal'], 'idx_ticket_messages_ticket_internal');
            $table->index(['ticket_id', 'is_read'], 'idx_ticket_messages_ticket_read');
            $table->index(['user_id', 'created_at'], 'idx_ticket_messages_user_created');
            $table->index('type', 'idx_ticket_messages_type');

            // Full-text search index for message content
            // Allows efficient searching of message text
            // Note: Requires MySQL 5.7+ or MariaDB 10.0+
            $table->fullText('message', 'idx_ticket_messages_message_fulltext');
        });

        // Add table comment for documentation
        DB::statement("ALTER TABLE `ticket_messages` COMMENT = 'Stores individual messages in ticket conversation threads'");
    }

    /**
     * Reverse the migrations.
     * 
     * Drops the ticket_messages table and all its data.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the table
        Schema::dropIfExists('ticket_messages');
    }
};
