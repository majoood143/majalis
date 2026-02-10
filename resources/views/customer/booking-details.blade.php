@extends('customer.layout')

@section('title', 'Booking #' . $booking->booking_number . ' - majalis')

@section('content')
<div class="max-w-5xl px-4 py-8 mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4 text-sm text-gray-600">
            <a href="{{ route('customer.bookings') }}" class="hover:text-indigo-600">My Bookings</a>
            <span class="mx-2">/</span>
            <span>Booking #{{ $booking->booking_number }}</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="mb-2 text-3xl font-bold text-gray-900">Booking Details</h1>
                <p class="text-gray-600">{{ $booking->booking_date->format('F d, Y') }} • {{ ucfirst($booking->time_slot) }}</p>
            </div>
            <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full
                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                @else bg-red-100 text-red-800
                @endif">
                {{ ucfirst($booking->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Hall Information -->
            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="flex items-start p-6">
                    <div class="flex-shrink-0 mr-6">
                        @if($booking->hall->featured_image ?? false)
                            <img src="{{ Storage::url($booking->hall->featured_image) }}"
                                alt="{{ $booking->hall->name ?? 'Hall Image' }}"
                                class="object-cover w-32 h-32 rounded-lg">
                        @else
                            <div class="w-32 h-32 rounded-lg bg-gradient-to-br from-indigo-400 to-purple-500"></div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h2 class="mb-2 text-2xl font-bold text-gray-900">{{ $booking->hall->name ?? 'Hall Name Not Available' }}</h2>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                {{ $booking->hall->city->name ?? 'City Not Available' }}, {{ $booking->hall->address ?? 'Address Not Available' }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $booking->hall->owner->phone ?? 'N/A' }}
                            </div>
                        </div>
                        <a href="{{ route('customer.halls.show', $booking->hall->slug ?? 'default-slug') }}"
                            class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            View Hall Details →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Booking Information -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-4 text-lg font-semibold">Booking Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Booking Number</span>
                        <div class="font-mono font-medium">{{ $booking->booking_number }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Event Date</span>
                        <div class="font-medium">{{ $booking->booking_date->format('F d, Y') }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Time Slot</span>
                        <div class="font-medium">{{ ucfirst(str_replace('_', ' ', $booking->time_slot)) }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Number of Guests</span>
                        <div class="font-medium">{{ $booking->number_of_guests }}</div>
                    </div>
                    @if($booking->event_type)
                        <div>
                            <span class="text-sm text-gray-600">Event Type</span>
                            <div class="font-medium">{{ ucfirst($booking->event_type) }}</div>
                        </div>
                    @endif
                    <div>
                        <span class="text-sm text-gray-600">Booked On</span>
                        <div class="font-medium">{{ $booking->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                @if($booking->customer_notes)
                    <div class="pt-4 mt-4 border-t">
                        <span class="text-sm text-gray-600">Special Notes</span>
                        <div class="mt-1 text-gray-900">{{ $booking->customer_notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Customer Information -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-4 text-lg font-semibold">Customer Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <span class="text-sm text-gray-600">Name</span>
                        <div class="font-medium">{{ $booking->customer_name }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Email</span>
                        <div class="font-medium">{{ $booking->customer_email }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Phone</span>
                        <div class="font-medium">{{ $booking->customer_phone }}</div>
                    </div>
                </div>
            </div>

            <!-- Extra Services -->
            @if($booking->extraServices && $booking->extraServices->count() > 0)
                <div class="p-6 bg-white rounded-lg shadow-md">
                    <h3 class="mb-4 text-lg font-semibold">Extra Services</h3>
                    <div class="space-y-3">
                        @foreach($booking->extraServices as $service)
                            <div class="flex items-center justify-between py-2 border-b last:border-0">
                                <div>
                                    <div class="font-medium">{{ $service->pivot->service_name }}</div>
                                    <div class="text-sm text-gray-600">
                                        {{ $service->pivot->quantity }} × {{ number_format($service->pivot->unit_price, 3) }} OMR
                                    </div>
                                </div>
                                <div class="font-semibold text-indigo-600">
                                    {{ number_format($service->pivot->total_price, 3) }} OMR
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Payment Summary -->
            <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-4 text-lg font-semibold">Payment Summary</h3>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Hall Price</span>
                        <span class="font-medium">{{ number_format($booking->hall_price, 3) }} OMR</span>
                    </div>

                    @if($booking->services_price > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Extra Services</span>
                            <span class="font-medium">{{ number_format($booking->services_price, 3) }} OMR</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">{{ number_format($booking->subtotal, 3) }} OMR</span>
                    </div>

                    @if($booking->commission_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Commission</span>
                            <span class="font-medium">{{ number_format($booking->commission_amount, 3) }} OMR</span>
                        </div>
                    @endif

                    <div class="pt-3 border-t">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold">Total Amount</span>
                            <span class="text-2xl font-bold text-indigo-600">{{ number_format($booking->total_amount, 3) }} OMR</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Status -->
                <div class="pt-6 mt-6 border-t">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Payment Status</span>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                            @if($booking->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($booking->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                </div>

                @if($booking->payment_status === 'pending')
                    <a href="{{ route('customer.booking.payment', $booking) }}"
                        class="block w-full px-6 py-3 mt-4 font-semibold text-center text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Complete Payment
                    </a>
                @endif
            </div>

            <!-- Actions -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-4 text-lg font-semibold">Actions</h3>
                <div class="space-y-3">
                    @if(in_array($booking->status, ['pending', 'confirmed']))
                        <button onclick="confirmCancel()"
                            class="w-full px-4 py-2 text-sm font-medium text-red-500 transition border border-red-500 rounded-lg hover:bg-red-50">
                            Cancel Booking
                        </button>
                    @endif

                    <a href="{{ route('customer.halls.show', $booking->hall->slug ?? '404') }}"
                        class="block px-4 py-2 text-sm font-medium text-center text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                        View Hall Details
                    </a>

                    <a href="{{ route('customer.bookings') }}"
                        class="block px-4 py-2 text-sm font-medium text-center text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                        Back to All Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div x-data="{ showModal: false }" x-cloak>
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showModal" @click="showModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div x-show="showModal" class="relative w-full max-w-md p-6 bg-white rounded-lg">
                <h3 class="mb-4 text-lg font-semibold">Cancel Booking</h3>
                <p class="mb-6 text-gray-600">Are you sure you want to cancel this booking? This action cannot be undone.</p>
                <div class="flex space-x-3">
                    <form action="{{ route('customer.booking.cancel', $booking) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Yes, Cancel
                        </button>
                    </form>
                    <button @click="showModal = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        No, Keep It
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmCancel() {
    const modal = document.querySelector('[x-data]').__x.$data;
    modal.showModal = true;
}
</script>
@endpush
@endsection
