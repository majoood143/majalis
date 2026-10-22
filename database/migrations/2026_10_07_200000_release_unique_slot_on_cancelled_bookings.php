<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The (hall, date, slot) unique keys also covered cancelled/refunded and
 * soft-deleted bookings, so a released slot could never be booked again.
 *
 * Both keys now include a nullable `slot_lock` column: 1 while the row holds
 * the slot, NULL once released (NULLs never collide in a unique index).
 * - bookings.slot_lock is generated from status/deleted_at.
 * - booking_days.slot_lock is maintained by BookingObserver.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedTinyInteger('slot_lock')
                ->nullable()
                ->storedAs("CASE WHEN status IN ('cancelled', 'refunded', 'rejected') OR deleted_at IS NOT NULL THEN NULL ELSE 1 END");
        });

        // Add the new key first: it also serves the hall_id foreign key
        Schema::table('bookings', function (Blueprint $table) {
            $table->unique(['hall_id', 'booking_date', 'time_slot', 'slot_lock'], 'bookings_active_slot_unique');
            $table->dropUnique('unique_hall_slot');
        });

        Schema::table('booking_days', function (Blueprint $table) {
            $table->unsignedTinyInteger('slot_lock')->nullable()->default(1);
        });

        // Days of bookings that no longer hold their slot
        DB::table('booking_days')
            ->whereIn('booking_id', fn ($q) => $q->select('id')->from('bookings')->whereNull('slot_lock'))
            ->update(['slot_lock' => null]);

        Schema::table('booking_days', function (Blueprint $table) {
            $table->unique(['hall_id', 'date', 'time_slot', 'slot_lock'], 'booking_days_active_slot_unique');
            $table->dropUnique('unique_booking_day_slot');
        });
    }

    public function down(): void
    {
        Schema::table('booking_days', function (Blueprint $table) {
            $table->unique(['hall_id', 'date', 'time_slot'], 'unique_booking_day_slot');
            $table->dropUnique('booking_days_active_slot_unique');
            $table->dropColumn('slot_lock');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unique(['hall_id', 'booking_date', 'time_slot'], 'unique_hall_slot');
            $table->dropUnique('bookings_active_slot_unique');
            $table->dropColumn('slot_lock');
        });
    }
};
