<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Guest Sessions Table
 *
 * This table manages guest booking sessions and email verification via OTP.
 * It provides security for guest bookings by requiring email verification
 * before payment processing.
 *
 * Session Flow:
 * 1. Guest initiates booking → session created with pending status
 * 2. OTP sent to guest email
 * 3. Guest enters OTP → session verified
 * 4. Guest proceeds to payment
 * 5. Session expires or completes after booking
 *
 * Security Features:
 * - OTP expires after 10 minutes
 * - Maximum 3 verification attempts
 * - Rate limiting: max 3 pending bookings per email
 * - Sessions auto-expire after 24 hours
 *
 * @package Database\Migrations
 * @version 1.0.0
 * @see App\Models\GuestSession
 * @see App\Http\Controllers\Customer\GuestBookingController
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the guest_sessions table for managing guest booking
     * verification and temporary data storage.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('guest_sessions', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Unique session identifier (UUID for security)
            $table->uuid('session_token')
                ->unique()
                ->comment('Unique session identifier for guest');

            // Guest information (pre-booking)
            $table->string('email')
                ->index()
                ->comment('Guest email address');

            $table->string('phone', 20)
                ->nullable()
                ->comment('Guest phone number');

            $table->string('name')
                ->comment('Guest full name');

            // OTP verification
            $table->string('otp_code', 6)
                ->nullable()
                ->comment('6-digit OTP for email verification');

            $table->timestamp('otp_expires_at')
                ->nullable()
                ->comment('OTP expiration time (10 minutes from generation)');

            $table->unsignedTinyInteger('otp_attempts')
                ->default(0)
                ->comment('Number of OTP verification attempts');

            $table->boolean('is_verified')
                ->default(false)
                ->index()
                ->comment('Whether email has been verified via OTP');

            $table->timestamp('verified_at')
                ->nullable()
                ->comment('When email verification completed');

            // Session status
            $table->enum('status', [
                'pending',      // Session created, awaiting OTP
                'verified',     // OTP verified, can proceed to booking
                'booking',      // Currently in booking process
                'payment',      // Proceeding to payment
                'completed',    // Booking completed successfully
                'expired',      // Session expired without completion
                'cancelled',    // Guest cancelled the process
            ])->default('pending')
                ->index()
                ->comment('Current session status');

            // Temporary booking data (JSON)
            // Stores form data between steps
            $table->json('booking_data')
                ->nullable()
                ->comment('Temporary storage for booking form data');

            // Hall reference (for the booking)
            $table->foreignId('hall_id')
                ->nullable()
                ->constrained('halls')
                ->nullOnDelete()
                ->comment('Hall being booked');

            // Link to actual booking once created
            $table->foreignId('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->nullOnDelete()
                ->comment('Created booking (after successful payment)');

            // Security & tracking
            $table->string('ip_address', 45)
                ->nullable()
                ->comment('Guest IP address');

            $table->text('user_agent')
                ->nullable()
                ->comment('Guest browser user agent');

            // Session expiration (24 hours from creation)
            $table->timestamp('expires_at')
            ->nullable()
                ->comment('Session expiration time');

            // Metadata for extensibility
            $table->json('metadata')
                ->nullable()
                ->comment('Additional session data');

            // Timestamps
            $table->timestamps();

            // Indexes for common queries
            $table->index(['email', 'status'], 'guest_sessions_email_status');
            $table->index(['status', 'expires_at'], 'guest_sessions_cleanup');
            $table->index(['hall_id', 'status'], 'guest_sessions_hall_pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_sessions');
    }
};
