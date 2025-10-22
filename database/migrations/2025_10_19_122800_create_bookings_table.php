<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 20)->unique(); // e.g., BK-2025-00001

            // Relationships
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Booking Details
            $table->date('booking_date');
            $table->string('time_slot'); // morning, afternoon, evening, full_day
            $table->integer('number_of_guests');

            // Customer Info (stored separately for record keeping)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20);
            $table->text('customer_notes')->nullable();

            // Event Details
            $table->string('event_type')->nullable(); // wedding, corporate, birthday, etc.
            $table->json('event_details')->nullable();

            // Pricing Breakdown
            $table->decimal('hall_price', 10, 2);
            $table->decimal('services_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);

            // Commission
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->string('commission_type')->nullable(); // percentage or fixed
            $table->decimal('commission_value', 10, 2)->nullable();
            $table->decimal('owner_payout', 10, 2)->default(0);

            // Status
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');

            // Cancellation
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();

            // Confirmation
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // PDF Invoice
            $table->string('invoice_path')->nullable();

            // Internal Notes
            $table->text('admin_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('booking_number');
            $table->index(['hall_id', 'booking_date', 'time_slot']);
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('booking_date');
            $table->unique(['hall_id', 'booking_date', 'time_slot'], 'unique_hall_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
