<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Payment Receipt') }} - {{ $booking->booking_number }}</title>
    <style>
        * { margin: 5; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #000000;
            line-height: 1.4;
            background: #ffffff;
            direction: {{ app()->isLocale('ar') ? 'rtl' : 'ltr' }};
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }

        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #000000;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #000000;
            margin-bottom: 8px;
        }

        .data-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }

        .data-table th {
            background: #f0f0f0;
            font-weight: bold;
            color: #000000;
            font-size: 7pt;
            text-transform: uppercase;
            padding: 5px 8px;
            border-bottom: 1px solid #999999;
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }

        .data-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e5e5;
            color: #000000;
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }

        .data-table tbody tr:nth-child(even) td { background: #fafafa; }

        .text-right  { text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }}; }
        .text-center { text-align: center; }

        .info-label { color: #444444; font-size: 7pt; }
        .info-value { font-weight: 500; color: #000000; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1.5px solid #000000;
            color: #000000;
        }

        .footer-text { font-size: 6.5pt; color: #555555; }
    </style>
</head>
<body>

    {{-- ========================================================================
        Header — Logo (left) + Receipt title / ref (right)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0"
           style="border-bottom: 2px solid #000000; margin-bottom: 12px; padding-bottom: 8px;">
        <tr>
            <td width="50%" style="vertical-align: top;">
                <img src="{{ public_path(config('app.logo_path')) }}"
                     alt="{{ $platformName }}"
                     style="height: 40px; display: block; margin-bottom: 6px;">
                <div style="font-size: 7pt; color: #444444; line-height: 1.6;">
                    {{ $platformAddress }}<br>
                    {{ __('Phone') }}: {{ $platformPhone }}<br>
                    {{ __('Email') }}: {{ $platformEmail }}
                </div>
            </td>
            <td width="50%" style="vertical-align: top; text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};">
                <div style="font-size: 16pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    {{ __('Payment Receipt') }}
                </div>
                <div style="font-size: 7.5pt; color: #444444; margin-bottom: 4px;">
                    {{ __('PAYMENT RECEIPT - FULLY PAID') }}
                </div>
                <div style="font-size: 8pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    {{ __('Receipt Number') }}: {{ $booking->booking_number }}
                </div>
                <div style="font-size: 7pt; color: #444444; margin-bottom: 6px;">
                    {{ __('Receipt Date') }}: {{ $generatedDate->format('d/m/Y H:i') }}
                    &nbsp;|&nbsp; {{ __('Booking Date') }}: {{ $booking->created_at->format('d/m/Y') }}
                </div>
                <span class="badge">{{ __('FULLY PAID') }}</span>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Payment Confirmed Banner
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            <td style="border: 1.5px solid #000000; background: #f9f9f9;
                text-align: center; padding: 10px;">
                <div style="font-size: 11pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    {{ __('PAYMENT COMPLETED SUCCESSFULLY') }}
                </div>
                <div style="font-size: 7.5pt; color: #444444;">
                    {{ __('Thank you! Your booking is fully confirmed and paid.') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 1: From + Receipt To (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('From') }}</div>
                <div style="font-size: 8pt; line-height: 1.8;">
                    <strong>{{ $platformName }}</strong><br>
                    {{ $platformAddress }}<br>
                    {{ __('Phone') }}: {{ $platformPhone }}<br>
                    {{ __('Email') }}: {{ $platformEmail }}
                </div>
            </td>
            <td width="2%"></td>
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Receipt To') }}</div>
                <div style="font-size: 8pt; line-height: 1.8;">
                    <strong>{{ $customerName }}</strong><br>
                    {{ __('Phone') }}: {{ $customerPhone }}<br>
                    {{ __('Email') }}: {{ $customerEmail }}
                    @if($userName)
                        <br>{{ __('Account') }}: {{ $userName }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 2: Booking Details (two sub-columns)
        ======================================================================== --}}
    <div class="section-title">{{ __('Booking Details') }}</div>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            <td width="49%" style="vertical-align: top; font-size: 8pt; line-height: 1.8;">
                <span class="info-label">{{ __('Hall Name') }}:</span>
                <span class="info-value"> {{ $hallName }}</span><br>
                <span class="info-label">{{ __('Location') }}:</span>
                <span class="info-value"> {{ $cityName }}, {{ $regionName }}</span><br>
                <span class="info-label">{{ __('Event Date') }}:</span>
                <span class="info-value"> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span>
            </td>
            <td width="2%"></td>
            <td width="49%" style="vertical-align: top; font-size: 8pt; line-height: 1.8;">
                <span class="info-label">{{ __('Time Slot') }}:</span>
                <span class="info-value"> {{ $booking->time_slot }}</span><br>
                <span class="info-label">{{ __('Number of Guests') }}:</span>
                <span class="info-value"> {{ $booking->number_of_guests }}</span><br>
                <span class="info-label">{{ __('Event Type') }}:</span>
                <span class="info-value"> {{ $eventType }}</span>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Service Breakdown table
        ======================================================================== --}}
    <div class="section-title" style="margin-bottom: 6px;">{{ __('Service Breakdown') }}</div>
    <table class="data-table" width="100%" style="margin-bottom: 12px;">
        <thead>
            <tr>
                <th>{{ __('Description') }}</th>
                <th class="text-center" width="15%">{{ __('Quantity') }}</th>
                <th class="text-right" width="25%">{{ __('Amount (OMR)') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ __('Hall Rental') }}</strong></td>
                <td class="text-center">1</td>
                <td class="text-right">{{ $formattedHallPrice }}</td>
            </tr>
            @foreach($extraServices as $service)
                <tr>
                    <td>{{ $service['name'] }}</td>
                    <td class="text-center">{{ $service['quantity'] }}</td>
                    <td class="text-right">{{ $service['total_price'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ========================================================================
        Row 3: Amount Summary + Payment Details (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            {{-- Amount Summary --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Amount Summary') }}</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="info-label">{{ __('Hall Price') }}</td>
                        <td class="text-right">{{ $formattedHallPrice }} OMR</td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('Services Total') }}</td>
                        <td class="text-right">{{ $formattedServicesPrice }} OMR</td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('Subtotal') }}</td>
                        <td class="text-right">{{ $formattedSubtotal }} OMR</td>
                    </tr>
                    <tr>
                        <td class="info-label">
                            {{ __('Platform Fee') }}{{ $booking->service_fee_type === 'percentage' && $booking->service_fee_value ? ' (' . $booking->service_fee_value . '%)' : '' }}
                        </td>
                        <td class="text-right">{{ number_format((float) $booking->platform_fee, 3) }} OMR</td>
                    </tr>
                    <tr style="border-top: 1.5px solid #000000;">
                        <td style="font-weight: bold; padding: 6px 8px; font-size: 9pt;">
                            {{ __('Total Amount Paid') }}
                        </td>
                        <td class="text-right" style="font-weight: bold; padding: 6px 8px; font-size: 9pt;">
                            {{ $formattedTotal }} OMR
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Payment Details --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Payment Details') }}</div>
                @if($booking->payment_type === 'advance')
                    {{-- Advance payment history --}}
                    <table class="data-table" width="100%" style="margin-bottom: 4px;">
                        <tr style="background: #f9f9f9;">
                            <td colspan="2" style="font-weight: bold; padding: 4px 8px; font-size: 7.5pt;">
                                {{ __('Initial Advance Payment') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('Amount') }}</td>
                            <td class="text-right">{{ $formattedAdvance }} OMR</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('Paid on') }}</td>
                            <td class="text-right">{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('Method') }}</td>
                            <td class="text-right">{{ __('Online Payment (Thawani)') }}</td>
                        </tr>
                    </table>
                    <table class="data-table" width="100%">
                        <tr style="background: #f9f9f9;">
                            <td colspan="2" style="font-weight: bold; padding: 4px 8px; font-size: 7.5pt;">
                                {{ __('Balance Payment') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('Amount') }}</td>
                            <td class="text-right">{{ $formattedBalance }} OMR</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('Paid on') }}</td>
                            <td class="text-right">
                                {{ $booking->balance_paid_at ? $booking->balance_paid_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                        </tr>
                        @if($booking->balance_payment_method)
                            <tr>
                                <td class="info-label">{{ __('Method') }}</td>
                                <td class="text-right">
                                    {{ __(ucfirst(str_replace('_', ' ', $booking->balance_payment_method))) }}
                                </td>
                            </tr>
                        @endif
                        @if($booking->balance_payment_reference)
                            <tr>
                                <td class="info-label">{{ __('Reference') }}</td>
                                <td class="text-right">{{ $booking->balance_payment_reference }}</td>
                            </tr>
                        @endif
                    </table>
                @else
                    {{-- Full payment --}}
                    <table class="data-table" width="100%">
                        <tr>
                            <td class="info-label">{{ __('Payment Type') }}</td>
                            <td class="text-right">{{ __('Full Payment') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('Payment Date') }}</td>
                            <td class="text-right">{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('Payment Method') }}</td>
                            <td class="text-right">{{ __('Online Payment (Thawani)') }}</td>
                        </tr>
                        <tr style="border-top: 1.5px solid #000000;">
                            <td style="font-weight: bold; padding: 6px 8px;">{{ __('Amount Paid') }}</td>
                            <td class="text-right" style="font-weight: bold; padding: 6px 8px; font-size: 9pt;">
                                {{ $formattedTotal }} OMR
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 4: Booking Confirmed reminders + Hall Owner + Terms (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            {{-- Booking Confirmed reminders --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Booking Confirmed') }}</div>
                <table width="100%" cellpadding="0" cellspacing="0"
                       style="border: 1px solid #cccccc; background: #f9f9f9; margin-bottom: 8px;">
                    <tr>
                        <td style="padding: 8px; font-size: 7.5pt; line-height: 1.8;">
                            {{ __('Your booking is confirmed and fully paid. Please keep this receipt for your records.') }}
                        </td>
                    </tr>
                </table>
                <div style="font-size: 7.5pt; line-height: 1.8;">
                    <strong>{{ __('Important Reminders') }}:</strong><br>
                    &bull; {{ __('Arrive at the hall 30 minutes before your event time') }}<br>
                    &bull; {{ __('Contact the hall owner if you have special requirements') }}<br>
                    &bull; {{ __('Keep this receipt as proof of payment') }}<br>
                    &bull; {{ __('For any changes or cancellations, contact us immediately') }}
                </div>
            </td>

            <td width="2%"></td>

            {{-- Hall Owner + Terms --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Hall Owner Contact') }}</div>
                <div style="font-size: 8pt; line-height: 1.8; margin-bottom: 10px;">
                    <strong>{{ __('Name') }}:</strong> {{ $ownerName }}<br>
                    <strong>{{ __('Phone') }}:</strong> {{ $ownerPhone }}<br>
                    <span style="font-size: 7pt; color: #444444;">
                        {{ __('For any hall-specific questions or special arrangements, please contact the hall owner directly.') }}
                    </span>
                </div>

                <div class="section-title">{{ __('Terms & Conditions') }}</div>
                <div style="font-size: 7pt; line-height: 1.8; color: #333333;">
                    &bull; {{ __('Cancellation policy applies as per terms of service') }}<br>
                    &bull; {{ __('Full payment has been received and confirmed') }}<br>
                    &bull; {{ __('All prices are in Omani Rial (OMR)') }}<br>
                    &bull; {{ __('This receipt is valid proof of payment') }}<br>
                    &bull; {{ __('For refund requests, contact customer support') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Footer
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0"
           style="border-top: 1px solid #cccccc; padding-top: 6px;">
        <tr>
            <td width="60%" style="vertical-align: middle;" class="footer-text">
                <strong>{{ $platformName }}</strong> &mdash;
                {{ $platformAddress }} &nbsp;|&nbsp; {{ $platformPhone }} &nbsp;|&nbsp; {{ $platformEmail }}
            </td>
            <td width="40%" style="vertical-align: middle; text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};" class="footer-text">
                {{ __('Thank you for your business!') }}<br>
                {{ __('This is an automated receipt. Generated on') }}: {{ $generatedDate->format('d/m/Y H:i:s') }}
            </td>
        </tr>
    </table>

</body>
</html>
