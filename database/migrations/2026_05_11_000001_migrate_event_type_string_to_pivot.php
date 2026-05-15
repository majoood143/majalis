<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure "Corporate" event type exists
        $corporateId = DB::table('event_types')->insertGetId([
            'name' => json_encode(['en' => 'Corporate', 'ar' => 'شركات']),
            'sort_order' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $map = [
            '1'         => 1,
            'wedding'   => 1,
            'corporate' => $corporateId,
        ];

        $bookings = DB::table('bookings')
            ->whereNotNull('event_type')
            ->select('id', 'event_type')
            ->get();

        foreach ($bookings as $booking) {
            $eventTypeId = $map[$booking->event_type] ?? null;

            if (!$eventTypeId) {
                continue;
            }

            $exists = DB::table('booking_event_type')
                ->where('booking_id', $booking->id)
                ->where('event_type_id', $eventTypeId)
                ->exists();

            if (!$exists) {
                DB::table('booking_event_type')->insert([
                    'booking_id'    => $booking->id,
                    'event_type_id' => $eventTypeId,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('event_types')->where('name->en', 'Corporate')->delete();

        DB::table('booking_event_type')
            ->whereIn('booking_id', DB::table('bookings')->whereNotNull('event_type')->pluck('id'))
            ->delete();
    }
};
