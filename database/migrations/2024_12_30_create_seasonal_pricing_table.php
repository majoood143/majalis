<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create seasonal_pricing table for date-range based pricing rules.
 *
 * This allows hall owners to set special prices for:
 * - Holidays (Eid, National Day, etc.)
 * - Peak seasons (Wedding season, summer)
 * - Weekends
 * - Special events
 *
 * Run: php artisan migrate
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seasonal_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')
                ->constrained('halls')
                ->cascadeOnDelete();

            // Rule identification
            $table->json('name'); // Translatable: {"en": "Eid Holiday", "ar": "عيد"}
            $table->string('type')->default('seasonal');
            // Types: seasonal, holiday, weekend, special_event, early_bird, last_minute

            // Date range
            $table->date('start_date');
            $table->date('end_date');

            // Recurrence (for weekends, annual holidays)
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_type')->nullable();
            // Types: weekly (weekends), yearly (annual holidays), null (one-time)

            // Days of week (for weekend pricing)
            // JSON array: [5, 6] for Fri-Sat (Omani weekend)
            $table->json('days_of_week')->nullable();

            // Pricing adjustment
            $table->string('adjustment_type')->default('percentage');
            // Types: percentage, fixed_increase, fixed_price

            $table->decimal('adjustment_value', 10, 3)->default(0);
            // For percentage: 20 means +20%
            // For fixed_increase: 50 means +50 OMR
            // For fixed_price: 200 means exactly 200 OMR

            // Apply to specific slots or all
            $table->json('apply_to_slots')->nullable();
            // null = all slots, or ["morning", "evening"]

            // Priority (higher = applied first when multiple rules match)
            $table->integer('priority')->default(0);

            // Minimum/Maximum price constraints
            $table->decimal('min_price', 10, 3)->nullable();
            $table->decimal('max_price', 10, 3)->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['hall_id', 'start_date', 'end_date']);
            $table->index(['hall_id', 'is_active']);
            $table->index('type');
        });

        // Add advance payment columns to halls table if not exists
        if (!Schema::hasColumn('halls', 'allows_advance_payment')) {
            Schema::table('halls', function (Blueprint $table) {
                $table->boolean('allows_advance_payment')->default(false)->after('pricing_override');
                $table->string('advance_payment_type')->default('percentage')->after('allows_advance_payment');
                // Types: percentage, fixed
                $table->decimal('advance_payment_amount', 10, 3)->nullable()->after('advance_payment_type');
                $table->decimal('advance_payment_percentage', 5, 2)->nullable()->after('advance_payment_amount');
                $table->decimal('minimum_advance_payment', 10, 3)->nullable()->after('advance_payment_percentage');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasonal_pricing');

        // Remove advance payment columns
        if (Schema::hasColumn('halls', 'allows_advance_payment')) {
            Schema::table('halls', function (Blueprint $table) {
                $table->dropColumn([
                    'allows_advance_payment',
                    'advance_payment_type',
                    'advance_payment_amount',
                    'advance_payment_percentage',
                    'minimum_advance_payment',
                ]);
            });
        }
    }
};
