<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('halls.payment') }} - {{ $booking->booking_number }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Tajawal', sans-serif; }

        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .payment-option {
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .payment-option input:checked + div {
            border-color: #0284c7;
            background-color: #f0f9ff;
        }

        @keyframes pulse-border {
            0%, 100% { border-color: #0284c7; }
            50% { border-color: #0ea5e9; }
        }

        .pulse-border {
            animation: pulse-border 2s ease-in-out infinite;
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50">

    <!-- Top Navigation -->
    <nav class="sticky top-0 z-50 border-b border-gray-200 glass-morphism">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                   class="flex items-center gap-2 text-gray-700 transition hover:text-gray-900">
                    <svg class="w-6 h-6 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="font-medium">{{ __('halls.back_to_home') }}</span>
                </a>

                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl">
                        <span class="text-xl font-bold text-white">م</span>
                    </div>
                    <span class="hidden text-xl font-bold text-gray-800 sm:inline">Majalis</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container px-4 py-6 mx-auto md:py-8">
        <div class="max-w-4xl mx-auto">

            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-primary-100">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h1 class="mb-2 text-2xl font-bold text-gray-900 md:text-3xl">{{ __('halls.complete_payment') }}</h1>
                <p class="text-gray-600">{{ __('halls.secure_payment_process') }}</p>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- Payment Options -->
                <div class="space-y-6 lg:col-span-2">

                    <!-- Booking Reference -->
                    <div class="p-6 bg-white border-2 shadow-sm rounded-2xl border-primary-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="mb-1 text-sm text-gray-600">{{ __('halls.booking_reference') }}</div>
                                <div class="text-xl font-bold text-gray-900">{{ $booking->booking_number }}</div>
                            </div>
                            <div class="px-4 py-2 text-sm font-semibold rounded-lg bg-amber-100 text-amber-800">
                                {{ __('halls.payment_pending') }}
                            </div>
                        </div>
                    </div>

                    <!-- ✅ NEW: Advance Payment Notice (if applicable) -->
                    @if($booking->isAdvancePayment())
                    <div class="p-6 border-2 border-blue-200 shadow-sm bg-blue-50 rounded-2xl">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="mb-2 text-lg font-bold text-blue-900">
                                    {{ __('halls.advance_payment_required') }}
                                </h3>
                                <p class="mb-4 text-sm text-blue-800">
                                    {{ __('halls.advance_payment_description') }}
                                </p>

                                <div class="p-4 bg-white border border-blue-200 rounded-lg">
                                    <div class="space-y-3">
                                        <!-- Amount to Pay NOW -->
                                        <div class="flex items-center justify-between pb-3 border-b border-blue-200">
                                            <span class="font-semibold text-gray-700">
                                                {{ __('halls.pay_now_advance') }}
                                            </span>
                                            <span class="text-2xl font-bold text-blue-600">
                                                {{ number_format($booking->advance_amount, 3) }} {{ __('halls.currency') }}
                                            </span>
                                        </div>

                                        <!-- Balance Due Later -->
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">
                                                {{ __('halls.balance_due_before_event') }}
                                            </span>
                                            <span class="text-lg font-semibold text-gray-800">
                                                {{ number_format($booking->balance_due, 3) }} {{ __('halls.currency') }}
                                            </span>
                                        </div>

                                        <!-- Total for Reference -->
                                        <div class="flex items-center justify-between pt-3 border-t border-blue-200">
                                            <span class="text-sm font-semibold text-gray-700">
                                                {{ __('halls.total_booking_amount') }}
                                            </span>
                                            <span class="text-lg font-semibold text-gray-900">
                                                {{ number_format($booking->total_amount, 3) }} {{ __('halls.currency') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Important Notice -->
                                <div class="flex items-start gap-2 p-3 mt-4 border rounded-lg bg-amber-50 border-amber-200">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm text-amber-800">
                                        <span class="font-semibold">{{ __('halls.important') }}:</span>
                                        {{ __('halls.balance_payment_reminder') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Payment Methods -->
                    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl md:p-8">
                        <h2 class="flex items-center gap-2 mb-6 text-xl font-bold text-gray-900">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            {{ __('halls.select_payment_method') }}
                        </h2>

                        <form action="{{ route('customer.booking.process-payment', ['booking' => $booking->id]) }}" method="POST" id="paymentForm">
                            @csrf
                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                            <div class="space-y-4">

                                <!-- Online Payment (Thawani) -->
                                <label class="block cursor-pointer payment-option">
                                    <input type="radio" name="payment_method" value="online" class="sr-only peer" required checked>
                                    <div class="p-6 transition border-2 border-gray-200 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50">
                                        <div class="flex items-start gap-4">
                                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="mb-1 font-bold text-gray-900">{{ __('halls.online_payment') }}</div>
                                                <div class="mb-3 text-sm text-gray-600">{{ __('halls.pay_with_card_wallet') }}</div>
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg">💳 {{ __('halls.credit_card') }}</span>
                                                    <span class="px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg">🏦 {{ __('halls.debit_card') }}</span>
                                                    <span class="px-3 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg">📱 {{ __('halls.digital_wallet') }}</span>
                                                </div>
                                            </div>
                                            {{-- <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-full peer-checked:border-primary-600 peer-checked:bg-primary-600">
                                                    <svg class="hidden w-4 h-4 text-white peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </label>

                                <!-- Bank Transfer -->
                                <label class="block cursor-pointer payment-option">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="sr-only peer">
                                    <div class="p-6 transition border-2 border-gray-200 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50">
                                        <div class="flex items-start gap-4">
                                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="mb-1 font-bold text-gray-900">{{ __('halls.bank_transfer') }}</div>
                                                <div class="text-sm text-gray-600">{{ __('halls.bank_transfer_desc') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Cash on Venue -->
                                <label class="block cursor-pointer payment-option">
                                    <input type="radio" name="payment_method" value="cash" class="sr-only peer">
                                    <div class="p-6 transition border-2 border-gray-200 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50">
                                        <div class="flex items-start gap-4">
                                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="mb-1 font-bold text-gray-900">{{ __('halls.cash_payment') }}</div>
                                                <div class="text-sm text-gray-600">{{ __('halls.pay_at_venue') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Security Notice -->
                            <div class="flex items-start gap-3 p-4 mt-6 border border-green-200 bg-green-50 rounded-xl">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <div class="text-sm text-green-800">
                                    <div class="mb-1 font-semibold">{{ __('halls.secure_transaction') }}</div>
                                    <div>{{ __('halls.payment_security_message') }}</div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="flex items-center justify-center w-full gap-2 mt-6 font-bold text-white transition shadow-lg h-14 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('halls.proceed_to_payment') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Booking Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky p-6 bg-white border border-gray-200 shadow-lg top-24 rounded-2xl">
                        <h3 class="mb-4 text-lg font-bold text-gray-900">{{ __('halls.booking_summary') }}</h3>

                        <!-- Hall Info -->
                        <div class="mb-6">
                            <div class="h-32 mb-3 overflow-hidden bg-gray-200 rounded-xl">
                                @if($booking->hall->featured_image)
                                    <img src="{{ asset('storage/' . $booking->hall->featured_image) }}" class="object-cover w-full h-full">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-100 to-primary-200"></div>
                                @endif
                            </div>
                            <h4 class="font-semibold text-gray-900">
                                {{ is_array($booking->hall->name) ? ($booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en']) : $booking->hall->name }}
                            </h4>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ is_array($booking->hall->city->name) ? ($booking->hall->city->name[app()->getLocale()] ?? $booking->hall->city->name['en']) : $booking->hall->city->name }}
                            </p>
                        </div>

                        <!-- Booking Details -->
                        <div class="py-4 space-y-3 border-t border-gray-200">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-600">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-600">{{ __('halls.' . $booking->time_slot) }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-gray-600">{{ $booking->number_of_guests }} {{ __('halls.guests_count') }}</span>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="py-4 space-y-3 border-t border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('halls.hall_price') }}</span>
                                <span class="font-medium text-gray-900">{{ number_format($booking->hall_price, 3) }} OMR</span>
                            </div>
                            @if($booking->services_price > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('halls.extra_services_total') }}</span>
                                <span class="font-medium text-gray-900">{{ number_format($booking->services_price, 3) }} OMR</span>
                            </div>
                            @endif
                            @if($booking->platform_fee > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ __('halls.platform_fee') }}</span>
                                <span class="font-medium text-gray-900">{{ number_format($booking->platform_fee, 3) }} OMR</span>
                            </div>
                            @endif
                        </div>

                        <!-- Total -->
                        <div class="pt-4 border-t-2 border-gray-300">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900">{{ __('halls.total') }}</span>
                                <span class="text-2xl font-bold text-primary-600">{{ number_format($booking->total_amount, 3) }} OMR</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layouts.footer')
</body>
</html>
