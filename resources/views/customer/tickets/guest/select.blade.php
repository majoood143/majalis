@extends('customer.layout')

@section('title', __('tickets_guest.select_title') . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-2xl sm:px-6 lg:px-8">

    {{-- Step indicator --}}
    <div class="flex items-center mb-8 max-w-xs mx-auto">
        <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-indigo-600 rounded-full shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="flex-1 h-1 mx-2 bg-indigo-300 rounded"></div>
        <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-indigo-600 rounded-full shrink-0">1½</div>
        <div class="flex-1 h-1 mx-2 bg-gray-200 rounded"></div>
        <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-gray-400 bg-gray-100 rounded-full shrink-0">2</div>
    </div>

    {{-- Header --}}
    <div class="mb-6 text-center">
        <h1 class="mb-2 text-2xl font-bold text-gray-900">{{ __('tickets_guest.select_title') }}</h1>
        <p class="text-gray-600 text-sm">{{ __('tickets_guest.select_subtitle') }}</p>
    </div>

    @error('booking_id')
        <div class="p-4 mb-6 text-red-800 bg-red-100 border border-red-200 rounded-lg text-sm">
            {{ $message }}
        </div>
    @enderror

    <form action="{{ route('guest.tickets.select') }}" method="POST">
        @csrf

        <div class="space-y-3 mb-6">
            @foreach($bookings as $booking)
                <label class="relative cursor-pointer block">
                    <input type="radio" name="booking_id" value="{{ $booking->id }}"
                           class="sr-only peer"
                           {{ old('booking_id') == $booking->id ? 'checked' : '' }}>

                    <div class="flex items-start gap-4 p-5 bg-white rounded-xl border-2 border-gray-200 transition
                                peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300">

                        {{-- Radio indicator --}}
                        <div class="mt-0.5 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0
                                    peer-checked:border-indigo-500 bg-white">
                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-600 hidden peer-checked:block"></div>
                        </div>

                        {{-- Hall image --}}
                        <div class="shrink-0">
                            @if($booking->hall?->featured_image)
                                <img src="{{ Storage::url($booking->hall->featured_image) }}"
                                     alt="{{ $booking->hall->name }}"
                                     class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-xs font-semibold text-indigo-600 tracking-wide uppercase">
                                    {{ $booking->booking_number }}
                                </span>
                                @php
                                    $statusValue = is_object($booking->status) ? $booking->status->value : $booking->status;
                                    $statusColor = match($statusValue) {
                                        'confirmed' => 'bg-green-100 text-green-700',
                                        'pending'   => 'bg-yellow-100 text-yellow-700',
                                        'cancelled' => 'bg-red-100 text-red-600',
                                        default     => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ __('enums.booking_status.' . $statusValue) }}
                                </span>
                            </div>

                            <p class="font-semibold text-gray-900 truncate">
                                {{ $booking->hall?->name ?? __('tickets_guest.unknown_hall') }}
                            </p>

                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $booking->booking_date?->format('M d, Y') }}
                                @if($booking->time_slot)
                                    &mdash; {{ __('enums.time_slot.' . $booking->time_slot) }}
                                @endif
                            </p>
                        </div>

                        {{-- Chevron --}}
                        <svg class="w-5 h-5 text-gray-300 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('guest.tickets.verify') }}"
               class="px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                {{ __('tickets_guest.back_btn') }}
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                {{ __('tickets_guest.select_continue') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </form>

</div>

{{-- Make the radio indicator visible via JS since Tailwind peer-checked on sibling doesn't reach grandchildren --}}
<script>
document.querySelectorAll('input[name="booking_id"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('input[name="booking_id"]').forEach(r => {
            const dot = r.closest('label').querySelector('.rounded-full .rounded-full');
            if (dot) dot.style.display = r.checked ? 'block' : 'none';
        });
    });
});
</script>
@endsection
