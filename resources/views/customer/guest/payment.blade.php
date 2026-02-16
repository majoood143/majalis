{{--
    Guest Booking - Payment Page (FIXED VERSION)

    Displays payment options and booking summary before redirecting
    to payment gateway (Thawani).

    @var Booking $booking The booking to pay for
--}}

@extends('layouts.customer')

@section('title', __('guest.page_title_payment') . ' - ' . $booking->booking_number)

@section('content')
    <div class="min-h-screen py-8 bg-gray-50" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">

            {{-- Progress Steps --}}
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 text-white bg-green-500 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                        </div>
                        @if ($i < 3)
                            <div class="w-12 h-0.5 bg-green-500"></div>
                        @endif
                    @endfor

                    <div class="w-12 h-0.5 bg-green-500"></div>

                    {{-- Step 4: Payment (Active) --}}
                    <div class="flex items-center">
                        <span
                            class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white rounded-full bg-primary-600">
                            4
                        </span>
                        <span class="text-sm font-medium ms-2 text-primary-600">{{ __('guest.step_4_payment') }}</span>
                    </div>
                </div>
            </div>

            {{-- Alert Messages --}}
            @if (session('error'))
                <div class="px-4 py-3 mb-6 text-red-700 border border-red-200 rounded-lg bg-red-50">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2 rtl:ml-2 rtl:mr-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="px-4 py-3 mb-6 text-yellow-700 border border-yellow-200 rounded-lg bg-yellow-50">
                    {{ session('warning') }}
                </div>
            @endif

            @if (session('info'))
                <div class="px-4 py-3 mb-6 text-blue-700 border border-blue-200 rounded-lg bg-blue-50">
                    {{ session('info') }}
                </div>
            @endif

            @if (session('success'))
                <div class="px-4 py-3 mb-6 text-green-700 border border-green-200 rounded-lg bg-green-50">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Payment Form --}}
                <div class="lg:col-span-2">
                    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('payment.method') }}</h2>
                        </div>

                        <div class="p-6">
                            {{--
                            IMPORTANT: Form submits to guest.booking.process-payment
                            This route is defined in guest-booking.php
                        --}}
                            <form id="payment-form" method="POST"
                                action="{{ route('guest.booking.process-payment', ['guest_token' => $booking->guest_token]) }}">
                                @csrf

                                {{-- Hidden field to track submission --}}
                                <input type="hidden" name="_submitted" value="1">

                                @php
                                    $allowsAdvance = $booking->hall && $booking->hall->allows_advance_payment;
                                    $advancePercentage = $booking->hall->advance_percentage ?? 50;
                                    $advanceAmount = $booking->total_amount * ($advancePercentage / 100);
                                    $balanceDue = $booking->total_amount - $advanceAmount;
                                @endphp

                                {{-- Payment Type Selection (if hall allows advance payment) --}}
                                @if ($allowsAdvance)
                                    <div class="mb-6">
                                        <label class="block mb-3 text-sm font-medium text-gray-700">
                                            {{ __('payment.select_option') }}
                                        </label>

                                        <div class="space-y-3">
                                            {{-- Full Payment Option --}}
                                            <label class="relative block cursor-pointer">
                                                <input type="radio" name="payment_type" value="full"
                                                    class="sr-only peer" checked>
                                                <div
                                                    class="p-4 transition border-2 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:bg-gray-50">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <span
                                                                class="font-medium text-gray-900">{{ __('payment.full') }}</span>
                                                            <p class="mt-1 text-sm text-gray-500">
                                                                {{ __('payment.full_description') }}
                                                            </p>
                                                        </div>
                                                        <span class="text-lg font-bold text-primary-600">
                                                            {{ number_format($booking->total_amount, 3) }}
                                                            <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                                class="inline w-6 h-6 -mt-1">
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>

                                            {{-- Advance Payment Option --}}
                                            <label class="relative block cursor-pointer">
                                                <input type="radio" name="payment_type" value="advance"
                                                    class="sr-only peer">
                                                <div
                                                    class="p-4 transition border-2 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:bg-gray-50">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <span
                                                                class="font-medium text-gray-900">{{ __('payment.advance') }}</span>
                                                            <p class="mt-1 text-sm text-gray-500">
                                                                {{ __('payment.advance_description', ['percentage' => $advancePercentage]) }}
                                                            </p>
                                                        </div>
                                                        <div class="text-right">
                                                            <span class="text-lg font-bold text-primary-600">
                                                                {{ number_format($advanceAmount, 3) }}
                                                                <img src="{{ asset('images/Medium.svg') }}"
                                                                    alt="Omani Riyal" class="inline w-6 h-6 -mt-1">
                                                            </span>
                                                            <p class="text-xs text-gray-500">
                                                                {{ __('payment.balance') }}:
                                                                {{ number_format($balanceDue, 3) }}

                                                                <img src="{{ asset('images/Medium.svg') }}"
                                                                    alt="Omani Riyal" class="inline w-6 h-6 -mt-1">
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @else
                                    {{-- Hidden field for full payment when advance not allowed --}}
                                    <input type="hidden" name="payment_type" value="full">

                                    <div class="p-4 mb-6 rounded-lg bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-900">{{ __('payment.total_amount') }}</span>
                                            <span class="text-xl font-bold text-primary-600">
                                                {{ number_format($booking->total_amount, 3) }}
                                                <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                    class="inline w-6 h-6 -mt-1">
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Payment Gateway Info --}}
                                <div class="p-4 mb-6 border border-blue-100 rounded-lg bg-blue-50">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-500 mt-0.5 me-3 rtl:ms-3 rtl:me-0 flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="text-sm text-blue-700">
                                            <p class="font-medium">{{ __('payment.secure') }}</p>
                                            <p class="mt-1">
                                                {{ __('payment.redirect_message') }}
                                            </p>
                                            <div class="flex items-center gap-3 mt-3">
                                                <img src="{{ asset('images/payment/visa.svg') }}" alt="Visa"
                                                    class="h-8">
                                                <img src="{{ asset('images/payment/mastercard.svg') }}" alt="Mastercard"
                                                    class="h-8">
                                                <img src="{{ asset('images/payment/thawani.svg') }}" alt="Thawani"
                                                    class="h-8">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Terms Checkbox --}}
                                <div class="mb-6">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" name="agree_terms" id="agree_terms" required
                                            class="mt-1 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-600 ms-2 rtl:me-2 rtl:ms-0">
                                            {{ __('payment.terms_agreement') }}
                                            <a href="{{ route('pages.terms') }}" target="_blank"
                                                class="text-primary-600 hover:underline">{{ __('payment.view_terms') }}</a>
                                        </span>
                                    </label>
                                    @error('agree_terms')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Submit Button --}}
                                <button type="submit" id="pay-button"
                                    class="flex items-center justify-center w-full px-4 py-4 font-semibold text-white transition rounded-lg bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <svg id="pay-icon" class="w-5 h-5 me-2 rtl:ms-2 rtl:me-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                    <svg id="pay-spinner" class="hidden w-5 h-5 animate-spin me-2 rtl:ms-2 rtl:me-0"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span id="pay-text">
                                        {{ __('payment.pay_now') }} - {{ number_format($booking->total_amount, 3) }}
                                         <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal" class="inline w-6 h-6 -mt-1">
                                    </span>
                                    <span id="pay-loading-text" class="hidden">{{ __('payment.redirecting') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Booking Summary Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="sticky overflow-hidden bg-white shadow-sm rounded-xl top-4">
                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-semibold text-gray-900">{{ __('booking.summary') }}</h3>
                        </div>

                        <div class="p-4">
                            {{-- Hall Info --}}
                            <div class="flex items-start mb-4 space-x-3 rtl:space-x-reverse">
                                @if ($booking->hall && $booking->hall->featured_image)
                                    <img src="{{ Storage::url($booking->hall->featured_image) }}"
                                        alt="{{ $booking->hall->getTranslation('name', app()->getLocale()) }}"
                                        class="object-cover w-16 h-16 rounded-lg">
                                @endif
                                <div>
                                    <h4 class="font-medium text-gray-900">
                                        {{ $booking->hall?->getTranslation('name', app()->getLocale()) ?? __('booking.hall') }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $booking->hall?->city?->getTranslation('name', app()->getLocale()) }}</p>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Booking Details --}}
                            <div class="mb-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('booking.number') }}</span>
                                    <span class="font-mono font-medium">{{ $booking->booking_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('booking.date') }}</span>
                                    <span>{{ $booking->booking_date->format(__('date.format')) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('booking.time') }}</span>
                                    <span>
                                        @php
                                            $timeSlotLabels = [
                                                'morning' => __('booking.morning'),
                                                'afternoon' => __('booking.afternoon'),
                                                'evening' => __('booking.evening'),
                                                'full_day' => __('booking.full_day'),
                                            ];
                                        @endphp
                                        {{ $timeSlotLabels[$booking->time_slot] ?? $booking->time_slot }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('booking.guests') }}</span>
                                    <span>{{ $booking->number_of_guests }}</span>
                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Price Breakdown --}}
                            {{-- <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('payment.hall_rental') }}</span>
                                    <span>{{ number_format($booking->hall_price, 3) }} {{ __('currency.omr') }}</span>
                                </div>

                                @if ($booking->services_price > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('payment.services') }}</span>
                                        <span>{{ number_format($booking->services_price, 3) }} {{ __('currency.omr') }}</span>
                                    </div>
                                @endif

                                @if ($booking->platform_fee > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('payment.platform_fee') }}</span>
                                        <span>{{ number_format($booking->platform_fee, 3) }} {{ __('currency.omr') }}</span>
                                    </div>
                                @endif

                                <hr class="my-2">

                                <div class="flex justify-between text-base font-semibold">
                                    <span>{{ __('payment.total') }}</span>
                                    <span class="text-primary-600">{{ number_format($booking->total_amount, 3) }}
                                        {{ __('currency.omr') }}</span>
                                </div>
                            </div> --}}

                            <div class="space-y-2 text-sm">
                                {{-- Hall Rental --}}
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('halls.hall_rental') }}</span>
                                    <span>{{ number_format((float) $booking->hall_price, 3) }} <img
                                            src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                            class="inline w-6 h-6 -mt-1"></span>
                                </div>

                                {{-- Services --}}
                                @if ((float) $booking->services_price > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('halls.services') }}</span>
                                        <span>{{ number_format((float) $booking->services_price, 3) }}
                                            <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                class="inline w-6 h-6 -mt-1"></span>
                                    </div>
                                @endif

                                {{-- ✅ FIX: Platform Fee (shows only when fee > 0) --}}
                                @if ((float) $booking->platform_fee > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('halls.platform_fee') }}
                                            @if ($booking->commission_type === 'percentage')
                                                <span
                                                    class="text-xs text-gray-400">({{ $booking->commission_value }}%)</span>
                                            @endif
                                        </span>
                                        <span>{{ number_format((float) $booking->platform_fee, 3) }}
                                            <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                class="inline w-6 h-6 -mt-1"></span>
                                    </div>
                                @endif

                                <hr class="my-2">

                                {{-- Total --}}
                                <div class="flex justify-between text-base font-semibold">
                                    <span>{{ __('halls.total') }}</span>
                                    <span class="text-primary-600">{{ number_format((float) $booking->total_amount, 3) }}
                                        <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                            class="inline w-6 h-6 -mt-1"></span>
                                </div>

                                {{-- ✅ FIX: Advance Payment Details (if applicable) --}}
                                @if ($booking->isAdvancePayment())
                                    <hr class="my-2">
                                    <div class="flex justify-between font-medium text-green-700">
                                        <span>{{ __('halls.pay_now') }}</span>
                                        <span>{{ number_format((float) $booking->advance_amount, 3) }}
                                            <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                class="inline w-6 h-6 -mt-1"></span>
                                    </div>
                                    @if ((float) $booking->balance_due > 0)
                                        <div class="flex justify-between text-orange-600">
                                            <span>{{ __('halls.balance_due') }}</span>
                                            <span>{{ number_format((float) $booking->balance_due, 3) }}
                                                <img src="{{ asset('images/Medium.svg') }}" alt="Omani Riyal"
                                                    class="inline w-6 h-6 -mt-1"></span>
                                        </div>
                                    @endif

                                    {{-- Info: platform fee included in advance --}}
                                    @if ((float) $booking->platform_fee > 0)
                                        <p class="mt-2 text-xs text-gray-500">
                                            {{ __('halls.platform_fee_included_in_advance') }}
                                        </p>
                                    @endif
                                @endif
                            </div>






                            {{-- Customer Info --}}
                            <hr class="my-4">

                            <div class="text-sm">
                                <h4 class="mb-2 font-medium text-gray-900">{{ __('booking.customer') }}</h4>
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

    {{-- JavaScript for form handling --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('payment-form');
            const payButton = document.getElementById('pay-button');
            const payIcon = document.getElementById('pay-icon');
            const paySpinner = document.getElementById('pay-spinner');
            const payText = document.getElementById('pay-text');
            const payLoadingText = document.getElementById('pay-loading-text');
            const agreeTerms = document.getElementById('agree_terms');

            // Handle form submission
            form.addEventListener('submit', function(e) {
                // Check if terms are agreed
                if (!agreeTerms.checked) {
                    e.preventDefault();
                    alert('{{ __('payment.terms_required') }}');
                    return false;
                }

                // Disable button and show loading state
                payButton.disabled = true;
                payIcon.classList.add('hidden');
                paySpinner.classList.remove('hidden');
                payText.classList.add('hidden');
                payLoadingText.classList.remove('hidden');

                // Let the form submit naturally - it will redirect to payment gateway
                console.log('Form submitting to:', form.action);

                return true;
            });

            // Update displayed amount based on payment type selection
            const paymentTypeInputs = document.querySelectorAll('input[name="payment_type"]');
            paymentTypeInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    const isAdvance = this.value === 'advance';
                    const amount = isAdvance ?
                        '{{ number_format($advanceAmount ?? $booking->total_amount, 3) }}' :
                        '{{ number_format($booking->total_amount, 3) }}';

                    payText.innerHTML = '{{ __('payment.pay_now') }} - ' + amount +
                        ' {{ __('currency.omr') }}';
                });
            });
        });
    </script>
@endsection
