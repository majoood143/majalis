<div class="space-y-3 text-sm">
    <div class="grid grid-cols-2 gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Payment Reference:</span>
            <span class="block mt-1 font-mono text-gray-900 dark:text-gray-100">{{ $payment->payment_reference }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-700 dark:text-gray-300">Booking Number:</span>
            <span class="block mt-1 font-mono text-gray-900 dark:text-gray-100">{{ $payment->booking->booking_number }}</span>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="p-3 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800">
            <span class="text-xs text-blue-700 dark:text-blue-300">Original Amount</span>
            <span class="block mt-1 text-lg font-bold text-blue-900 dark:text-blue-100">{{ number_format($payment->amount, 3) }} OMR</span>
        </div>

        @if($payment->refund_amount > 0)
        <div class="p-3 border border-orange-200 rounded-lg bg-orange-50 dark:bg-orange-900/20 dark:border-orange-800">
            <span class="text-xs text-orange-700 dark:text-orange-300">Already Refunded</span>
            <span class="block mt-1 text-lg font-bold text-orange-900 dark:text-orange-100">{{ number_format($payment->refund_amount, 3) }} OMR</span>
        </div>
        @endif

        <div class="p-3 border border-green-200 rounded-lg bg-green-50 dark:bg-green-900/20 dark:border-green-800">
            <span class="text-xs text-green-700 dark:text-green-300">Available to Refund</span>
            <span class="block mt-1 text-lg font-bold text-green-900 dark:text-green-100">{{ number_format($remainingAmount, 3) }} OMR</span>
        </div>
    </div>

    <div class="p-3 border border-yellow-200 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-800">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 mt-0.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Important Notes:</p>
                <ul class="mt-1 space-y-1 text-xs text-yellow-700 list-disc list-inside dark:text-yellow-300">
                    <li>Refunds are processed through Thawani payment gateway</li>
                    <li>Full refunds will automatically cancel the booking</li>
                    <li>Refund typically takes 5-10 business days to appear in customer's account</li>
                    <li>This action cannot be undone</li>
                </ul>
            </div>
        </div>
    </div>
</div>
