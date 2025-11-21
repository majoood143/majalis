<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
            font-size: 14px;
        }
        .success-banner {
            background-color: #dcfce7;
            border: 1px solid #22c55e;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        .success-banner h2 {
            color: #166534;
            margin: 0;
            font-size: 20px;
        }
        .success-banner p {
            color: #166534;
            margin: 5px 0 0;
            font-size: 14px;
        }
        .amount-box {
            background-color: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #166534;
        }
        .details-section {
            margin: 20px 0;
        }
        .details-section h3 {
            color: #1e40af;
            font-size: 16px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #666;
            font-weight: 500;
        }
        .detail-value {
            color: #333;
            font-weight: 600;
        }
        .cta-button {
            display: block;
            background-color: #3b82f6;
            color: #ffffff !important;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
        .cta-button:hover {
            background-color: #2563eb;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #666;
            font-size: 12px;
        }
        .attachment-notice {
            background-color: #eff6ff;
            border: 1px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .attachment-notice p {
            color: #1e40af;
            margin: 0;
            font-size: 14px;
        }
        @media only screen and (max-width: 480px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .amount-value {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Majalis</h1>
            <p>Hall Booking Management System</p>
        </div>

        <!-- Success Banner -->
        <div class="success-banner">
            <h2>✓ Payment Successful</h2>
            <p>Thank you for your payment, {{ $customerName }}!</p>
        </div>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="amount-label">Amount Paid</div>
            <div class="amount-value">{{ number_format((float)$payment->amount, 3) }} OMR</div>
        </div>

        <!-- Payment Details -->
        <div class="details-section">
            <h3>Payment Details</h3>
            <div class="detail-row">
                <span class="detail-label">Receipt Number:</span>
                <span class="detail-value">{{ $payment->payment_reference }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Transaction ID:</span>
                <span class="detail-value">{{ $payment->transaction_id ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ $payment->payment_method ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Date:</span>
                <span class="detail-value">{{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</span>
            </div>
        </div>

        <!-- Booking Details -->
        @if($booking)
        <div class="details-section">
            <h3>Booking Details</h3>
            <div class="detail-row">
                <span class="detail-label">Booking Number:</span>
                <span class="detail-value">{{ $booking->booking_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Hall:</span>
                <span class="detail-value">{{ $hall->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Event Date:</span>
                <span class="detail-value">{{ $booking->booking_date ? $booking->booking_date->format('d M Y') : 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time Slot:</span>
                <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $booking->time_slot ?? 'N/A')) }}</span>
            </div>
        </div>
        @endif

        <!-- Attachment Notice -->
        <div class="attachment-notice">
            <p>📎 Your payment receipt PDF is attached to this email.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>If you have any questions, please contact our support team.</p>
            <p style="margin-top: 15px;">
                <strong>Majalis</strong><br>
                Hall Booking Management System<br>
                Sultanate of Oman
            </p>
            <p style="margin-top: 15px; color: #999;">
                This is an automated email. Please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>
</html>
