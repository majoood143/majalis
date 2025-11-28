<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('halls.booking_cancelled') }} - Majalis</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Tajawal', sans-serif;
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

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4 bg-gradient-to-br from-red-50 to-orange-50">

    <div class="w-full max-w-2xl">
        <!-- Cancellation Icon -->
        <div class="mb-8 text-center fade-in-up">
            <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 bg-red-100 rounded-full shake">
                <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="mb-2 text-3xl font-bold text-gray-900 md:text-4xl">
                {{ __('halls.payment_cancelled') }}
            </h1>
            <p class="text-lg text-gray-600">
                {{ __('halls.booking_not_completed') }}
            </p>
        </div>

        <!-- Info Card -->
        <div class="p-6 mb-6 bg-white shadow-xl rounded-2xl md:p-8 fade-in-up" style="animation-delay: 0.2s;">

            <!-- Booking Reference -->
            @if(isset($booking))
            <div class="p-6 mb-6 text-gray-900 bg-gradient-to-r from-gray-400 to-gray-500 rounded-xl">
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
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200"></div>
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
            @endif

            <!-- Cancellation Info -->
            <div class="p-6 mb-6 border-2 border-red-200 bg-red-50 rounded-xl">
                <div class="flex items-start gap-3 mb-4">
                    <svg class="flex-shrink-0 w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="mb-2 text-lg font-bold text-red-900">
                            {{ __('halls.what_happened') }}
                        </h3>
                        <p class="mb-2 text-sm text-red-800">
                            {{ __('halls.payment_cancelled_message') }}
                        </p>
                        <p class="text-sm text-red-700">
                            {{ __('halls.no_charges_applied') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="p-6 border-2 border-blue-200 bg-blue-50 rounded-xl">
                <h3 class="mb-3 text-lg font-bold text-blue-900">
                    {{ __('halls.what_next') }}
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start gap-2">
                        <svg class="flex-shrink-0 w-5 h-5 mt-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ __('halls.try_payment_again') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="flex-shrink-0 w-5 h-5 mt-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ __('halls.choose_different_hall') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="flex-shrink-0 w-5 h-5 mt-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ __('halls.contact_support') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Booking Details (if available) -->
            @if(isset($booking))
            <div class="p-4 mt-6 bg-gray-50 rounded-xl">
                <h4 class="mb-3 text-sm font-semibold text-gray-700">{{ __('halls.booking_details') }}</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('halls.event_date') }}</span>
                        <span class="font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('halls.time_slot') }}</span>
                        <span class="font-medium text-gray-900">{{ __('halls.' . $booking->time_slot) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('halls.total_amount') }}</span>
                        <span class="text-lg font-bold text-gray-900">
                            {{ number_format($booking->total_amount, 3) }} OMR
                        </span>
                    </div>
                </div>
            </div>
            @endif
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
                {{ __('halls.browse_halls') }}
            </a>

            @if(isset($booking))
            <a href="{{ route('customer.booking.retry-payment', $booking->id) }}?lang={{ app()->getLocale() }}"
                class="flex items-center justify-center gap-2 font-bold text-gray-700 transition border-2 shadow-lg border-gray-300b g-primary-600 h-14 rounded-xl hover:bg-primary-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ __('halls.retry_payment') }}
            </a>
            @endif
        </div>

        <!-- Help Section -->
        <div class="p-4 mt-6 text-center bg-white shadow-lg rounded-xl fade-in-up" style="animation-delay: 0.6s;">
            <p class="mb-2 text-sm text-gray-600">{{ __('halls.need_help') }}</p>
            <a href="mailto:support@majalis.om" class="text-primary-600 hover:text-primary-700">
                support@majalis.om
            </a>
            <span class="mx-2 text-gray-400">|</span>
            <a href="tel:+96812345678" class="text-primary-600 hover:text-primary-700">
                +968 95522928
            </a>
        </div>
    </div>

</body>
</html>
