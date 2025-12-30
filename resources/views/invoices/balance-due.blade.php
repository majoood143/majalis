<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Balance Due Invoice') }} - {{ $booking->booking_number }}</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            direction: {{ app()->isLocale('ar') ? 'rtl' : 'ltr' }};
        }

        /* Container */
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .invoice-header {
            border-bottom: 3px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .invoice-header h1 {
            color: #dc2626;
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .invoice-header .invoice-type {
            background-color: #dc2626;
            color: white;
            padding: 5px 15px;
            display: inline-block;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12pt;
        }

        /* Two Column Layout */
        .row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .col-50 {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }

        /* Info Boxes */
        .info-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .info-box h3 {
            color: #dc2626;
            font-size: 12pt;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 10pt;
        }

        .info-box .label {
            font-weight: bold;
            color: #6b7280;
        }

        /* Urgent Alert Box */
        .urgent-alert {
            background-color: #fef2f2;
            border: 3px solid #dc2626;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }

        .urgent-alert h2 {
            color: #dc2626;
            font-size: 18pt;
            margin-bottom: 15px;
        }

        .urgent-alert .amount {
            font-size: 28pt;
            font-weight: bold;
            color: #991b1b;
            margin: 20px 0;
        }

        .urgent-alert .deadline {
            background-color: #fee2e2;
            border: 2px solid #dc2626;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13pt;
        }

        /* Payment Summary */
        .payment-summary {
            background-color: #eff6ff;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 11pt;
            border-bottom: 1px solid #dbeafe;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row.highlight {
            background-color: #dbeafe;
            margin: 10px -15px;
            padding: 12px 15px;
            font-weight: bold;
        }

        /* Highlight Box */
        .highlight-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .highlight-box.alert {
            background-color: #fee2e2;
            border-left-color: #dc2626;
        }

        /* Footer */
        .invoice-footer {
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }

        .invoice-footer p {
            margin: 5px 0;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 9pt;
            font-weight: bold;
        }

        .status-badge.pending {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Payment Methods */
        .payment-methods {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .payment-method {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            background-color: #f9fafb;
            border: 2px solid #e5e7eb;
            margin: 0 5px;
        }

        .payment-method .icon {
            font-size: 24pt;
            margin-bottom: 10px;
        }

        /* Days Remaining */
        .days-remaining {
            background-color: #fef2f2;
            border: 2px dashed #dc2626;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        }

        .days-remaining .number {
            font-size: 36pt;
            font-weight: bold;
            color: #dc2626;
        }

        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .invoice-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        {{-- Invoice Header --}}
        <div class="invoice-header">
            <h1>{{ $platformName }}</h1>
            <span class="invoice-type">{{ __('PAYMENT REMINDER - BALANCE DUE') }}</span>
            <div style="margin-top: 15px; font-size: 10pt;">
                <p><strong>{{ __('Booking Number') }}:</strong> {{ $booking->booking_number }}</p>
                <p><strong>{{ __('Invoice Date') }}:</strong> {{ $generatedDate->format('d/m/Y H:i') }}</p>
                <p><strong>{{ __('Original Booking Date') }}:</strong> {{ $booking->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- Urgent Payment Alert --}}
        <div class="urgent-alert">
            <h2> {{ __('URGENT: BALANCE PAYMENT REQUIRED') }}</h2>
            <p style="font-size: 12pt;">{{ __('Your event is approaching. Please settle the balance immediately.') }}</p>
            
            <div class="amount">
                {{ $formattedBalance }} OMR
            </div>

            <div class="deadline">
                <strong>{{ __('Payment Deadline') }}:</strong> {{ $paymentDeadline->format('d/m/Y') }}
                <br>
                <span style="font-size: 11pt; color: #991b1b;">
                    {{ __('Failure to pay may result in booking cancellation') }}
                </span>
            </div>
        </div>

        {{-- Days Remaining --}}
        @php
            $daysRemaining = now()->diffInDays($paymentDeadline, false);
        @endphp
        @if($daysRemaining > 0)
        <div class="days-remaining">
            <div class="number">{{ $daysRemaining }}</div>
            <p style="font-size: 13pt; font-weight: bold; color: #991b1b;">
                {{ __('Days Remaining Until Deadline') }}
            </p>
        </div>
        @elseif($daysRemaining === 0)
        <div class="days-remaining">
            <p style="font-size: 16pt; font-weight: bold; color: #991b1b;">
                 {{ __('PAYMENT DUE TODAY!') }}
            </p>
        </div>
        @else
        <div class="days-remaining" style="background-color: #fee2e2; border-color: #991b1b;">
            <p style="font-size: 16pt; font-weight: bold; color: #7f1d1d;">
                 {{ __('OVERDUE BY') }} {{ abs($daysRemaining) }} {{ __('DAYS') }}
            </p>
        </div>
        @endif

        {{-- Customer Info --}}
        <div class="row">
            <div class="col-50">
                <div class="info-box">
                    <h3>{{ __('Bill To') }}</h3>
                    <p><strong>{{ $customerName }}</strong></p>
                    <p>{{ __('Phone') }}: {{ $customerPhone }}</p>
                    <p>{{ __('Email') }}: {{ $customerEmail }}</p>
                    @if($userName)
                        <p>{{ __('Account') }}: {{ $userName }}</p>
                    @endif
                </div>
            </div>
            <div class="col-50">
                <div class="info-box">
                    <h3>{{ __('Event Details') }}</h3>
                    <p><span class="label">{{ __('Hall') }}:</span> {{ $hallName }}</p>
                    <p><span class="label">{{ __('Location') }}:</span> {{ $cityName }}, {{ $regionName }}</p>
                    <p><span class="label">{{ __('Event Date') }}:</span> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                    <p><span class="label">{{ __('Time') }}:</span> {{ $booking->time_slot }}</p>
                </div>
            </div>
        </div>

        {{-- Payment Summary --}}
        <h3 style="color: #dc2626; margin: 25px 0 15px;">{{ __('Payment Summary') }}</h3>
        <div class="payment-summary">
            <div class="summary-row">
                <span>{{ __('Original Total Amount') }}</span>
                <span><strong>{{ $formattedTotal }} OMR</strong></span>
            </div>
            <div class="summary-row highlight" style="color: #059669;">
                <span> {{ __('Advance Already Paid') }}</span>
                <span>{{ $formattedAdvance }} OMR</span>
            </div>
            <div class="summary-row" style="font-size: 14pt; font-weight: bold; color: #dc2626; padding-top: 15px; border-top: 2px solid #dc2626;">
                <span>{{ __('Balance Due') }}</span>
                <span>{{ $formattedBalance }} OMR</span>
            </div>
        </div>

        {{-- Booking Breakdown --}}
        <div class="info-box">
            <h3>{{ __('Original Booking Breakdown') }}</h3>
            <div class="summary-row">
                <span>{{ __('Hall Rental') }}</span>
                <span>{{ $formattedHallPrice }} OMR</span>
            </div>
            @if($extraServices->count() > 0)
                <div class="summary-row">
                    <span>{{ __('Extra Services') }}</span>
                    <span>{{ $formattedServicesPrice }} OMR</span>
                </div>
                @foreach($extraServices as $service)
                <div style="margin-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px; padding: 5px 0; font-size: 9pt; color: #6b7280;">
                    <span>• {{ $service['name'] }} (x{{ $service['quantity'] }})</span>
                    <span style="float: {{ app()->isLocale('ar') ? 'left' : 'right' }};">{{ $service['total_price'] }} OMR</span>
                </div>
                @endforeach
            @endif
            <div class="summary-row">
                <span>{{ __('Platform Fee') }}</span>
                <span>{{ $formattedCommission }} OMR</span>
            </div>
        </div>

        {{-- Payment Instructions --}}
        <div class="highlight-box alert">
            <h3 style="color: #dc2626; margin-bottom: 10px;">{{ __('How to Pay the Balance') }}</h3>
            <p style="margin-bottom: 10px;">
                <strong>{{ __('Amount to Pay') }}:</strong> {{ $formattedBalance }} OMR
            </p>
            <p style="margin-bottom: 10px;">
                <strong>{{ __('Payment Deadline') }}:</strong> {{ $paymentDeadline->format('l, d F Y') }}
            </p>
            
            <h4 style="margin-top: 15px; margin-bottom: 8px;">{{ __('Accepted Payment Methods') }}:</h4>
            <ul style="margin-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px;">
                <li><strong>{{ __('Bank Transfer') }}:</strong> {{ __('Contact hall owner for bank details') }}</li>
                <li><strong>{{ __('Cash Payment') }}:</strong> {{ __('Arrange with hall owner') }}</li>
                <li><strong>{{ __('Mobile Payment') }}:</strong> {{ __('As agreed with hall owner') }}</li>
            </ul>

            <div style="margin-top: 15px; padding: 12px; background-color: #fff; border-radius: 5px;">
                <h4 style="color: #dc2626;">{{ __('Hall Owner Contact Information') }}</h4>
                <p style="margin-top: 8px;"><strong>{{ __('Name') }}:</strong> {{ $ownerName }}</p>
                <p><strong>{{ __('Phone') }}:</strong> {{ $ownerPhone }}</p>
                <p style="margin-top: 8px; font-size: 10pt; color: #6b7280;">
                    {{ __('Please contact the hall owner directly to arrange payment') }}
                </p>
            </div>
        </div>

        {{-- Important Notice --}}
        <div class="highlight-box">
            <h3 style="color: #f59e0b; margin-bottom: 10px;">{{ __('Important Information') }}</h3>
            <ul style="margin-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px; font-size: 10pt;">
                <li>{{ __('Your advance payment of') }} <strong>{{ $formattedAdvance }} OMR</strong> {{ __('has been received') }}</li>
                <li>{{ __('The balance must be paid before') }} <strong>{{ $paymentDeadline->format('d/m/Y') }}</strong></li>
                <li>{{ __('Failure to pay may result in automatic booking cancellation') }}</li>
                <li>{{ __('After payment, please inform the hall owner and platform') }}</li>
                <li>{{ __('Keep proof of payment for your records') }}</li>
            </ul>
        </div>

        {{-- Payment Status --}}
        <div style="text-align: center; margin: 30px 0;">
            <p style="font-size: 11pt;">{{ __('Current Payment Status') }}:</p>
            <span class="status-badge pending">{{ __('BALANCE PENDING') }}</span>
        </div>

        {{-- Contact Support --}}
        <div class="info-box">
            <h3>{{ __('Need Help?') }}</h3>
            <p>{{ __('If you have any questions or need assistance with payment, please contact us:') }}</p>
            <p style="margin-top: 10px;">
                <strong>{{ $platformName }} {{ __('Support') }}</strong><br>
                {{ __('Phone') }}: {{ $platformPhone }}<br>
                {{ __('Email') }}: {{ $platformEmail }}
            </p>
        </div>

        {{-- Footer --}}
        <div class="invoice-footer">
            <p><strong>{{ $platformName }}</strong></p>
            <p>{{ $platformAddress }} | {{ __('Phone') }}: {{ $platformPhone }} | {{ __('Email') }}: {{ $platformEmail }}</p>
            <p style="margin-top: 10px; color: #dc2626; font-weight: bold;">{{ __('Please pay the balance before the deadline to avoid cancellation') }}</p>
            <p style="font-size: 8pt; margin-top: 5px;">{{ __('Generated on') }}: {{ $generatedDate->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
