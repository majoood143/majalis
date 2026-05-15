@extends('customer.layout')

@section('title', __('booking.my_bookings') . ' - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

    {{-- ─── Header ─────────────────────────────────────────── --}}
    <div class="relative mb-8 overflow-hidden bg-[#B9916D] rounded-2xl">
        <div class="absolute inset-0 opacity-10"
             style="background-image: url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\");">
        </div>
        <div class="relative flex flex-col gap-4 px-6 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-white sm:text-3xl">{{ __('booking.my_bookings') }}</h1>
                <p class="text-[#e4c9b5]">{{ __('booking.view_and_manage') }}</p>
            </div>
            <a href="{{ route('customer.halls.index') }}"
               class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-[#B9916D] bg-white rounded-xl hover:bg-[#f5ede6] transition-colors shrink-0">
                {{ __('booking.book_new_hall') }}
            </a>
        </div>
    </div>

    {{-- ─── Filters ─────────────────────────────────────────── --}}
    <div class="p-6 mb-6 bg-white shadow-sm rounded-xl">
        <form action="{{ route('customer.bookings') }}" method="GET"
              class="grid grid-cols-1 gap-4 md:grid-cols-4">

            <div>
                <label class="block mb-1.5 text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('booking.status') }}
                </label>
                <select name="status"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B9916D] focus:border-transparent bg-gray-50">
                    <option value="">{{ __('booking.all_statuses') }}</option>
                    <option value="pending"   {{ request('status') == 'pending'    ? 'selected' : '' }}>{{ __('booking.status_pending') }}</option>
                    <option value="confirmed" {{ request('status') == 'confirmed'  ? 'selected' : '' }}>{{ __('booking.status_confirmed') }}</option>
                    <option value="completed" {{ request('status') == 'completed'  ? 'selected' : '' }}>{{ __('booking.status_completed') }}</option>
                    <option value="cancelled" {{ request('status') == 'cancelled'  ? 'selected' : '' }}>{{ __('booking.status_cancelled') }}</option>
                </select>
            </div>

            <div>
                <label class="block mb-1.5 text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('booking.from_date') }}
                </label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B9916D] focus:border-transparent bg-gray-50">
            </div>

            <div>
                <label class="block mb-1.5 text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    {{ __('booking.to_date') }}
                </label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#B9916D] focus:border-transparent bg-gray-50">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-5 py-2 text-sm font-semibold text-white transition-colors bg-gray-800 rounded-lg hover:bg-gray-900">
                    {{ __('booking.apply_filters') }}
                </button>
            </div>

        </form>
    </div>

    {{-- ─── Bookings List ───────────────────────────────────── --}}
    @if($bookings->count() > 0)
        <div class="flex flex-col gap-4">
            @foreach($bookings as $booking)
                <div class="overflow-hidden bg-white border-s-4 border-[#B9916D] shadow-sm rounded-xl hover:shadow-md transition-shadow">
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                            {{-- Booking info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-4">

                                    {{-- Hall image --}}
                                    <div class="shrink-0">
                                        @if($booking->hall->featured_image ?? false)
                                            <img src="{{ Storage::url($booking->hall->featured_image) }}"
                                                 alt="{{ $booking->hall->name ?? __('booking.hall_image') }}"
                                                 class="object-cover w-20 h-20 rounded-xl">
                                        @else
                                            <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-[#B9916D] to-[#E8D5C4]"></div>
                                        @endif
                                    </div>

                                    {{-- Details --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                            <div>
                                                <h3 class="text-base font-semibold text-gray-900">
                                                    {{ $booking->hall->name ?? __('booking.unnamed_hall') }}
                                                </h3>
                                                <div class="flex items-center gap-1 mt-0.5 text-sm text-gray-500">
                                                    <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    </svg>
                                                    {{ $booking->hall->city->name ?? __('booking.unknown_city') }}
                                                </div>
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
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold shrink-0 {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-3 gap-3 text-sm">
                                            <div>
                                                <p class="text-xs text-gray-400">{{ __('booking.booking_date') }}</p>
                                                <p class="font-medium text-gray-800">{{ $booking->booking_date->format('M d, Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">{{ __('booking.time_slot') }}</p>
                                                <p class="font-medium text-gray-800">{{ ucfirst($booking->time_slot) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">{{ __('booking.guests') }}</p>
                                                <p class="font-medium text-gray-800">{{ $booking->number_of_guests }}</p>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 mt-3 pt-3 border-t border-gray-100 text-sm">
                                            <div>
                                                <span class="text-gray-400">{{ __('booking.booking_number_label') }}: </span>
                                                <span class="font-mono font-medium text-gray-700">{{ $booking->booking_number }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">{{ __('booking.total') }}: </span>
                                                <span class="font-bold text-[#B9916D]">{{ number_format($booking->total_amount, 3) }} {{ __('booking.currency') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-row gap-2 md:flex-col md:items-stretch md:ms-4 shrink-0">
                                <a href="{{ route('customer.booking.details', $booking) }}"
                                   class="px-4 py-2 text-sm font-medium text-center text-white transition-colors bg-[#B9916D] rounded-lg hover:bg-[#a07d5e]">
                                    {{ __('booking.view_details') }}
                                </a>
                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                    <button onclick="confirmCancel({{ $booking->id }})"
                                            class="px-4 py-2 text-sm font-medium text-center text-red-500 transition-colors border border-red-300 rounded-lg hover:bg-red-50">
                                        {{ __('booking.cancel_booking') }}
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $bookings->links() }}
        </div>

    @else
        {{-- ─── Empty State ─────────────────────────────────── --}}
        <div class="flex flex-col items-center justify-center px-6 py-20 text-center bg-white shadow-sm rounded-xl">
            <div class="flex items-center justify-center w-20 h-20 mb-5 bg-[#f5ede6] rounded-full">
                <svg class="w-10 h-10 text-[#d4b49f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="mb-2 text-xl font-semibold text-gray-900">{{ __('booking.no_bookings_found') }}</h3>
            <p class="mb-6 text-gray-500">{{ __('booking.no_bookings_message') }}</p>
            <a href="{{ route('customer.halls.index') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 font-medium text-white transition-colors bg-[#B9916D] rounded-xl hover:bg-[#a07d5e]">
                {{ __('booking.browse_halls') }}
            </a>
        </div>
    @endif

</div>

{{-- ─── Cancel Confirmation Modal ───────────────────────── --}}
<div x-data="{ showModal: false, bookingId: null }" x-cloak>
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-500/75" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full px-4 pt-5 pb-4 overflow-hidden text-start align-bottom transition-all transform bg-white rounded-xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:p-6">

                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center shrink-0 w-10 h-10 mx-auto bg-red-100 rounded-full sm:mx-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 sm:mt-0 sm:ms-4">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('booking.cancel_booking_title') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('booking.cancel_booking_confirmation') }}</p>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 mt-5 sm:flex-row sm:justify-end">
                    <button type="button" @click="showModal = false"
                            class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#B9916D] sm:w-auto">
                        {{ __('booking.keep_booking') }}
                    </button>
                    <form :action="`/bookings/${bookingId}/cancel`" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 sm:w-auto">
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
function confirmCancel(bookingId) {
    Alpine.store('cancelModal', { showModal: true, bookingId: bookingId });
}
</script>
@endpush
@endsection
