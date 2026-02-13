<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add Service Fee Audit Columns to Bookings
 *
 * The `platform_fee` column already exists and stores the calculated amount.
 * These new columns record WHICH fee rule was applied (type + value) at booking
 * time, providing an audit trail even if the fee setting is later changed.
 *
 * Financial flow after this migration:
 *   Customer pays:  hall_price + services_price + platform_fee = total_amount
 *   Owner receives: total_amount - platform_fee - commission_amount = owner_payout
 *
 * @see \App\Models\Booking
 * @see \App\Models\ServiceFeeSetting
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            // Record which fee type was applied at booking time
            $table->string('service_fee_type')
                ->nullable()
                ->after('platform_fee')
                ->comment('percentage or fixed — snapshot at booking time');

            // Record the original fee value (e.g., 5.00 for 5%)
            $table->decimal('service_fee_value', 10, 2)
                ->nullable()
                ->after('service_fee_type')
                ->comment('Original fee value from service_fee_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropColumn(['service_fee_type', 'service_fee_value']);
        });
    }
};
