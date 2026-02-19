{{--
|--------------------------------------------------------------------------
| Payment Receipt PDF Template - A5 Size (148mm x 210mm)
|--------------------------------------------------------------------------
|
| Compact payment receipt template optimized for A5 printing.
| Supports both English and Arabic content with RTL layout detection.
|
| Variables:
| - $payment: Payment model instance
| - $booking: Booking model instance (optional, via $payment->booking)
| - $hall: Hall model instance (optional, via $booking->hall)
|
| Usage:
| Pdf::loadView('pdf.payment-receipt-a5', compact('payment', 'booking', 'hall'))
|     ->setPaper('a5', 'portrait')
|     ->download('receipt.pdf');
|
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    {{-- <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp"> --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('payment.receipt.title') }} - {{ $payment->payment_reference }}</title>
    <style>
        /**
         * Reset and Base Styles
         * A5 dimensions: 148mm x 210mm
         */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A5 portrait;
            margin: 10mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            background: #fff;
            padding: 5mm;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }

        /**
         * Header Section
         * Company branding and receipt identifier
         */
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }

        .header .tagline {
            font-size: 8px;
            color: #666;
            margin-bottom: 5px;
        }

        .header .receipt-number {
            font-size: 10px;
            color: #2563eb;
            font-weight: bold;
        }

        /**
         * Receipt Title Bar
         * Visual indicator of document type
         */
        .receipt-title {
            text-align: center;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            border-radius: 4px;
        }

        /**
         * Status Badge
         * Payment status indicator with color coding
         */
        .status-container {
            text-align: center;
            margin-bottom: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #22c55e;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #ef4444;
        }

        .status-refunded {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        /**
         * Amount Display Box
         * Prominent display of payment amount
         */
        .amount-box {
            background-color: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            margin-bottom: 12px;
        }

        .amount-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .amount-value {
            font-size: 22px;
            font-weight: bold;
            color: #166534;
        }

        .currency {
            font-size: 12px;
            color: #666;
            margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 3px;
        }

        /**
         * Refund Box (if applicable)
         */
        .refund-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            margin-bottom: 12px;
        }

        .refund-label {
            font-size: 8px;
            color: #92400e;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .refund-value {
            font-size: 16px;
            font-weight: bold;
            color: #b45309;
        }

        /**
         * Information Sections
         * Organized data display
         */
        .section {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /**
         * Information Grid Layout
         * Label-value pairs in tabular format
         */
        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            padding: 3px 8px 3px 0;
            font-weight: bold;
            color: #666;
            width: 40%;
            font-size: 9px;
        }

        .info-value {
            display: table-cell;
            padding: 3px 0;
            font-size: 9px;
            color: #333;
        }

        /**
         * Two Column Layout
         * Side-by-side information display
         */
        .two-columns {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 8px;
        }

        .column:last-child {
            padding-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 0;
            padding-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 8px;
        }

        /**
         * Thank You Message
         */
        .thank-you {
            text-align: center;
            padding: 10px;
            background-color: #eff6ff;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .thank-you p {
            color: #1e40af;
            font-weight: bold;
            font-size: 10px;
            margin: 0;
        }

        .thank-you .sub-text {
            font-weight: normal;
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }

        /**
         * Footer Section
         */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .footer p {
            margin: 2px 0;
        }

        .footer .company-info {
            margin-top: 8px;
            font-weight: bold;
            color: #1e40af;
        }

        /**
         * Watermark (for paid receipts)
         */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px;
            color: rgba(34, 197, 94, 0.08);
            font-weight: bold;
            z-index: -1;
            text-transform: uppercase;
            letter-spacing: 5px;
        }

        /**
         * QR Code Placeholder
         */
        .qr-section {
            text-align: center;
            margin-top: 10px;
        }

        .qr-code {
            width: 60px;
            height: 60px;
            border: 1px solid #ddd;
            display: inline-block;
        }

        .qr-label {
            font-size: 7px;
            color: #999;
            margin-top: 3px;
        }

        /**
         * Print Styles
         */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .amount-box,
            .status-badge,
            .thank-you {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    {{-- Watermark for paid receipts --}}
    @if($payment->status === 'paid')
        <div class="watermark">{{ __('payment.status.paid') }}</div>
    @endif

    {{-- Header Section --}}
    <div class="header">
        <h1>{{ config('app.name', 'Majalis') }}</h1>
        <p class="tagline">{{ __('payment.receipt.tagline', ['default' => 'Hall Booking Management System']) }}</p>
        <p class="receipt-number">{{ $payment->payment_reference }}</p>
    </div>

    {{-- Receipt Title --}}
    <div class="receipt-title">
        {{ __('payment.receipt.title', ['default' => 'PAYMENT RECEIPT']) }}
    </div>

    {{-- Status Badge --}}
    <div class="status-container">
        <span class="status-badge status-{{ $payment->status }}">
            @php
                $statusText = match($payment->status) {
                    'paid' => __('payment.status.paid'),
                    'pending' => __('payment.status.pending'),
                    'failed' => __('payment.status.failed'),
                    'refunded' => __('payment.status.refunded'),
                    'partially_refunded' => __('payment.status.partially_refunded'),
                    default => ucfirst($payment->status),
                };
            @endphp
            {{ $statusText }}
        </span>
    </div>

    {{-- Amount Box --}}
    <div class="amount-box">
        <div class="amount-label">{{ __('payment.receipt.amount_paid', ['default' => 'Amount Paid']) }}</div>
        <div class="amount-value">
            <span class="currency">{{ $payment->currency ?? 'OMR' }}</span>
            {{ number_format((float) $payment->amount, 3) }}
        </div>
    </div>

    {{-- Refund Box (if applicable) --}}
    @if($payment->refund_amount && (float) $payment->refund_amount > 0)
        <div class="refund-box">
            <div class="refund-label">{{ __('payment.receipt.refund_amount', ['default' => 'Refunded Amount']) }}</div>
            <div class="refund-value">
                {{ $payment->currency ?? 'OMR' }} {{ number_format((float) $payment->refund_amount, 3) }}
            </div>
        </div>
    @endif

    {{-- Two Column Layout: Payment & Booking Details --}}
    <div class="two-columns">
        {{-- Payment Details Column --}}
        <div class="column">
            <div class="section">
                <div class="section-title">{{ __('payment.receipt.payment_details', ['default' => 'Payment Details']) }}</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">{{ __('payment.fields.payment_reference') }}:</div>
                        <div class="info-value">{{ $payment->payment_reference }}</div>
                    </div>
                    @if($payment->transaction_id)
                        <div class="info-row">
                            <div class="info-label">{{ __('payment.fields.transaction_id') }}:</div>
                            <div class="info-value">{{ $payment->transaction_id }}</div>
                        </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">{{ __('payment.fields.payment_method') }}:</div>
                        <div class="info-value">{{ ucfirst($payment->payment_method ?? 'N/A') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">{{ __('payment.fields.payment_date') }}:</div>
                        <div class="info-value">
                            {{ $payment->paid_at
                                ? $payment->paid_at->format('d M Y, H:i')
                                : ($payment->created_at ? $payment->created_at->format('d M Y, H:i') : 'N/A')
                            }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking Details Column --}}
        <div class="column">
            <div class="section">
                <div class="section-title">{{ __('payment.receipt.booking_details', ['default' => 'Booking Details']) }}</div>
                <div class="info-grid">
                    @if($booking)
                        <div class="info-row">
                            <div class="info-label">{{ __('payment.fields.booking') }}:</div>
                            <div class="info-value">{{ $booking->booking_number }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ __('payment.receipt.hall') }}:</div>
                            <div class="info-value">
                                @php
                                    $hallName = $hall->name ?? null;
                                    if (is_array($hallName)) {
                                        $hallName = $hallName[app()->getLocale()] ?? $hallName['en'] ?? 'N/A';
                                    }
                                @endphp
                                {{ $hallName ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ __('payment.receipt.event_date') }}:</div>
                            <div class="info-value">
                                {{ $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">{{ __('payment.receipt.time_slot') }}:</div>
                            <div class="info-value">
                                {{ ucfirst(str_replace('_', ' ', $booking->time_slot ?? 'N/A')) }}
                            </div>
                        </div>
                    @else
                        <div class="info-row">
                            <div class="info-label">{{ __('payment.fields.booking') }}:</div>
                            <div class="info-value">N/A</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Information --}}
    @if($booking)
        <div class="section">
            <div class="section-title">{{ __('payment.receipt.customer_info', ['default' => 'Customer Information']) }}</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">{{ __('payment.receipt.customer_name') }}:</div>
                    <div class="info-value">{{ $booking->customer_name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">{{ __('payment.receipt.email') }}:</div>
                    <div class="info-value">{{ $booking->customer_email ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">{{ __('payment.receipt.phone') }}:</div>
                    <div class="info-value">{{ $booking->customer_phone ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Thank You Message --}}
    <div class="thank-you">
        <p>{{ __('payment.receipt.thank_you', ['default' => 'Thank you for your payment!']) }}</p>
        <p class="sub-text">{{ __('payment.receipt.thank_you_sub', ['default' => 'We appreciate your business and look forward to serving you.']) }}</p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ __('payment.receipt.computer_generated', ['default' => 'This is a computer-generated receipt and does not require a signature.']) }}</p>
        <p>{{ __('payment.receipt.generated_on', ['default' => 'Generated on']) }}: {{ now()->format('d M Y, H:i:s') }}</p>
        <p class="company-info">
            <strong>{{ config('app.name', 'Majalis') }}</strong><br>
            {{ __('payment.receipt.sultanate_oman', ['default' => 'Sultanate of Oman']) }} | {{ config('mail.from.address', 'support@majalis.om') }}
        </p>
    </div>
</body>
</html>
