@extends('customer.layout')

@section('title', 'My Bookings - Majalis')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex flex-col mb-8 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="mb-2 text-3xl font-bold text-gray-900">My Bookings</h1>
            <p class="text-gray-600">View and manage all your hall bookings</p>
        </div>
        <a href="{{ route('customer.halls.index') }}"
            class="px-6 py-3 mt-4 font-medium text-white transition bg-indigo-600 rounded-lg md:mt-0 hover:bg-indigo-700">
            Book New Hall
        </a>
    </div>

    <!-- Filters -->
    <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
        <form action="{{ route('customer.bookings') }}" method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <!-- Status Filter -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- From Date -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- To Date -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Submit -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-2 text-white transition bg-gray-900 rounded-lg hover:bg-gray-800">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings List -->
    @if($bookings->count() > 0)
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <div class="overflow-hidden transition bg-white rounded-lg shadow-md hover:shadow-lg">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <!-- Booking Info -->
                            <div class="flex-1">
                                <div class="flex items-start space-x-4">
                                    <!-- Hall Image -->
                                    <div class="flex-shrink-0">
                                        @if($booking->hall->featured_image ?? false)
                                            <img src="{{ Storage::url($booking->hall->featured_image) }}"
                                                alt="{{ $booking->hall->name ?? 'Hall Image' }}"
                                                class="object-cover w-24 h-24 rounded-lg">
                                        @else
                                            <div class="w-24 h-24 rounded-lg bg-gradient-to-br from-indigo-400 to-purple-500"></div>
                                        @endif
                                    </div>

                                    <!-- Details -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h3 class="mb-1 text-lg font-semibold text-gray-900">
                                                    {{ $booking->hall->name ?? 'Unnamed Hall' }}
                                                </h3>
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    </svg>
                                                    {{ $booking->hall->city->name ?? 'Unknown City' }}
                                                </div>
                                            </div>
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-3">
                                            <div>
                                                <span class="text-gray-600">Booking Date:</span>
                                                <div class="font-medium">{{ $booking->booking_date->format('M d, Y') }}</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Time Slot:</span>
                                                <div class="font-medium">{{ ucfirst($booking->time_slot) }}</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Guests:</span>
                                                <div class="font-medium">{{ $booking->number_of_guests }}</div>
                                            </div>
                                        </div>

                                        <div class="flex items-center mt-3 space-x-4">
                                            <div class="text-sm">
                                                <span class="text-gray-600">Booking #:</span>
                                                <span class="font-mono font-medium">{{ $booking->booking_number }}</span>
                                            </div>
                                            <div class="text-sm">
                                                <span class="text-gray-600">Total:</span>
                                                <span class="font-bold text-indigo-600">{{ number_format($booking->total_amount, 3) }} OMR</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col mt-4 space-y-2 md:mt-0 md:ml-6">
                                <a href="{{ route('customer.booking.details', $booking) }}"
                                    class="px-4 py-2 text-sm font-medium text-center text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                    View Details
                                </a>

                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                    <button onclick="confirmCancel({{ $booking->id }})"
                                        class="px-4 py-2 text-sm font-medium text-center text-red-500 transition border border-red-500 rounded-lg hover:bg-red-50">
                                        Cancel Booking
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="p-12 text-center bg-white rounded-lg shadow-md">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mb-2 text-xl font-semibold text-gray-900">No Bookings Found</h3>
            <p class="mb-6 text-gray-600">You haven't made any bookings yet. Start exploring our halls!</p>
            <a href="{{ route('customer.halls.index') }}"
                class="inline-block px-6 py-3 font-medium text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Browse Halls
            </a>
        </div>
    @endif
</div>

<!-- Cancel Confirmation Modal -->
<div x-data="{ showModal: false, bookingId: null }" x-cloak>
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Cancel Booking</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to cancel this booking? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form :action="`/bookings/${bookingId}/cancel`" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Yes, Cancel Booking
                        </button>
                    </form>
                    <button type="button" @click="showModal = false"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        No, Keep It
                    </button>
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
