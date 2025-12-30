@php
    $totalSlots = 30 * 4; // 30 days * 4 slots per day
    $bookedSlots = $getRecord()->bookings()
        ->whereMonth('booking_date', now()->month)
        ->whereYear('booking_date', now()->year)
        ->whereIn('status', ['confirmed', 'completed'])
        ->count();
    $rate = $totalSlots > 0 ? round(($bookedSlots / $totalSlots) * 100, 1) : 0;

    $color = match(true) {
        $rate >= 70 => 'success',
        $rate >= 40 => 'warning',
        default => 'danger'
    };
@endphp

<div class="flex items-center justify-center">
    <div class="relative">
        <div class="text-sm font-semibold text-{{ $color }}-600 dark:text-{{ $color }}-400">
            {{ $rate }}%
        </div>
        <div class="w-20 h-2 mt-1 bg-gray-200 rounded-full">
            <div class="bg-{{ $color }}-600 h-2 rounded-full" style="width: {{ $rate }}%"></div>
        </div>
    </div>
</div>
