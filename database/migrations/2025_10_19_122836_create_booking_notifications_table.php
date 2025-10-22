<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Notification Type
            $table->string('type'); // email, sms, push, whatsapp
            $table->string('event'); // booking_created, payment_confirmed, booking_cancelled, reminder, etc.

            // Recipient
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();

            // Content
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('data')->nullable(); // Additional data/variables

            // Status
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);

            // Tracking
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('clicked_at')->nullable();

            // External Service Response
            $table->string('external_id')->nullable(); // SMS/Email service message ID
            $table->json('provider_response')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('event');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_notifications');
    }
};
