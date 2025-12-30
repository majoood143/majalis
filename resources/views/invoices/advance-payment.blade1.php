<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Advance Payment Invoice') }} - {{ $booking->booking_number }}</title>
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
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .invoice-header h1 {
            color: #4f46e5;
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .invoice-header .invoice-type {
            background-color: #fbbf24;
            color: #1f2937;
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
            color: #4f46e5;
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

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #4f46e5;
            color: white;
        }

        table th {
            padding: 12px;
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
            font-weight: bold;
            font-size: 10pt;
        }

        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10pt;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Amount Summary */
        .amount-summary {
            background-color: #eff6ff;
            border: 2px solid #4f46e5;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 11pt;
        }

        .amount-row.subtotal {
            border-top: 1px solid #cbd5e1;
            padding-top: 12px;
            margin-top: 8px;
        }

        .amount-row.total {
            border-top: 2px solid #4f46e5;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 14pt;
            font-weight: bold;
            color: #4f46e5;
        }

        .amount-row.advance {
            background-color: #fef3c7;
            padding: 12px;
            margin: 15px -10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13pt;
            color: #92400e;
        }

        .amount-row.balance {
            background-color: #fee2e2;
            padding: 12px;
            margin: 15px -10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13pt;
            color: #991b1b;
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

        .status-badge.paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-badge.partial {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
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
            <span class="invoice-type">{{ __('Advance Payment Invoice') }}</span>
            <div style="margin-top: 15px; font-size: 10pt;">
                <p><strong>{{ __('Invoice Number') }}:</strong> {{ $booking->booking_number }}</p>
                <p><strong>{{ __('Invoice Date') }}:</strong> {{ $generatedDate->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        {{-- Company and Customer Info --}}
        <div class="row">
            <div class="col-50">
                <div class="info-box">
                    <h3>{{ __('From') }}</h3>
                    <p><strong>{{ $platformName }}</strong></p>
                    <p>{{ $platformAddress }}</p>
                    <p>{{ __('Phone') }}: {{ $platformPhone }}</p>
                    <p>{{ __('Email') }}: {{ $platformEmail }}</p>
                </div>
            </div>
            <div class="col-50">
                <div class="info-box">
                    <h3>{{ __('Bill To') }}</h3>
                    <p><strong>{{ $booking->customer_name }}</strong></p>
                    <p>{{ __('Phone') }}: {{ $booking->customer_phone }}</p>
                    <p>{{ __('Email') }}: {{ $booking->customer_email }}</p>
                    @if($booking->user)
                        <p>{{ __('Account') }}: {{ $booking->user->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Booking Details --}}
        <div class="info-box">
            <h3>{{ __('Booking Details') }}</h3>
            <div class="row">
                <div class="col-50">
                    <p><span class="label">{{ __('Hall Name') }}:</span> {{ $hallName }}</p>
                    <p><span class="label">{{ __('Location') }}:</span> {{ $cityName }}, {{ $regionName }}</p>
                    <p><span class="label">{{ __('Event Date') }}:</span> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                </div>
                <div class="col-50">
                    <p><span class="label">{{ __('Time Slot') }}:</span> {{ $booking->time_slot }}</p>
                    <p><span class="label">{{ __('Number of Guests') }}:</span> {{ $booking->number_of_guests }}</p>
                    <p><span class="label">{{ __('Event Type') }}:</span> {{ $booking->event_type ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Pricing Breakdown --}}
        <h3 style="color: #4f46e5; margin: 25px 0 15px;">{{ __('Pricing Breakdown') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Description') }}</th>
                    <th style="text-align: center;">{{ __('Quantity') }}</th>
                    <th style="text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};">{{ __('Amount (OMR)') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- Hall Rental --}}
                <tr>
                    <td><strong>{{ __('Hall Rental') }}</strong></td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};">{{ $formattedHallPrice }}</td>
                </tr>

                {{-- Extra Services --}}
                @foreach($extraServices as $service)
                <tr>
                    <td>{{ $service['name'] }}</td>
                    <td style="text-align: center;">{{ $service['quantity'] }}</td>
                    <td style="text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};">{{ $service['total_price'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Amount Summary --}}
        <div class="amount-summary">
            <div class="amount-row">
                <span>{{ __('Hall Price') }}</span>
                <span>{{ $formattedHallPrice }} OMR</span>
            </div>
            <div class="amount-row">
                <span>{{ __('Services Total') }}</span>
                <span>{{ $formattedServicesPrice }} OMR</span>
            </div>
            <div class="amount-row subtotal">
                <span>{{ __('Subtotal') }}</span>
                <span>{{ $formattedSubtotal }} OMR</span>
            </div>
            <div class="amount-row">
                <span>{{ __('Platform Fee') }} ({{ $booking->commission_type === 'percentage' ? $booking->commission_value . '%' : 'Fixed' }})</span>
                <span>{{ $formattedCommission }} OMR</span>
            </div>
            <div class="amount-row total">
                <span>{{ __('Total Amount') }}</span>
                <span>{{ $formattedTotal }} OMR</span>
            </div>

            {{-- Advance Payment --}}
            <div class="amount-row advance">
                <span>{{ __('Advance Paid') }}</span>
                <span>{{ $formattedAdvance }} OMR</span>
            </div>

            {{-- Balance Due --}}
            <div class="amount-row balance">
                <span>{{ __('Balance Due') }}</span>
                <span>{{ $formattedBalance }} OMR</span>
            </div>
        </div>

        {{-- Important Notice --}}
        <div class="highlight-box alert">
            <strong>{{ __('Important Notice') }}:</strong>
            <p style="margin-top: 8px;">
                {{ __('This invoice confirms your advance payment. The remaining balance of') }} 
                <strong>{{ $formattedBalance }} OMR</strong> 
                {{ __('must be paid before') }} 
                <strong>{{ $paymentDeadline->format('d/m/Y') }}</strong>.
            </p>
            <p style="margin-top: 8px;">
                {{ __('Payment Status') }}: 
                <span class="status-badge partial">{{ __('Partially Paid') }}</span>
            </p>
        </div>

        {{-- Payment Instructions --}}
        <div class="info-box">
            <h3>{{ __('Balance Payment Instructions') }}</h3>
            <p><strong>{{ __('Payment Deadline') }}:</strong> {{ $paymentDeadline->format('d/m/Y') }}</p>
            <p><strong>{{ __('Amount to Pay') }}:</strong> {{ $formattedBalance }} OMR</p>
            <p><strong>{{ __('Payment Methods') }}:</strong></p>
            <ul style="margin-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px; margin-top: 8px;">
                <li>{{ __('Bank Transfer to Hall Owner') }}</li>
                <li>{{ __('Cash Payment (upon arrangement)') }}</li>
                <li>{{ __('Contact hall owner for payment details') }}</li>
            </ul>
            <p style="margin-top: 10px;"><strong>{{ __('Hall Owner Contact') }}:</strong></p>
            <p>{{ __('Name') }}: {{ $ownerName }}</p>
            <p>{{ __('Phone') }}: {{ $ownerPhone }}</p>
        </div>

        {{-- Terms and Conditions --}}
        <div class="highlight-box">
            <strong>{{ __('Terms & Conditions') }}:</strong>
            <ul style="margin-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px; margin-top: 8px; font-size: 9pt;">
                <li>{{ __('Advance payment is non-refundable except as per cancellation policy') }}</li>
                <li>{{ __('Balance must be paid before the event date') }}</li>
                <li>{{ __('Failure to pay balance may result in booking cancellation') }}</li>
                <li>{{ __('All prices are in Omani Rial (OMR)') }}</li>
                <li>{{ __('This is a computer-generated invoice') }}</li>
            </ul>
        </div>

        {{-- Footer --}}
        <div class="invoice-footer">
            <p><strong>{{ $platformName }}</strong></p>
            <p>{{ $platformAddress }} | {{ __('Phone') }}: {{ $platformPhone }} | {{ __('Email') }}: {{ $platformEmail }}</p>
            <p style="margin-top: 10px;">{{ __('Thank you for choosing') }} {{ $platformName }}!</p>
            <p style="font-size: 8pt; margin-top: 5px;">{{ __('Generated on') }}: {{ $generatedDate->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
