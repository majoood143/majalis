{{--
    Guest Booking - Payment Page
    
    Displays payment options and booking summary before redirecting
    to payment gateway (Thawani).
    
    @var Booking $booking The booking to pay for
--}}

@extends('layouts.customer')

@section('title', __('guest.page_title_payment') . ' - ' . $booking->booking_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Progress Steps --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse">
                @for($i = 1; $i <= 3; $i++)
                    <div class="flex items-center">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                    </div>
                    @if($i < 3)
                        <div class="w-12 h-0.5 bg-green-500"></div>
                    @endif
                @endfor
                
                <div class="w-12 h-0.5 bg-green-500"></div>
                
                {{-- Step 4: Payment (Active) --}}
                <div class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-600 text-white text-sm font-medium">
                        4
                    </span>
                    <span class="ms-2 text-sm font-medium text-primary-600">{{ __('guest.step_4_payment') }}</span>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                {{ session('info') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Payment Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Payment Method') }}</h2>
                    </div>

                    <div class="p-6">
                        <form 
                            method="POST" 
                            action="{{ route('guest.booking.process-payment', ['guest_token' => $booking->guest_token]) }}"
                            x-data="{ 
                                paymentType: '{{ $booking->hall->allows_advance_payment ? 'advance' : 'full' }}',
                                isSubmitting: false 
                            }"
                        >
                            @csrf

                            {{-- Payment Type Selection (if hall allows advance payment) --}}
                            @if($booking->hall->allows_advance_payment)
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        {{ __('Select Payment Option') }}
                                    </label>
                                    
                                    <div class="space-y-3">
                                        {{-- Full Payment Option --}}
                                        <label class="relative block">
                                            <input 
                                                type="radio" 
                                                name="payment_type" 
                                                value="full"
                                                x-model="paymentType"
                                                class="peer sr-only"
                                            >
                                            <div class="p-4 border-2 rounded-lg cursor-pointer transition peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:bg-gray-50">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="font-medium text-gray-900">{{ __('Full Payment') }}</span>
                                                        <p class="text-sm text-gray-500 mt-1">
                                                            {{ __('Pay the full amount now') }}
                                                        </p>
                                                    </div>
                                                    <span class="text-lg font-bold text-primary-600">
                                                        {{ number_format($booking->total_amount, 3) }} {{ __('OMR') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>

                                        {{-- Advance Payment Option --}}
                                        <label class="relative block">
                                            <input 
                                                type="radio" 
                                                name="payment_type" 
                                                value="advance"
                                                x-model="paymentType"
                                                class="peer sr-only"
                                            >
                                            <div class="p-4 border-2 rounded-lg cursor-pointer transition peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:bg-gray-50">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="font-medium text-gray-900">{{ __('Advance Payment') }}</span>
                                                        <p class="text-sm text-gray-500 mt-1">
                                                            {{ __('Pay :percentage% now, rest before event', ['percentage' => $booking->hall->advance_percentage]) }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        @php
                                                            $advanceAmount = $booking->total_amount * ($booking->hall->advance_percentage / 100);
                                                            $balanceDue = $booking->total_amount - $advanceAmount;
                                                        @endphp
                                                        <span class="text-lg font-bold text-primary-600">
                                                            {{ number_format($advanceAmount, 3) }} {{ __('OMR') }}
                                                        </span>
                                                        <p class="text-xs text-gray-500">
                                                            {{ __('Balance due') }}: {{ number_format($balanceDue, 3) }} {{ __('OMR') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="payment_type" value="full">
                            @endif

                            {{-- Payment Gateway Info --}}
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center mb-3">
                                    <svg class="w-6 h-6 text-primary-600 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">{{ __('Secure Payment via Thawani') }}</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    {{ __('You will be redirected to Thawani secure payment gateway to complete your payment.') }}
                                </p>
                                <div class="flex items-center gap-3 mt-3">
                                    <img src="{{ asset('images/payment/visa.svg') }}" alt="Visa" class="h-8">
                                    <img src="{{ asset('images/payment/mastercard.svg') }}" alt="Mastercard" class="h-8">
                                    <img src="{{ asset('images/payment/thawani.svg') }}" alt="Thawani" class="h-8">
                                </div>
                            </div>

                            {{-- Terms --}}
                            <div class="mb-6">
                                <label class="flex items-start">
                                    <input 
                                        type="checkbox" 
                                        name="agree_payment_terms" 
                                        required
                                        class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                    >
                                    <span class="ms-2 text-sm text-gray-600">
                                        {{ __('I understand that my booking will be confirmed upon successful payment.') }}
                                        <a href="#" class="text-primary-600 hover:underline">{{ __('View Cancellation Policy') }}</a>
                                    </span>
                                </label>
                            </div>

                            {{-- Submit Button --}}
                            <button 
                                type="submit"
                                :disabled="isSubmitting"
                                @click="isSubmitting = true"
                                class="w-full py-4 px-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center"
                            >
                                <svg x-show="!isSubmitting" class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <svg x-show="isSubmitting" class="animate-spin w-5 h-5 me-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-show="!isSubmitting">
                                    {{ __('guest.btn_pay_now') }} - 
                                    <span x-text="paymentType === 'advance' ? '{{ number_format($advanceAmount ?? $booking->total_amount, 3) }}' : '{{ number_format($booking->total_amount, 3) }}'"></span>
                                    {{ __('OMR') }}
                                </span>
                                <span x-show="isSubmitting">{{ __('Redirecting to payment...') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Booking Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-4">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">{{ __('Booking Summary') }}</h3>
                    </div>

                    <div class="p-4">
                        {{-- Hall Info --}}
                        <div class="flex items-start space-x-3 rtl:space-x-reverse mb-4">
                            @if($booking->hall->featured_image)
                                <img 
                                    src="{{ Storage::url($booking->hall->featured_image) }}" 
                                    alt="{{ $booking->hall->getTranslation('name', app()->getLocale()) }}"
                                    class="w-16 h-16 rounded-lg object-cover"
                                >
                            @endif
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $booking->hall->getTranslation('name', app()->getLocale()) }}</h4>
                                <p class="text-sm text-gray-500">{{ $booking->hall->city?->getTranslation('name', app()->getLocale()) }}</p>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Booking Details --}}
                        <div class="space-y-2 text-sm mb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Booking #') }}</span>
                                <span class="font-medium">{{ $booking->booking_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Date') }}</span>
                                <span>{{ $booking->booking_date->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Time') }}</span>
                                <span>
                                    @php
                                        $timeSlotLabels = [
                                            'morning' => __('Morning'),
                                            'afternoon' => __('Afternoon'),
                                            'evening' => __('Evening'),
                                            'full_day' => __('Full Day'),
                                        ];
                                    @endphp
                                    {{ $timeSlotLabels[$booking->time_slot] ?? $booking->time_slot }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Guests') }}</span>
                                <span>{{ $booking->number_of_guests }}</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Price Breakdown --}}
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Hall Rental') }}</span>
                                <span>{{ number_format($booking->hall_price, 3) }} {{ __('OMR') }}</span>
                            </div>
                            
                            @if($booking->services_price > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('Services') }}</span>
                                    <span>{{ number_format($booking->services_price, 3) }} {{ __('OMR') }}</span>
                                </div>
                            @endif

                            @if($booking->platform_fee > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('Platform Fee') }}</span>
                                    <span>{{ number_format($booking->platform_fee, 3) }} {{ __('OMR') }}</span>
                                </div>
                            @endif

                            <hr class="my-2">

                            <div class="flex justify-between text-base font-semibold">
                                <span>{{ __('Total') }}</span>
                                <span class="text-primary-600">{{ number_format($booking->total_amount, 3) }} {{ __('OMR') }}</span>
                            </div>
                        </div>

                        {{-- Customer Info --}}
                        <hr class="my-4">
                        
                        <div class="text-sm">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Customer') }}</h4>
                            <p class="text-gray-600">{{ $booking->customer_name }}</p>
                            <p class="text-gray-600">{{ $booking->customer_email }}</p>
                            <p class="text-gray-600">{{ $booking->customer_phone }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
