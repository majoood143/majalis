<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status === 'pending' ? __('Payment Pending') : __('Payment Successful') }}</title>
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
<body class="min-h-screen flex items-center justify-center p-4
    {{ ($status ?? 'paid') === 'pending'
        ? 'bg-gradient-to-br from-yellow-50 to-amber-100'
        : 'bg-gradient-to-br from-green-50 to-emerald-100' }}">

    <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-8 text-center">

        @if(($status ?? 'paid') === 'pending')
            {{-- Pending / verification in progress --}}
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Being Verified</h1>
            <p class="text-gray-500 mb-6">
                Your payment is being processed. If you completed the payment, your booking will be confirmed shortly.
                You will receive a confirmation email at <strong>{{ $booking->customer_email }}</strong>.
            </p>
        @else
            {{-- Confirmed paid --}}
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
            <p class="text-gray-500 mb-6">
                Your booking has been confirmed. A confirmation email has been sent to
                <strong>{{ $booking->customer_email }}</strong>.
            </p>
        @endif

        <div class="bg-gray-50 rounded-xl p-4 text-left mb-6">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-gray-500 text-sm">Booking #</span>
                <span class="font-semibold text-gray-900">{{ $booking->booking_number }}</span>
            </div>
            @php
                $hallName = $booking->hall->name ?? '';
                if (is_array($hallName)) {
                    $hallName = $hallName[app()->getLocale()] ?? $hallName['en'] ?? '';
                }
            @endphp
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-gray-500 text-sm">Hall</span>
                <span class="font-semibold text-gray-900">{{ $hallName }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-gray-500 text-sm">Date</span>
                <span class="font-semibold text-gray-900">{{ $booking->booking_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-500 text-sm">Amount</span>
                <span class="font-bold {{ ($status ?? 'paid') === 'pending' ? 'text-yellow-600' : 'text-green-600' }}">
                    {{ number_format($booking->total_amount, 3) }} OMR
                </span>
            </div>
        </div>

        @if(($status ?? 'paid') === 'pending')
            <p class="text-xs text-gray-400">If you did not complete the payment, you can ignore this page.</p>
        @endif
    </div>
</body>
</html>
