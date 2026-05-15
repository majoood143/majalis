@extends('customer.layout')

@section('title', __('booking.booking_number', ['number' => $booking->booking_number]) . ' - majalis')

@section('content')
<div class="max-w-5xl px-4 py-8 mx-auto sm:px-6 lg:px-8">

    {{-- ─── Breadcrumb ──────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('customer.bookings') }}"
           class="hover:text-[#B9916D] transition-colors">{{ __('booking.my_bookings') }}</a>
        <svg class="w-3.5 h-3.5 shrink-0 rtl:rotate-180 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-700 font-medium truncate">
            {{ __('booking.booking_number', ['number' => $booking->booking_number]) }}
        </span>
    </nav>

    {{-- ─── Header Banner ───────────────────────────────────── --}}
    <div class="relative mb-8 overflow-hidden bg-[#B9916D] rounded-2xl">
        <div class="absolute inset-0 opacity-10"
             style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");">
        </div>
        <div class="relative flex flex-col gap-3 px-6 py-7 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <div>
                <h1 class="mb-0.5 text-2xl font-bold text-white">{{ __('booking.booking_details') }}</h1>
                <p class="text-[#e4c9b5] text-sm">
                    {{ $booking->booking_date->format('F d, Y') }} &bull; {{ ucfirst($booking->time_slot) }}
                </p>
            </div>
            @php
                $statusClass = match($booking->status) {
                    'confirmed'  => 'bg-green-100 text-green-800',
                    'pending'    => 'bg-amber-100 text-amber-800',
                    'completed'  => 'bg-blue-100 text-blue-800',
                    default      => 'bg-red-100 text-red-800',
                };
                $statusLabel = match($booking->status) {
                    'confirmed'  => __('booking.status_confirmed'),
                    'pending'    => __('booking.status_pending'),
                    'completed'  => __('booking.status_completed'),
                    default      => __('booking.status_cancelled'),
                };
            @endphp
            <span class="inline-flex self-start px-4 py-1.5 text-sm font-semibold rounded-full shrink-0 {{ $statusClass }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Main Content ──────────────────────────────────── --}}
        <div class="flex flex-col gap-6 lg:col-span-2">

            {{-- Hall Information --}}
            <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                <div class="flex items-start gap-5 p-6">
                    <div class="shrink-0">
                        @if($booking->hall->featured_image ?? false)
                            <img src="{{ Storage::url($booking->hall->featured_image) }}"
                                 alt="{{ $booking->hall->name ?? __('booking.hall_name_not_available') }}"
                                 class="object-cover w-28 h-28 rounded-xl">
                        @else
                            <div class="w-28 h-28 rounded-xl bg-linear-to-br from-[#B9916D] to-[#E8D5C4]"></div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="mb-3 text-xl font-bold text-gray-900">
                            {{ $booking->hall->name ?? __('booking.hall_name_not_available') }}
                        </h2>
                        <div class="flex flex-col gap-1.5 text-sm text-gray-500">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                {{ $booking->hall->city->name ?? __('booking.city_not_available') }},
                                {{ $booking->hall->address ?? __('booking.address_not_available') }}
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $booking->hall->owner->phone ?? 'N/A' }}
                            </span>
                        </div>
                        <a href="{{ route('customer.halls.show', $booking->hall->slug ?? 'default-slug') }}"
                           class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-[#B9916D] hover:text-[#8a6a4f] transition-colors">
                            {{ __('booking.view_hall_details') }}
                            <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Booking Information --}}
            <div class="p-6 bg-white shadow-sm rounded-xl">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('booking.booking_information') }}
                </h3>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs text-gray-400">{{ __('booking.booking_number_label') }}</p>
                        <p class="font-mono font-medium text-gray-900">{{ $booking->booking_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('booking.event_date') }}</p>
                        <p class="font-medium text-gray-900">{{ $booking->booking_date->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('booking.time_slot') }}</p>
                        <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $booking->time_slot)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('booking.number_of_guests') }}</p>
                        <p class="font-medium text-gray-900">{{ $booking->number_of_guests }}</p>
                    </div>
                    @if($booking->event_type)
                        <div>
                            <p class="text-xs text-gray-400">{{ __('booking.event_type') }}</p>
                            <p class="font-medium text-gray-900">{{ ucfirst($booking->event_type) }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400">{{ __('booking.booked_on') }}</p>
                        <p class="font-medium text-gray-900">{{ $booking->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                @if($booking->customer_notes)
                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <p class="mb-1 text-xs text-gray-400">{{ __('booking.special_notes') }}</p>
                        <p class="text-gray-700">{{ $booking->customer_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Customer Information --}}
            <div class="p-6 bg-white shadow-sm rounded-xl">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('booking.customer_information') }}
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs text-gray-400">{{ __('booking.name') }}</p>
                        <p class="font-medium text-gray-900">{{ $booking->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('booking._email') }}</p>
                        <p class="font-medium text-gray-900 truncate">{{ $booking->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ __('booking.phone') }}</p>
                        <p class="font-medium text-gray-900">{{ $booking->customer_phone }}</p>
                    </div>
                </div>
            </div>

            {{-- Extra Services --}}
            @if($booking->extraServices && $booking->extraServices->count() > 0)
                <div class="p-6 bg-white shadow-sm rounded-xl">
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                        {{ __('booking.extra_services') }}
                    </h3>
                    <div class="flex flex-col divide-y divide-gray-100">
                        @foreach($booking->extraServices as $service)
                            <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $service->pivot->service_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $service->pivot->quantity }} &times;
                                        {{ number_format($service->pivot->unit_price, 3) }} {{ __('booking.currency') }}
                                    </p>
                                </div>
                                <span class="font-semibold text-[#B9916D]">
                                    {{ number_format($service->pivot->total_price, 3) }} {{ __('booking.currency') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Sidebar ───────────────────────────────────────── --}}
        <div class="flex flex-col gap-6 lg:col-span-1">

            {{-- Payment Summary --}}
            <div class="p-6 bg-white shadow-sm rounded-xl">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('booking.payment_summary') }}
                </h3>

                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ __('booking.hall_price') }}</span>
                        <span class="font-medium text-gray-900">{{ number_format($booking->hall_price, 3) }} {{ __('booking.currency') }}</span>
                    </div>

                    @if($booking->services_price > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">{{ __('booking.extra_services_price') }}</span>
                            <span class="font-medium text-gray-900">{{ number_format($booking->services_price, 3) }} {{ __('booking.currency') }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ __('booking.subtotal') }}</span>
                        <span class="font-medium text-gray-900">{{ number_format($booking->subtotal, 3) }} {{ __('booking.currency') }}</span>
                    </div>

                    @if($booking->platform_fee > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">
                                {{ __('booking.platform_fee') }}
                                @if($booking->service_fee_type === 'percentage' && $booking->service_fee_value)
                                    <span class="text-xs text-gray-400">({{ number_format($booking->service_fee_value, 0) }}%)</span>
                                @endif
                            </span>
                            <span class="font-medium text-gray-900">{{ number_format($booking->platform_fee, 3) }} {{ __('booking.currency') }}</span>
                        </div>
                    @endif

                    <div class="h-px bg-gray-100"></div>

                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-900">{{ __('booking.total_amount') }}</span>
                        <span class="text-2xl font-bold text-[#B9916D]">
                            {{ number_format($booking->total_amount, 3) }} {{ __('booking.currency') }}
                        </span>
                    </div>
                </div>

                {{-- Payment Status --}}
                <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
                    <span class="text-sm text-gray-500">{{ __('booking.payment_status') }}</span>
                    @php
                        $paymentClass = match($booking->payment_status) {
                            'paid'    => 'bg-green-100 text-green-800',
                            'pending' => 'bg-amber-100 text-amber-800',
                            default   => 'bg-red-100 text-red-800',
                        };
                        $paymentLabel = match($booking->payment_status) {
                            'paid'    => __('booking.payment_status_paid'),
                            'pending' => __('booking.payment_status_pending'),
                            default   => __('booking.payment_status_failed'),
                        };
                    @endphp
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $paymentClass }}">
                        {{ $paymentLabel }}
                    </span>
                </div>

                @if($booking->payment_status === 'pending')
                    <a href="{{ route('customer.booking.payment', $booking) }}"
                       class="block w-full px-6 py-3 mt-4 text-sm font-semibold text-center text-white transition-colors bg-[#B9916D] rounded-xl hover:bg-[#a07d5e]">
                        {{ __('booking.complete_payment') }}
                    </a>
                @endif
            </div>

            {{-- Actions --}}
            <div class="p-6 bg-white shadow-sm rounded-xl">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('booking._actions') }}
                </h3>
                <div class="flex flex-col gap-2">
                    @if(in_array($booking->status, ['pending', 'confirmed']))
                        <button onclick="confirmCancel()"
                                class="w-full px-4 py-2 text-sm font-medium text-red-500 transition-colors border border-red-300 rounded-lg hover:bg-red-50">
                            {{ __('booking.cancel_booking') }}
                        </button>
                    @endif

                    <a href="{{ route('customer.halls.show', $booking->hall->slug ?? '404') }}"
                       class="block px-4 py-2 text-sm font-medium text-center text-gray-700 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50">
                        {{ __('booking.view_hall_details_btn') }}
                    </a>

                    <a href="{{ route('customer.bookings') }}"
                       class="block px-4 py-2 text-sm font-medium text-center text-gray-700 transition-colors border border-gray-200 rounded-lg hover:bg-gray-50">
                        {{ __('booking.back_to_all_bookings') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ─── Cancel Modal ────────────────────────────────────── --}}
<div x-data="{ showModal: false }" x-cloak>
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showModal" @click="showModal = false"
                 class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
            <div x-show="showModal"
                 class="relative w-full max-w-md p-6 bg-white rounded-xl shadow-xl text-start">
                <h3 class="mb-2 text-base font-semibold text-gray-900">
                    {{ __('booking.cancel_booking_title') }}
                </h3>
                <p class="mb-6 text-sm text-gray-500">{{ __('booking.cancel_booking_confirmation') }}</p>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button @click="showModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        {{ __('booking.keep_booking') }}
                    </button>
                    <form action="{{ route('customer.booking.cancel', $booking) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            {{ __('booking.confirm_cancel') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmCancel() {
    document.querySelector('[x-data]').__x.$data.showModal = true;
}
</script>
@endpush
@endsection
