<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payment Receipt - {{ $payment->payment_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .receipt-title {
            text-align: center;
            background-color: #3b82f6;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-refunded {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            padding: 5px 10px 5px 0;
            font-weight: bold;
            color: #666;
            width: 40%;
        }

        .info-value {
            display: table-cell;
            padding: 5px 0;
        }

        .amount-box {
            background-color: #f0fdf4;
            border: 2px solid #22c55e;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .amount-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .amount-value {
            font-size: 28px;
            font-weight: bold;
            color: #166534;
        }

        .refund-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .refund-value {
            font-size: 20px;
            font-weight: bold;
            color: #b45309;
        }

        .two-columns {
            display: table;
            width: 100%;
        }

        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        .column:last-child {
            padding-right: 0;
            padding-left: 15px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.03);
            font-weight: bold;
            z-index: -1;
        }

        .thank-you {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #eff6ff;
            border-radius: 5px;
        }

        .thank-you p {
            color: #1e40af;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark">PAID</div>

    <!-- Header -->
    <div class="header">
        <h1>Majalis</h1>
        <p>Hall Booking Management System</p>
        <p>Sultanate of Oman</p>
    </div>

    <!-- Receipt Title -->
    <div class="receipt-title">
        PAYMENT RECEIPT
    </div>

    <!-- Payment Reference & Status -->
    <div class="section" style="text-align: center; margin-bottom: 20px;">
        <p style="font-size: 11px; color: #666;">Receipt Number</p>
        <p style="font-size: 18px; font-weight: bold; color: #1e40af;">{{ $payment->payment_reference }}</p>
        <br>
        <span class="status-badge status-{{ $payment->status }}">
            {{ strtoupper(str_replace('_', ' ', $payment->status)) }}
        </span>
    </div>

    <!-- Amount Box -->
    <div class="amount-box">
        <div class="amount-label">Amount Paid</div>
        <div class="amount-value">{{ number_format((float)$payment->amount, 3) }} OMR</div>
    </div>

    @if($payment->refund_amount > 0)
    <div class="refund-box">
        <div class="amount-label">Refund Amount</div>
        <div class="refund-value">{{ number_format((float)$payment->refund_amount, 3) }} OMR</div>
        @if($payment->refund_reason)
        <p style="font-size: 10px; margin-top: 5px;">Reason: {{ $payment->refund_reason }}</p>
        @endif
    </div>
    @endif

    <!-- Two Column Layout -->
    <div class="two-columns">
        <!-- Payment Details -->
        <div class="column">
            <div class="section">
                <div class="section-title">Payment Details</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Transaction ID:</div>
                        <div class="info-value">{{ $payment->transaction_id ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Payment Method:</div>
                        <div class="info-value">{{ $payment->payment_method ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Currency:</div>
                        <div class="info-value">{{ $payment->currency }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Payment Date:</div>
                        <div class="info-value">
                            {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : ($payment->created_at ? $payment->created_at->format('d M Y, H:i') : 'N/A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="column">
            <div class="section">
                <div class="section-title">Booking Details</div>
                <div class="info-grid">
                    @if($booking)
                    <div class="info-row">
                        <div class="info-label">Booking Number:</div>
                        <div class="info-value">{{ $booking->booking_number }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Hall:</div>
                        <div class="info-value">{{ $hall->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Event Date:</div>
                        <div class="info-value">{{ $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Time Slot:</div>
                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $booking->time_slot ?? 'N/A')) }}</div>
                    </div>
                    @else
                    <div class="info-row">
                        <div class="info-label">Booking:</div>
                        <div class="info-value">N/A</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details -->
    @if($booking)
    <div class="section">
        <div class="section-title">Customer Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Customer Name:</div>
                <div class="info-value">{{ $booking->customer_name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $booking->customer_email ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $booking->customer_phone ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Thank You Message -->
    <div class="thank-you">
        <p>Thank you for your payment!</p>
        <p style="font-weight: normal; font-size: 11px; color: #666; margin-top: 5px;">
            We appreciate your business and look forward to serving you.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <p>Generated on: {{ now()->format('d M Y, H:i:s') }}</p>
        <p style="margin-top: 10px;">
            <strong>Majalis</strong> - Hall Booking Management System<br>
            Sultanate of Oman | support@majalis.om
        </p>
    </div>
</body>
</html>
