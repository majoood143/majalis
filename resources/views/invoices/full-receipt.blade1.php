<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Payment Receipt') }} - {{ $booking->booking_number }}</title>
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
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .invoice-header h1 {
            color: #10b981;
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .invoice-header .invoice-type {
            background-color: #10b981;
            color: white;
            padding: 5px 15px;
            display: inline-block;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12pt;
        }

        /* Success Badge */
        .success-badge {
            background-color: #d1fae5;
            border: 2px solid #10b981;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin: 25px 0;
        }

        .success-badge .icon {
            font-size: 48pt;
            color: #10b981;
        }

        .success-badge h2 {
            color: #065f46;
            font-size: 18pt;
            margin: 10px 0;
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
            color: #10b981;
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

        /* Amount Summary */
        .amount-summary {
            background-color: #ecfdf5;
            border: 2px solid #10b981;
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

        .amount-row.total {
            border-top: 2px solid #10b981;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 14pt;
            font-weight: bold;
            color: #10b981;
        }

        /* Payment Details */
        .payment-details {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11pt;
            font-weight: bold;
        }

        .status-badge.paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #10b981;
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

        /* Highlight Box */
        .highlight-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
            <span class="invoice-type">{{ __('PAYMENT RECEIPT - FULLY PAID') }}</span>
            <div style="margin-top: 15px; font-size: 10pt;">
                <p><strong>{{ __('Receipt Number') }}:</strong> {{ $booking->booking_number }}</p>
                <p><strong>{{ __('Receipt Date') }}:</strong> {{ $generatedDate->format('d/m/Y H:i') }}</p>
                <p><strong>{{ __('Booking Date') }}:</strong> {{ $booking->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- Success Badge --}}
        <div class="success-badge">
            <div class="icon"></div>
            <h2>{{ __('PAYMENT COMPLETED SUCCESSFULLY') }}</h2>
            <p style="font-size: 12pt; color: #065f46; margin-top: 10px;">
                {{ __('Thank you! Your booking is fully confirmed and paid.') }}
            </p>
            <div style="margin-top: 15px;">
                <span class="status-badge paid">{{ __('FULLY PAID') }}</span>
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
                    <h3>{{ __('Receipt To') }}</h3>
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

        {{-- Service Breakdown --}}
        <h3 style="color: #10b981; margin: 25px 0 15px;">{{ __('Service Breakdown') }}</h3>
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

        {{-- Payment Summary --}}
        <div class="amount-summary">
            <div class="amount-row">
                <span>{{ __('Hall Price') }}</span>
                <span>{{ $formattedHallPrice }} OMR</span>
            </div>
            <div class="amount-row">
                <span>{{ __('Services Total') }}</span>
                <span>{{ $formattedServicesPrice }} OMR</span>
            </div>
            <div class="amount-row">
                <span>{{ __('Subtotal') }}</span>
                <span>{{ $formattedSubtotal }} OMR</span>
            </div>
            <div class="amount-row">
                <span>{{ __('Platform Fee') }} ({{ $booking->commission_type === 'percentage' ? $booking->commission_value . '%' : 'Fixed' }})</span>
                <span>{{ $formattedCommission }} OMR</span>
            </div>
            <div class="amount-row total">
                <span>{{ __('Total Amount Paid') }}</span>
                <span>{{ $formattedTotal }} OMR</span>
            </div>
        </div>

        {{-- Payment Details --}}
        <h3 style="color: #10b981; margin: 25px 0 15px;">{{ __('Payment Details') }}</h3>
        
        @if($booking->payment_type === 'advance')
            {{-- Advance Payment Breakdown --}}
            <div class="payment-details">
                <h4 style="color: #065f46; margin-bottom: 10px;">{{ __('Payment History') }}</h4>
                
                <div style="background-color: white; padding: 12px; border-radius: 5px; margin-bottom: 10px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span><strong>{{ __('Initial Advance Payment') }}</strong></span>
                        <span><strong>{{ $formattedAdvance }} OMR</strong></span>
                    </div>
                    <div style="font-size: 9pt; color: #6b7280;">
                        {{ __('Paid on') }}: {{ $booking->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div style="font-size: 9pt; color: #6b7280;">
                        {{ __('Payment Method') }}: {{ __('Online Payment (Thawani)') }}
                    </div>
                </div>

                <div style="background-color: white; padding: 12px; border-radius: 5px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span><strong>{{ __('Balance Payment') }}</strong></span>
                        <span><strong>{{ $formattedBalance }} OMR</strong></span>
                    </div>
                    <div style="font-size: 9pt; color: #6b7280;">
                        {{ __('Paid on') }}: {{ $booking->balance_paid_at ? $booking->balance_paid_at->format('d/m/Y H:i') : 'N/A' }}
                    </div>
                    @if($booking->balance_payment_method)
                    <div style="font-size: 9pt; color: #6b7280;">
                        {{ __('Payment Method') }}: {{ __(ucfirst(str_replace('_', ' ', $booking->balance_payment_method))) }}
                    </div>
                    @endif
                    @if($booking->balance_payment_reference)
                    <div style="font-size: 9pt; color: #6b7280;">
                        {{ __('Reference') }}: {{ $booking->balance_payment_reference }}
                    </div>
                    @endif
                </div>
            </div>
        @else
            {{-- Full Payment --}}
            <div class="payment-details">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span><strong>{{ __('Payment Type') }}</strong></span>
                    <span>{{ __('Full Payment') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span><strong>{{ __('Payment Date') }}</strong></span>
                    <span>{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span><strong>{{ __('Payment Method') }}</strong></span>
                    <span>{{ __('Online Payment (Thawani)') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span><strong>{{ __('Amount Paid') }}</strong></span>
                    <span><strong style="color: #10b981; font-size: 13pt;">{{ $formattedTotal }} OMR</strong></span>
                </div>
            </div>
        @endif

        {{-- Confirmation Message --}}
        <div class="highlight-box" style="background-color: #ecfdf5; border-left-color: #10b981;">
            <h3 style="color: #065f46; margin-bottom: 10px;">{{ __('Booking Confirmed') }}</h3>
            <p>{{ __('Your booking is confirmed and fully paid. Please keep this receipt for your records.') }}</p>
            <p style="margin-top: 10px;">
                <strong>{{ __('Important Reminders') }}:</strong>
            </p>
            <ul style="margin-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px; margin-top: 8px; font-size: 10pt;">
                <li>{{ __('Arrive at the hall 30 minutes before your event time') }}</li>
                <li>{{ __('Contact the hall owner if you have special requirements') }}</li>
                <li>{{ __('Keep this receipt as proof of payment') }}</li>
                <li>{{ __('For any changes or cancellations, contact us immediately') }}</li>
            </ul>
        </div>

        {{-- Hall Owner Contact --}}
        <div class="info-box">
            <h3>{{ __('Hall Owner Contact') }}</h3>
            <p><strong>{{ __('Name') }}:</strong> {{ $ownerName }}</p>
            <p><strong>{{ __('Phone') }}:</strong> {{ $ownerPhone }}</p>
            <p style="margin-top: 10px; font-size: 9pt; color: #6b7280;">
                {{ __('For any hall-specific questions or special arrangements, please contact the hall owner directly.') }}
            </p>
        </div>

        {{-- Terms and Conditions --}}
        <div class="highlight-box">
            <strong>{{ __('Terms & Conditions') }}:</strong>
            <ul style="margin-{{ app()->isLocale('ar') ? 'right' : 'left' }}: 20px; margin-top: 8px; font-size: 9pt;">
                <li>{{ __('Cancellation policy applies as per terms of service') }}</li>
                <li>{{ __('Full payment has been received and confirmed') }}</li>
                <li>{{ __('All prices are in Omani Rial (OMR)') }}</li>
                <li>{{ __('This receipt is valid proof of payment') }}</li>
                <li>{{ __('For refund requests, contact customer support') }}</li>
            </ul>
        </div>

        {{-- Footer --}}
        <div class="invoice-footer">
            <p><strong>{{ $platformName }}</strong></p>
            <p>{{ $platformAddress }} | {{ __('Phone') }}: {{ $platformPhone }} | {{ __('Email') }}: {{ $platformEmail }}</p>
            <p style="margin-top: 10px; color: #10b981; font-weight: bold;">{{ __('Thank you for your business!') }}</p>
            <p style="font-size: 8pt; margin-top: 5px;">{{ __('This is an automated receipt. Generated on') }}: {{ $generatedDate->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
