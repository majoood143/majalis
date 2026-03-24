<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Payment Cancelled') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>* { font-family: 'Tajawal', sans-serif; }</style>
    <style>
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Regular.ttf") }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Medium.ttf") }}') format('truetype');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ asset("fonts/Tajawal-Bold.ttf") }}') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        *, *::before, *::after { font-family: 'Tajawal', sans-serif !important; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-8 text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Payment Cancelled') }}</h1>
        <p class="text-gray-500 mb-6">{{ __('Your payment was not completed. Your booking is still reserved.') }}</p>

        <div class="bg-gray-50 rounded-xl p-4 text-left mb-6">
            <div class="flex justify-between py-2">
                <span class="text-gray-500 text-sm">{{ __('Booking #') }}</span>
                <span class="font-semibold text-gray-900">{{ $booking->booking_number }}</span>
            </div>
        </div>

        <p class="text-sm text-gray-500">{{ __('Please contact us if you need assistance.') }}</p>
    </div>
</body>
</html>
