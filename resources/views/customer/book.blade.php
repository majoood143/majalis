@extends('customer.layout')

@section('title', 'Book ' . $hall->name . ' - HallBooking')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Book {{ $hall->name }}</h1>
        <p class="text-gray-600">Complete the form below to make your reservation</p>
    </div>

    <form action="{{ route('customer.booking.store', $hall) }}" method="POST" 
        x-data="bookingForm()" @submit="validateForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Booking Details</h2>
                    
                    <!-- Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Event Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="booking_date" 
                            x-model="bookingDate"
                            @change="checkAvailability()"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('booking_date') border-red-500 @enderror">
                        @error('booking_date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p x-show="availabilityMessage" 
                            :class="isAvailable ? 'text-green-600' : 'text-red-600'"
                            class="mt-1 text-sm" x-text="availabilityMessage"></p>
                    </div>

                    <!-- Time Slot -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Time Slot <span class="text-red-500">*</span>
                        </label>
                        <select name="time_slot" 
                            x-model="timeSlot"
                            @change="checkAvailability(); calculateTotal()"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('time_slot') border-red-500 @enderror">
                            <option value="">Select time slot</option>
                            <option value="morning">Morning (8 AM - 12 PM)</option>
                            <option value="afternoon">Afternoon (12 PM - 5 PM)</option>
                            <option value="evening">Evening (5 PM - 11 PM)</option>
                            <option value="full_day">Full Day (8 AM - 11 PM)</option>
                        </select>
                        @error('time_slot')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Number of Guests -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Guests <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="number_of_guests" 
                            x-model="numberOfGuests"
                            min="{{ $hall->capacity_min }}" 
                            max="{{ $hall->capacity_max }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('number_of_guests') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-600">
                            Capacity: {{ $hall->capacity_min }} - {{ $hall->capacity_max }} guests
                        </p>
                        @error('number_of_guests')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Event Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Event Type
                        </label>
                        <select name="event_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select event type</option>
                            <option value="wedding">Wedding</option>
                            <option value="corporate">Corporate Event</option>
                            <option value="birthday">Birthday Party</option>
                            <option value="conference">Conference</option>
                            <option value="graduation">Graduation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Your Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_name" 
                                value="{{ old('customer_name', Auth::user()->name) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('customer_name') border-red-500 @enderror">
                            @error('customer_name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="customer_email" 
                                value="{{ old('customer_email', Auth::user()->email) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('customer_email') border-red-500 @enderror">
                            @error('customer_email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="customer_phone" 
                                value="{{ old('customer_phone', Auth::user()->phone) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('customer_phone') border-red-500 @enderror">
                            @error('customer_phone')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes
                        </label>
                        <textarea name="customer_notes" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="Any special requests or requirements...">{{ old('customer_notes') }}</textarea>
                    </div>
                </div>

                <!-- Extra Services -->
                @if($hall->activeExtraServices->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">Extra Services</h2>
                        
                        @foreach($hall->activeExtraServices as $service)
                            <div class="flex items-center justify-between py-3 border-b last:border-0">
                                <div class="flex items-start space-x-3 flex-1">
                                    <input type="checkbox" 
                                        x-model="selectedServices"
                                        :value="{{ $service->id }}"
                                        @change="calculateTotal()"
                                        class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <div class="font-medium">{{ $service->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $service->description }}</div>
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="font-semibold text-indigo-600">{{ number_format($service->price, 3) }} OMR</div>
                                    <div class="text-xs text-gray-500">{{ $service->unit }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                    <h3 class="text-lg font-semibold mb-4">Booking Summary</h3>
                    
                    <!-- Hall Info -->
                    <div class="mb-4 pb-4 border-b">
                        <div class="text-sm text-gray-600">Hall</div>
                        <div class="font-medium">{{ $hall->name }}</div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span>Hall Price</span>
                            <span class="font-medium">{{ number_format($hall->price_per_slot, 3) }} OMR</span>
                        </div>
                        <div x-show="selectedServices.length > 0" class="flex justify-between text-sm">
                            <span>Extra Services</span>
                            <span class="font-medium" x-text="servicesTotal.toFixed(3) + ' OMR'"></span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="pt-4 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-2xl font-bold text-indigo-600" x-text="total.toFixed(3) + ' OMR'"></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                        :disabled="!isAvailable"
                        :class="isAvailable ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="w-full mt-6  px-6 py-3 rounded-lg font-semibold transition">
                        Confirm Booking
                    </button>

                    <!-- Terms -->
                    <p class="text-xs text-gray-500 mt-4">
                        By confirming this booking, you agree to our terms and conditions.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function bookingForm() {
    return {
        bookingDate: '',
        timeSlot: '',
        numberOfGuests: {{ $hall->capacity_min }},
        selectedServices: [],
        hallPrice: {{ $hall->price_per_day }},
        servicesTotal: 0,
        total: {{ $hall->price_per_day }},
        isAvailable: true,
        availabilityMessage: '',

        async checkAvailability() {
            if (!this.bookingDate || !this.timeSlot) return;

            try {
                const response = await fetch('/api/check-availability', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        hall_id: {{ $hall->id }},
                        booking_date: this.bookingDate,
                        time_slot: this.timeSlot
                    })
                });

                const data = await response.json();
                this.isAvailable = data.available;
                this.availabilityMessage = data.message;
            } catch (error) {
                console.error('Error checking availability:', error);
            }
        },

        calculateTotal() {
            this.servicesTotal = 0;
            // Add services calculation logic here
            this.total = this.hallPrice + this.servicesTotal;
        },

        validateForm(e) {
            if (!this.isAvailable) {
                e.preventDefault();
                alert('Please select an available date and time slot.');
            }
        }
    }
}
</script>
@endpush
@endsection
