<!DOCTYPE html>
<html>
<head>
    <title>Payment Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <div class="max-w-2xl p-6 mx-auto bg-white rounded-lg shadow-lg">
        <h1 class="mb-6 text-2xl font-bold">Payment Gateway Test</h1>

        <div class="space-y-4">
            <div>
                <strong>Thawani Configuration:</strong>
                <pre class="p-4 mt-2 text-sm bg-gray-100 rounded">{{ json_encode([
                    'secret_key_set' => !empty(config('services.thawani.secret_key')),
                    'publishable_key_set' => !empty(config('services.thawani.publishable_key')),
                    'base_url' => config('services.thawani.base_url'),
                    'secret_key_length' => strlen(config('services.thawani.secret_key')),
                    'publishable_key_length' => strlen(config('services.thawani.publishable_key')),
                ], JSON_PRETTY_PRINT) }}</pre>
            </div>

            @if(isset($booking))
            <div>
                <strong>Booking Details:</strong>
                <pre class="p-4 mt-2 text-sm bg-gray-100 rounded">{{ json_encode([
                    'id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'total_amount' => $booking->total_amount,
                    'hall_name' => is_array($booking->hall->name) ? $booking->hall->name['en'] : $booking->hall->name,
                ], JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif

            @if(isset($payment))
            <div>
                <strong>Payment Details:</strong>
                <pre class="p-4 mt-2 text-sm bg-gray-100 rounded">{{ json_encode([
                    'id' => $payment->id,
                    'payment_reference' => $payment->payment_reference,
                    'amount' => $payment->amount,
                    'transaction_id' => $payment->transaction_id,
                    'payment_url' => $payment->payment_url,
                    'status' => $payment->status,
                ], JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif

            @if(isset($error))
            <div class="p-4 text-red-700 bg-red-100 border border-red-400 rounded">
                <strong>Error:</strong> {{ $error }}
            </div>
            @endif

            @if(isset($paymentUrl))
            <div class="mt-6">
                <a href="{{ $paymentUrl }}" target="_blank" class="inline-block px-6 py-3 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Open Payment Gateway in New Tab
                </a>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
