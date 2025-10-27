@extends('customer.layout')

@section('title', 'My Bookings - HallBooking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Bookings</h1>
            <p class="text-gray-600">View and manage all your hall bookings</p>
        </div>
        <a href="{{ route('customer.halls.index') }}" 
            class="mt-4 md:mt-0 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium">
            Book New Hall
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('customer.bookings') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- To Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Submit -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-900 text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings List -->
    @if($bookings->count() > 0)
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <!-- Booking Info -->
                            <div class="flex-1">
                                <div class="flex items-start space-x-4">
                                    <!-- Hall Image -->
                                    <div class="flex-shrink-0">
                                        @if($booking->hall->main_image)
                                            <img src="{{ Storage::url($booking->hall->main_image) }}" 
                                                alt="{{ $booking->hall->name }}" 
                                                class="w-24 h-24 object-cover rounded-lg">
                                        @else
                                            <div class="w-24 h-24 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg"></div>
                                        @endif
                                    </div>

                                    <!-- Details -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                    {{ $booking->hall->name }}
                                                </h3>
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    </svg>
                                                    {{ $booking->hall->city->name }}
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

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
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

                                        <div class="mt-3 flex items-center space-x-4">
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
                            <div class="mt-4 md:mt-0 md:ml-6 flex flex-col space-y-2">
                                <a href="{{ route('customer.booking.details', $booking) }}" 
                                    class="text-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                                    View Details
                                </a>
                                
                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                    <button onclick="confirmCancel({{ $booking->id }})"
                                        class="text-center border border-red-500 text-red-500 px-4 py-2 rounded-lg hover:bg-red-50 transition text-sm font-medium">
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
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Bookings Found</h3>
            <p class="text-gray-600 mb-6">You haven't made any bookings yet. Start exploring our halls!</p>
            <a href="{{ route('customer.halls.index') }}" 
                class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium">
                Browse Halls
            </a>
        </div>
    @endif
</div>

<!-- Cancel Confirmation Modal -->
<div x-data="{ showModal: false, bookingId: null }" x-cloak>
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

            <div x-show="showModal" x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Booking</h3>
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
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Yes, Cancel Booking
                        </button>
                    </form>
                    <button type="button" @click="showModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
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
