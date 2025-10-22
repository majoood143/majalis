<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();

            // Payment Reference
            $table->string('payment_reference')->unique(); // Our internal reference
            $table->string('transaction_id')->nullable()->unique(); // Thawani transaction ID

            // Payment Details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('OMR');
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable(); // card, wallet, etc.

            // Thawani Response Data
            $table->json('gateway_response')->nullable();
            $table->string('payment_url')->nullable(); // Thawani checkout URL
            $table->string('invoice_id')->nullable(); // Thawani invoice ID

            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            // Refund Details
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->text('refund_reason')->nullable();

            // Failure Details
            $table->text('failure_reason')->nullable();

            // Metadata
            $table->string('customer_ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('booking_id');
            $table->index('payment_reference');
            $table->index('transaction_id');
            $table->index('status');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
