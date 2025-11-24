<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('halls.booking_success') }} - Majalis</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Tajawal', sans-serif;
        }

        @keyframes checkmark {
            0% {
                stroke-dashoffset: 100;
            }

            100% {
                stroke-dashoffset: 0;
            }
        }

        .checkmark {
            stroke-dasharray: 100;
            animation: checkmark 0.5s ease-in-out 0.5s forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4 bg-gradient-to-br from-green-50 to-blue-50">

    <div class="w-full max-w-2xl">
        <!-- Success Icon -->
        <div class="mb-8 text-center fade-in-up">
            <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 bg-green-100 rounded-full">
                <svg class="w-16 h-16" viewBox="0 0 52 52">
                    <circle class="checkmark" cx="26" cy="26" r="24" fill="none" stroke="#22c55e"
                        stroke-width="4" />
                    <path class="checkmark" fill="none" stroke="#22c55e" stroke-width="4" d="M14 27l8 8 16-16" />
                </svg>
            </div>
            <h1 class="mb-2 text-3xl font-bold text-gray-900 md:text-4xl">{{ __('halls.booking_success') }}</h1>
            <p class="text-lg text-gray-600">{{ __('halls.booking_confirmed') }}</p>
        </div>

        <!-- Booking Card -->
        <div class="p-6 mb-6 bg-white shadow-xl rounded-2xl md:p-8 fade-in-up" style="animation-delay: 0.2s;">
            <!-- Reference Number -->
            <div class="p-6 mb-6 -mx-2 text-gray-900 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl">
                <div class="mb-1 text-sm opacity-90">{{ __('halls.booking_reference') }}</div>
                <div class="text-2xl font-bold tracking-wider">{{ $booking->booking_number }}</div>
            </div>

            <!-- Hall Info -->
            <div class="flex items-center gap-4 pb-6 mb-6 border-b border-gray-200">
                <div class="flex-shrink-0 w-20 h-20 overflow-hidden bg-gray-200 rounded-xl">
                    @if ($booking->hall->featured_image)
                        <img src="{{ asset('storage/' . $booking->hall->featured_image) }}"
                            class="object-cover w-full h-full">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary-100 to-primary-200"></div>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ is_array($booking->hall->name) ? $booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] : $booking->hall->name }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ is_array($booking->hall->city->name) ? $booking->hall->city->name[app()->getLocale()] ?? $booking->hall->city->name['en'] : $booking->hall->city->name }}
                    </p>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="mb-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ __('halls.event_date') }}
                    </span>
                    <span
                        class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('halls.time_slot') }}
                    </span>
                    <span class="font-semibold text-gray-900">{{ __('halls.' . $booking->time_slot) }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        {{ __('halls.number_of_guests') }}
                    </span>
                    <span class="font-semibold text-gray-900">{{ $booking->number_of_guests }}</span>
                </div>
            </div>

            <!-- Pricing -->
            <div class="p-6 bg-gray-50 rounded-xl">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ __('halls.hall_price') }}</span>
                        <span class="font-medium">{{ number_format($booking->hall_price, 3) }} OMR</span>
                    </div>
                    @if ($booking->services_total > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ __('halls.extra_services_total') }}</span>
                            <span class="font-medium">{{ number_format($booking->services_total, 3) }} OMR</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ __('halls.tax') }}</span>
                        <span class="font-medium">{{ number_format($booking->tax_amount, 3) }} OMR</span>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t-2 border-gray-300">
                        <span class="text-lg font-bold text-gray-900">{{ __('halls.total') }}</span>
                        <span
                            class="text-2xl font-bold text-primary-600">{{ number_format($booking->total_amount, 3) }}
                            OMR</span>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="flex items-center gap-3 p-4 mt-6 border bg-amber-50 border-amber-200 rounded-xl">
                <svg class="flex-shrink-0 w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <div class="font-semibold text-amber-900">{{ __('halls.payment_pending') }}</div>
                    <div class="text-sm text-amber-700">{{ __('halls.payment_instructions') }}</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 fade-in-up" style="animation-delay: 0.4s;">
            <a href="{{ route('customer.halls.index') }}?lang={{ app()->getLocale() }}"
                class="flex items-center justify-center gap-2 font-semibold text-gray-700 transition bg-white border-2 border-gray-300 h-14 rounded-xl hover:bg-gray-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                {{ __('halls.back_to_home') }}
            </a>

            <a href="{{ route('customer.booking.download-pdf', $booking->id) }}"
                class="flex items-center justify-center gap-2 font-bold text-white transition bg-green-600 shadow-lg h-14 rounded-xl hover:bg-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                {{ __('halls.download_pdf') }}
            </a>
        </div>
    </div>

</body>

</html>
