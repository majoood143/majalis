<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Guest Booking Fields to Bookings Table
 *
 * This migration adds support for guest bookings by:
 * - Making user_id nullable (guests don't have accounts)
 * - Adding is_guest_booking flag for easy filtering
 * - Adding guest_token for secure guest access to booking details
 * - Adding account_created_at to track if guest later creates account
 *
 * Guest Booking Flow:
 * 1. Guest fills booking form with email/phone
 * 2. System generates unique guest_token
 * 3. Guest receives email with booking link containing token
 * 4. Guest can view/manage booking using token
 * 5. Optionally, guest can create account (links booking to new user)
 *
 * @package Database\Migrations
 * @version 1.0.0
 * @see App\Models\Booking
 * @see App\Http\Controllers\Customer\GuestBookingController
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds guest booking support columns to bookings table:
     * - is_guest_booking: Boolean flag to identify guest bookings
     * - guest_token: Unique 64-char token for secure guest access
     * - guest_token_expires_at: Token expiration for security
     * - account_created_at: Timestamp when guest creates account
     *
     * Also modifies user_id to be nullable for guest bookings.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Make user_id nullable to support guest bookings
            // Note: This requires dropping and recreating the foreign key
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Flag to easily identify guest bookings in queries
            $table->boolean('is_guest_booking')
                ->default(false)
                ->after('user_id')
                ->index()
                ->comment('True if booking was made without user account');

            // Unique token for guest access to booking details
            // 64 characters provides sufficient entropy for security
            $table->string('guest_token', 64)
                ->nullable()
                ->unique()
                ->after('is_guest_booking')
                ->comment('Secure token for guest booking access');

            // Token expiration for added security (optional, can be used for time-limited access)
            $table->timestamp('guest_token_expires_at')
                ->nullable()
                ->after('guest_token')
                ->comment('When guest access token expires');

            // Track if/when guest creates an account after booking
            $table->timestamp('account_created_at')
                ->nullable()
                ->after('guest_token_expires_at')
                ->comment('When guest converted to registered user');

            // Index for efficient guest booking queries
            $table->index(['is_guest_booking', 'customer_email'], 'bookings_guest_email_index');
            $table->index(['guest_token', 'is_guest_booking'], 'bookings_guest_token_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Removes guest booking columns and restores user_id constraint.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('bookings_guest_email_index');
            $table->dropIndex('bookings_guest_token_lookup');

            // Drop columns
            $table->dropColumn([
                'is_guest_booking',
                'guest_token',
                'guest_token_expires_at',
                'account_created_at',
            ]);

            // Note: Restoring user_id to NOT NULL requires ensuring no NULL values exist
            // This should be handled manually if rolling back in production
        });
    }
};
