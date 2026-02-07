{{--
    Guest Booking - Step 3: Complete Booking Form

    This view displays the full booking form where verified guests can select
    date, time slot, number of guests, and additional services.

    @var Hall $hall The hall being booked
    @var GuestSession $guestSession The verified guest session
--}}

@extends('layouts.customer')

@section('title', __('guest.page_title_form') . ' - ' . $hall->getTranslation('name', app()->getLocale()))

@section('content')
<div class="min-h-screen py-8 bg-gray-50" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">

        {{-- Progress Steps --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse">
                {{-- Step 1: Guest Info (Completed) --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                </div>

                <div class="w-12 h-0.5 bg-green-500"></div>

                {{-- Step 2: Verify (Completed) --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                </div>

                <div class="w-12 h-0.5 bg-green-500"></div>

                {{-- Step 3: Booking (Active) --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white rounded-full bg-primary-600">
                        3
                    </span>
                    <span class="text-sm font-medium ms-2 text-primary-600">{{ __('guest.step_3_booking') }}</span>
                </div>

                <div class="w-12 h-0.5 bg-gray-300"></div>

                {{-- Step 4: Payment --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-600 bg-gray-300 rounded-full">
                        4
                    </span>
                    <span class="text-sm font-medium text-gray-500 ms-2">{{ __('guest.step_4_payment') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Main Form --}}
            <div class="lg:col-span-2">
                <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('guest.step_3_booking') }}</h2>
                    </div>

                    <div class="p-6">
                        {{-- Alert Messages --}}
                        @if(session('error'))
                            <div class="px-4 py-3 mb-6 text-red-700 border border-red-200 rounded-lg bg-red-50">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Guest Info Summary --}}
                        <div class="p-4 mb-6 border border-blue-200 rounded-lg bg-blue-50">
                            <h3 class="mb-2 font-medium text-blue-900">{{ __('Your Information') }}</h3>
                            <div class="space-y-1 text-sm text-blue-800">
                                <p><span class="font-medium">{{ __('Name') }}:</span> {{ $guestSession->name }}</p>
                                <p><span class="font-medium">{{ __('Email') }}:</span> {{ $guestSession->email }}</p>
                                <p><span class="font-medium">{{ __('Phone') }}:</span> {{ $guestSession->phone }}</p>
                            </div>
                        </div>

                        {{-- Booking Form --}}
                        <form
                            method="POST"
                            action="{{ route('guest.booking.store', ['hall' => $hall->slug, 'lang' => app()->getLocale()]) }}"
                            x-data="bookingForm()"
                            @submit="handleSubmit"
                        >
                            @csrf

                            {{-- Date Selection --}}
                            <div class="mb-5">
                                <label for="booking_date" class="block mb-1 text-sm font-medium text-gray-700">
                                    {{ __('Booking Date') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="booking_date"
                                    name="booking_date"
                                    x-model="bookingDate"
                                    @change="checkAvailability()"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    value="{{ old('booking_date') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('booking_date') border-red-500 @enderror"
                                    required
                                >
                                @error('booking_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Time Slot Selection --}}
                            <div class="mb-5">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    {{ __('Time Slot') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    @php
                                        $timeSlots = [
                                            'morning' => ['label' => __('halls.morning'), 'icon' => '🌅'],
                                            'afternoon' => ['label' => __('halls.afternoon'), 'icon' => '☀️'],
                                            'evening' => ['label' => __('halls.evening'), 'icon' => '🌙'],
                                            'full_day' => ['label' => __('halls.full_day'), 'icon' => '📅'],
                                        ];
                                    @endphp

                                    @foreach($timeSlots as $value => $slot)
                                        <label class="relative">
                                            <input
                                                type="radio"
                                                name="time_slot"
                                                value="{{ $value }}"
                                                x-model="timeSlot"
                                                @change="checkAvailability()"
                                                class="sr-only peer"
                                                {{ old('time_slot') === $value ? 'checked' : '' }}
                                            >
                                            <div class="p-3 text-center transition border-2 rounded-lg cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:bg-gray-50">
                                                <div class="mb-1 text-xl">{{ $slot['icon'] }}</div>
                                                <div class="text-sm font-medium">{{ $slot['label'] }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('time_slot')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                {{-- Availability Status --}}
                                <div x-show="availabilityMessage" class="mt-3">
                                    <p
                                        :class="isAvailable ? 'text-green-600' : 'text-red-600'"
                                        class="flex items-center text-sm"
                                    >
                                        <span x-show="isAvailable">✓</span>
                                        <span x-show="!isAvailable">✗</span>
                                        <span class="ms-1" x-text="availabilityMessage"></span>
                                    </p>
                                </div>
                            </div>

                            {{-- Number of Guests --}}
                            <div class="mb-5">
                                <label for="number_of_guests" class="block mb-1 text-sm font-medium text-gray-700">
                                    {{ __('Number of Guests') }} <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="number_of_guests"
                                    name="number_of_guests"
                                    min="{{ $hall->capacity_min }}"
                                    max="{{ $hall->capacity_max }}"
                                    value="{{ old('number_of_guests', $hall->capacity_min) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('number_of_guests') border-red-500 @enderror"
                                    required
                                >
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Capacity') }}: {{ $hall->capacity_min }} - {{ $hall->capacity_max }} {{ __('guests') }}
                                </p>
                                @error('number_of_guests')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Event Type --}}
                            <div class="mb-5">
                                <label for="event_type" class="block mb-1 text-sm font-medium text-gray-700">
                                    {{ __('Event Type') }}
                                </label>
                                <select
                                    id="event_type"
                                    name="event_type"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                >
                                    <option value="">{{ __('Select event type') }}</option>
                                    <option value="wedding" {{ old('event_type') === 'wedding' ? 'selected' : '' }}>{{ __('Wedding') }}</option>
                                    <option value="corporate" {{ old('event_type') === 'corporate' ? 'selected' : '' }}>{{ __('Corporate Event') }}</option>
                                    <option value="birthday" {{ old('event_type') === 'birthday' ? 'selected' : '' }}>{{ __('Birthday') }}</option>
                                    <option value="conference" {{ old('event_type') === 'conference' ? 'selected' : '' }}>{{ __('Conference') }}</option>
                                    <option value="graduation" {{ old('event_type') === 'graduation' ? 'selected' : '' }}>{{ __('Graduation') }}</option>
                                    <option value="other" {{ old('event_type') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                </select>
                            </div>

                            {{-- Extra Services --}}
                            @if($hall->activeExtraServices->count() > 0)
                                <div class="mb-5">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        {{ __('Additional Services') }}
                                    </label>
                                    <div class="space-y-3">
                                        @foreach($hall->activeExtraServices as $service)
                                            <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                                <input
                                                    type="checkbox"
                                                    name="services[]"
                                                    value="{{ $service->id }}"
                                                    x-model="selectedServices"
                                                    class="mt-1 border-gray-300 rounded text-primary-600 focus:ring-primary-500"
                                                    {{ in_array($service->id, old('services', [])) ? 'checked' : '' }}
                                                >
                                                <div class="flex-1 ms-3">
                                                    <div class="flex justify-between">
                                                        <span class="font-medium text-gray-900">
                                                            {{ $service->getTranslation('name', app()->getLocale()) }}
                                                        </span>
                                                        <span class="font-medium text-primary-600">
                                                            {{ number_format($service->price, 3) }} {{ __('OMR') }}
                                                        </span>
                                                    </div>
                                                    @if($service->description)
                                                        <p class="text-sm text-gray-500">
                                                            {{ $service->getTranslation('description', app()->getLocale()) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Customer Notes --}}
                            <div class="mb-5">
                                <label for="customer_notes" class="block mb-1 text-sm font-medium text-gray-700">
                                    {{ __('Special Requests / Notes') }}
                                </label>
                                <textarea
                                    id="customer_notes"
                                    name="customer_notes"
                                    rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="{{ __('Any special requirements or notes for your booking...') }}"
                                >{{ old('customer_notes') }}</textarea>
                            </div>

                            {{-- Terms Agreement --}}
                            <div class="mb-6">
                                <label class="flex items-start">
                                    <input
                                        type="checkbox"
                                        name="agree_terms"
                                        x-model="agreeTerms"
                                        class="mt-1 border-gray-300 rounded text-primary-600 focus:ring-primary-500"
                                        required
                                    >
                                    <span class="text-sm text-gray-600 ms-2">
                                        {{ __('I agree to the') }}
                                        <a href="#" class="text-primary-600 hover:underline">{{ __('Terms & Conditions') }}</a>
                                        {{ __('and') }}
                                        <a href="#" class="text-primary-600 hover:underline">{{ __('Cancellation Policy') }}</a>
                                    </span>
                                </label>
                                @error('agree_terms')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Submit Button --}}
                            <button
                                type="submit"
                                :disabled="!isAvailable || !agreeTerms || isSubmitting"
                                class="w-full px-4 py-3 font-semibold text-white transition rounded-lg bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed"
                            >
                                <span x-show="!isSubmitting">{{ __('guest.btn_submit_booking') }}</span>
                                <span x-show="isSubmitting">{{ __('Processing...') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Booking Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky overflow-hidden bg-white shadow-sm rounded-xl top-4">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">{{ __('Booking Summary') }}</h3>
                    </div>

                    <div class="p-4">
                        {{-- Hall Info --}}
                        <div class="flex items-start mb-4 space-x-3 rtl:space-x-reverse">
                            @if($hall->featured_image)
                                <img
                                    src="{{ Storage::url($hall->featured_image) }}"
                                    alt="{{ $hall->getTranslation('name', app()->getLocale()) }}"
                                    class="object-cover w-16 h-16 rounded-lg"
                                >
                            @endif
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $hall->getTranslation('name', app()->getLocale()) }}</h4>
                                <p class="text-sm text-gray-500">{{ $hall->city?->getTranslation('name', app()->getLocale()) }}</p>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Price Breakdown --}}
                        <div class="space-y-2 text-sm" x-data="{ hallPrice: {{ $hall->price_per_slot }} }">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Hall Rental') }}</span>
                                <span class="font-medium">{{ number_format($hall->price_per_slot, 3) }} {{ __('OMR') }}</span>
                            </div>

                            <template x-for="serviceId in selectedServices" :key="serviceId">
                                <div class="flex justify-between text-gray-600">
                                    <span x-text="getServiceName(serviceId)"></span>
                                    <span x-text="getServicePrice(serviceId) + ' {{ __('OMR') }}'"></span>
                                </div>
                            </template>

                            <hr class="my-2">

                            <div class="flex justify-between text-base font-semibold">
                                <span>{{ __('Total') }}</span>
                                <span class="text-primary-600" x-text="calculateTotal() + ' {{ __('OMR') }}'">
                                    {{ number_format($hall->price_per_slot, 3) }} {{ __('OMR') }}
                                </span>
                            </div>
                        </div>

                        {{-- Advance Payment Note --}}
                        @if($hall->allows_advance_payment)
                            <div class="p-3 mt-4 rounded-lg bg-blue-50">
                                <p class="text-sm text-blue-800">
                                    <strong>{{ __('Note') }}:</strong> {{ __('This hall allows advance payment of') }}
                                    {{ $hall->advance_percentage }}%.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@php
    // Prepare services data for JavaScript (avoiding arrow function in @json)
    $servicesForJs = $hall->activeExtraServices->map(function($s) {
        return [
            'id' => $s->id,
            'name' => $s->getTranslation('name', app()->getLocale()),
            'price' => $s->price
        ];
    })->toArray();
@endphp

@push('scripts')
<script>
function bookingForm() {
    return {
        bookingDate: '{{ old('booking_date') }}',
        timeSlot: '{{ old('time_slot') }}',
        selectedServices: @json(old('services', [])),
        agreeTerms: false,
        isAvailable: false,
        availabilityMessage: '',
        isSubmitting: false,
        services: @json($servicesForJs),
        hallPrice: {{ $hall->price_per_slot }},

        async checkAvailability() {
            if (!this.bookingDate || !this.timeSlot) {
                this.availabilityMessage = '';
                return;
            }

            try {
                const response = await fetch('{{ route('guest.check-availability') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        hall_id: {{ $hall->id }},
                        date: this.bookingDate,
                        time_slot: this.timeSlot
                    })
                });

                const data = await response.json();
                this.isAvailable = data.available;
                this.availabilityMessage = data.message;
            } catch (error) {
                this.isAvailable = false;
                this.availabilityMessage = '{{ __("Error checking availability") }}' + ': ' + error.message;
            }
        },

        getServiceName(id) {
            const service = this.services.find(s => s.id == id);
            return service ? service.name : '';
        },

        getServicePrice(id) {
            const service = this.services.find(s => s.id == id);
            return service ? parseFloat(service.price).toFixed(3) : '0.000';
        },

        calculateTotal() {
            let total = this.hallPrice;
            this.selectedServices.forEach(id => {
                const service = this.services.find(s => s.id == id);
                if (service) {
                    total += parseFloat(service.price);
                }
            });
            return total.toFixed(3);
        },

        handleSubmit(e) {
            if (!this.isAvailable || !this.agreeTerms) {
                e.preventDefault();
                return;
            }
            this.isSubmitting = true;
        }
    }
}
</script>
@endpush

@endsection

