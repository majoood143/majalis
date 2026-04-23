<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Balance Due Invoice') }} - {{ $booking->booking_number }}</title>
    <style>
        * { margin: 5; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #333333;
            line-height: 1.4;
            background: #ffffff;
            direction: {{ app()->isLocale('ar') ? 'rtl' : 'ltr' }};
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }

        /* Section titles */
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #dc2626;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #dc2626;
            margin-bottom: 8px;
        }

        /* Data tables */
        .data-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }

        .data-table th {
            background: #fef2f2;
            font-weight: bold;
            color: #991b1b;
            font-size: 7pt;
            text-transform: uppercase;
            padding: 5px 8px;
            border-bottom: 1px solid #fca5a5;
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }

        .data-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #fee2e2;
            color: #333333;
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }

        .data-table tbody tr:nth-child(even) td { background: #fff8f8; }

        .text-right  { text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }}; }
        .text-center { text-align: center; }

        .info-label { color: #6b7280; font-size: 7pt; }
        .info-value { font-weight: 500; color: #333333; }

        /* Status badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1.5px solid #dc2626;
            color: #dc2626;
        }

        .footer-text { font-size: 6.5pt; color: #6b7280; }
    </style>
</head>
<body>

    {{-- ========================================================================
        Header — Logo (left) + Invoice title / ref (right)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0"
           style="border-bottom: 2px solid #dc2626; margin-bottom: 12px; padding-bottom: 8px;">
        <tr>
            {{-- Left: logo + platform info --}}
            <td width="50%" style="vertical-align: top;">
                <img src="{{ public_path(config('app.logo_path')) }}"
                     alt="{{ $platformName }}"
                     style="height: 40px; display: block; margin-bottom: 6px;">
                <div style="font-size: 7pt; color: #6b7280; line-height: 1.6;">
                    {{ $platformAddress }}<br>
                    {{ __('Phone') }}: {{ $platformPhone }}<br>
                    {{ __('Email') }}: {{ $platformEmail }}
                </div>
            </td>

            {{-- Right: invoice type + ref + date + badge --}}
            <td width="50%" style="vertical-align: top; text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};">
                <div style="font-size: 13pt; font-weight: bold; color: #dc2626; margin-bottom: 2px;">
                    {{ __('Balance Due Invoice') }}
                </div>
                <div style="font-size: 7.5pt; color: #666666; margin-bottom: 4px;">
                    {{ __('Payment Reminder') }}
                </div>
                <div style="font-size: 8pt; font-weight: bold; color: #333333; margin-bottom: 2px;">
                    {{ __('Booking Number') }}: {{ $booking->booking_number }}
                </div>
                <div style="font-size: 7pt; color: #6b7280; margin-bottom: 6px;">
                    {{ __('Invoice Date') }}: {{ $generatedDate->format('d/m/Y H:i') }}
                    &nbsp;|&nbsp; {{ __('Booked') }}: {{ $booking->created_at->format('d/m/Y') }}
                </div>
                <span class="badge">{{ __('BALANCE PENDING') }}</span>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Urgent Balance Box — full width
        ======================================================================== --}}
    @php $daysRemaining = now()->diffInDays($paymentDeadline, false); @endphp
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            {{-- Balance due amount --}}
            <td width="49%" style="vertical-align: middle; border: 2px solid #dc2626;
                background: #fef2f2; text-align: center; padding: 12px;">
                <div style="font-size: 7.5pt; color: #991b1b; text-transform: uppercase;
                    letter-spacing: 1px; margin-bottom: 4px;">
                    {{ __('Balance Due') }}
                </div>
                <div style="font-size: 22pt; font-weight: bold; color: #991b1b; margin-bottom: 2px;">
                    {{ $formattedBalance }} OMR
                </div>
                <div style="font-size: 7.5pt; color: #dc2626;">
                    {{ __('Payment Deadline') }}: <strong>{{ $paymentDeadline->format('d/m/Y') }}</strong>
                </div>
            </td>

            <td width="2%"></td>

            {{-- Days remaining --}}
            <td width="49%" style="vertical-align: middle; border: 2px dashed #dc2626;
                background: #fff8f8; text-align: center; padding: 12px;">
                @if($daysRemaining > 0)
                    <div style="font-size: 28pt; font-weight: bold; color: #dc2626; line-height: 1;">
                        {{ $daysRemaining }}
                    </div>
                    <div style="font-size: 8pt; font-weight: bold; color: #991b1b; margin-top: 4px;">
                        {{ __('Days Remaining Until Deadline') }}
                    </div>
                @elseif($daysRemaining == 0)
                    <div style="font-size: 13pt; font-weight: bold; color: #991b1b;">
                        {{ __('PAYMENT DUE TODAY!') }}
                    </div>
                @else
                    <div style="font-size: 13pt; font-weight: bold; color: #7f1d1d;">
                        {{ __('OVERDUE BY') }} {{ abs($daysRemaining) }} {{ __('DAYS') }}
                    </div>
                @endif
                <div style="font-size: 7pt; color: #991b1b; margin-top: 6px;">
                    {{ __('Failure to pay may result in booking cancellation') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 1: Bill To + Event Details (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Bill To') }}</div>
                <div style="font-size: 8pt; line-height: 1.8;">
                    <strong>{{ $customerName }}</strong><br>
                    {{ __('Phone') }}: {{ $customerPhone }}<br>
                    {{ __('Email') }}: {{ $customerEmail }}
                    @if($userName)
                        <br>{{ __('Account') }}: {{ $userName }}
                    @endif
                </div>
            </td>
            <td width="2%"></td>
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Event Details') }}</div>
                <div style="font-size: 8pt; line-height: 1.8;">
                    <span class="info-label">{{ __('Hall') }}:</span>
                    <span class="info-value"> {{ $hallName }}</span><br>
                    <span class="info-label">{{ __('Location') }}:</span>
                    <span class="info-value"> {{ $cityName }}, {{ $regionName }}</span><br>
                    <span class="info-label">{{ __('Event Date') }}:</span>
                    <span class="info-value"> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span><br>
                    <span class="info-label">{{ __('Time') }}:</span>
                    <span class="info-value"> {{ $booking->time_slot }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 2: Payment Summary + Original Breakdown (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            {{-- Payment Summary --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Payment Summary') }}</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="info-label">{{ __('Original Total Amount') }}</td>
                        <td class="text-right"><strong>{{ $formattedTotal }} OMR</strong></td>
                    </tr>
                    <tr style="background: #f0fdf4;">
                        <td style="color: #059669; padding: 5px 8px;">{{ __('Advance Already Paid') }}</td>
                        <td class="text-right" style="color: #059669; font-weight: bold; padding: 5px 8px;">
                            {{ $formattedAdvance }} OMR
                        </td>
                    </tr>
                    <tr style="border-top: 1.5px solid #dc2626;">
                        <td style="font-weight: bold; color: #dc2626; padding: 6px 8px; font-size: 9pt;">
                            {{ __('Balance Due') }}
                        </td>
                        <td class="text-right" style="font-weight: bold; color: #dc2626; padding: 6px 8px; font-size: 9pt;">
                            {{ $formattedBalance }} OMR
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Original Booking Breakdown --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Original Booking Breakdown') }}</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="info-label">{{ __('Hall Rental') }}</td>
                        <td class="text-right">{{ $formattedHallPrice }} OMR</td>
                    </tr>
                    @if($extraServices->count() > 0)
                        <tr>
                            <td class="info-label">{{ __('Extra Services') }}</td>
                            <td class="text-right">{{ $formattedServicesPrice }} OMR</td>
                        </tr>
                        @foreach($extraServices as $service)
                            <tr>
                                <td style="padding: 3px 8px 3px 16px; color: #9ca3af; font-size: 7pt;">
                                    &bull; {{ $service['name'] }} (x{{ $service['quantity'] }})
                                </td>
                                <td class="text-right" style="padding: 3px 8px; color: #9ca3af; font-size: 7pt;">
                                    {{ $service['total_price'] }} OMR
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @if($promoCode && (float)($booking->discount_amount ?? 0) > 0)
                    <tr style="background: #f0fdf4;">
                        <td style="color: #059669; padding: 5px 8px;">
                            {{ __('Promo Code') }}: {{ $promoCode->code }}
                            ({{ $promoCode->discount_label }})
                        </td>
                        <td class="text-right" style="color: #059669; font-weight: bold; padding: 5px 8px;">
                            - {{ $formattedDiscount }} OMR
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="info-label">{{ __('Platform Fee') }}</td>
                        <td class="text-right">{{ $formattedCommission }} OMR</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 3: How to Pay + Important Information (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            {{-- How to Pay --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('How to Pay the Balance') }}</div>
                <div style="font-size: 7.5pt; line-height: 1.8;">
                    <span class="info-label">{{ __('Amount to Pay') }}:</span>
                    <strong style="color: #dc2626;"> {{ $formattedBalance }} OMR</strong><br>
                    <span class="info-label">{{ __('Payment Deadline') }}:</span>
                    <strong> {{ $paymentDeadline->format('d/m/Y') }}</strong>
                </div>
                <div style="font-size: 7.5pt; margin-top: 6px; line-height: 1.8;">
                    <strong>{{ __('Accepted Payment Methods') }}:</strong><br>
                    &bull; <strong>{{ __('Bank Transfer') }}:</strong> {{ __('Contact hall owner for bank details') }}<br>
                    &bull; <strong>{{ __('Cash Payment') }}:</strong> {{ __('Arrange with hall owner') }}<br>
                    &bull; <strong>{{ __('Mobile Payment') }}:</strong> {{ __('As agreed with hall owner') }}
                </div>
                <div style="margin-top: 8px; padding: 6px 8px; background: #fef2f2;
                    border: 1px solid #fca5a5; font-size: 7.5pt; line-height: 1.8;">
                    <strong style="color: #dc2626;">{{ __('Hall Owner Contact') }}</strong><br>
                    {{ __('Name') }}: {{ $ownerName }}<br>
                    {{ __('Phone') }}: {{ $ownerPhone }}
                </div>
            </td>

            <td width="2%"></td>

            {{-- Important Information --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Important Information') }}</div>
                <table width="100%" cellpadding="0" cellspacing="0"
                       style="border: 1px solid #fbbf24; background: #fffbeb; margin-bottom: 8px;">
                    <tr>
                        <td style="padding: 8px; font-size: 7.5pt; line-height: 1.8; color: #333333;">
                            &bull; {{ __('Your advance payment of') }}
                            <strong>{{ $formattedAdvance }} OMR</strong> {{ __('has been received') }}<br>
                            &bull; {{ __('The balance must be paid before') }}
                            <strong>{{ $paymentDeadline->format('d/m/Y') }}</strong><br>
                            &bull; {{ __('Failure to pay may result in automatic booking cancellation') }}<br>
                            &bull; {{ __('After payment, please inform the hall owner and platform') }}<br>
                            &bull; {{ __('Keep proof of payment for your records') }}
                        </td>
                    </tr>
                </table>

                <div class="section-title" style="margin-top: 8px;">{{ __('Need Help?') }}</div>
                <div style="font-size: 7.5pt; line-height: 1.8;">
                    <strong>{{ $platformName }} {{ __('Support') }}</strong><br>
                    {{ __('Phone') }}: {{ $platformPhone }}<br>
                    {{ __('Email') }}: {{ $platformEmail }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Footer
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0"
           style="border-top: 1px solid #fca5a5; padding-top: 6px;">
        <tr>
            <td width="60%" style="vertical-align: middle;" class="footer-text">
                <strong>{{ $platformName }}</strong> &mdash;
                {{ $platformAddress }} &nbsp;|&nbsp; {{ $platformPhone }} &nbsp;|&nbsp; {{ $platformEmail }}
            </td>
            <td width="40%" style="vertical-align: middle; text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};" class="footer-text">
                <span style="color: #dc2626; font-weight: bold;">
                    {{ __('Please pay before the deadline to avoid cancellation') }}
                </span><br>
                {{ __('Generated on') }}: {{ $generatedDate->format('d/m/Y H:i:s') }}
            </td>
        </tr>
    </table>

</body>
</html>
