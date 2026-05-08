@php
    $locale = app()->getLocale();

    // Safely extract a string from a possibly-array/translatable value.
    // Handles nested arrays returned by Setting::get() (json cast) or Translatable fields.
    $resolveStr = function (mixed $v, string $fallback) use ($locale): string {
        if (is_array($v)) {
            $v = $v[$locale] ?? $v['ar'] ?? $v['en'] ?? $fallback;
        }
        if (is_string($v) && $v !== '') return $v;
        if (is_numeric($v)) return (string) $v;
        return $fallback;
    };

    $safeHallName = 'N/A';
    if ($hall) {
        $raw = $hall->name;
        if (is_array($raw)) {
            $safeHallName = $raw[$locale] ?? $raw['ar'] ?? $raw['en'] ?? 'N/A';
        } elseif (is_string($raw) && $raw !== '') {
            $safeHallName = $raw;
        }
        $safeHallName = is_string($safeHallName) ? $safeHallName : 'N/A';
    }

    $safeCustomerName  = (string) (is_array($booking?->customer_name)  ? ($booking->customer_name[$locale]  ?? 'N/A') : ($booking?->customer_name  ?? 'N/A'));
    $safeCustomerPhone = (string) (is_array($booking?->customer_phone) ? ($booking->customer_phone[$locale] ?? 'N/A') : ($booking?->customer_phone ?? 'N/A'));
    $safeCustomerEmail = (string) (is_array($booking?->customer_email) ? ($booking->customer_email[$locale] ?? 'N/A') : ($booking?->customer_email ?? 'N/A'));
    $safeTimeSlot      = (string) (is_array($booking?->time_slot)      ? ($booking->time_slot[$locale]      ?? 'N/A') : ucfirst(str_replace('_', ' ', $booking?->time_slot ?? 'N/A')));
    $safePaymentType   = (string) (is_array($booking?->payment_type)   ? ($booking->payment_type[$locale]   ?? 'N/A') : ucfirst(str_replace('_', ' ', $booking?->payment_type ?? 'N/A')));
    $safePaymentMethod = (string) (is_array($payment->payment_method)  ? ($payment->payment_method[$locale] ?? 'N/A') : ucfirst($payment->payment_method ?? 'N/A'));
    $safeStatus        = strtoupper(str_replace('_', ' ', is_string($payment->status) ? $payment->status : 'N/A'));
    $safeRefundReason  = (string) (is_array($payment->refund_reason)   ? ($payment->refund_reason[$locale]  ?? '')    : ($payment->refund_reason ?? ''));

    $safePaymentRef   = $resolveStr($payment->payment_reference, 'N/A');
    $safeCurrency     = $resolveStr($payment->currency, 'OMR');
    $safeTransId      = $resolveStr($payment->transaction_id, '');
    $safeBookingNum   = $booking ? $resolveStr($booking->booking_number, 'N/A') : 'N/A';

    $safePlatformName    = $resolveStr($platformName,    'Majalis');
    $safePlatformAddress = $resolveStr($platformAddress, 'Muscat, Oman');
    $safePlatformPhone   = $resolveStr($platformPhone,   '+968 9999 9999');
    $safePlatformEmail   = $resolveStr($platformEmail,   'info@majalis.om');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payment Receipt - {{ $safePaymentRef }}</title>
    <style>
        * { margin: 0; padding: 0; }

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
                     alt="{{ $safePlatformName }}"
                     style="height: 40px; display: block; margin-bottom: 6px;">
                <div style="font-size: 7pt; color: #444444; line-height: 1.6;">
                    {{ $safePlatformAddress }}<br>
                    {{ __('Phone') }}: {{ $safePlatformPhone }}<br>
                    {{ __('Email') }}: {{ $safePlatformEmail }}
                </div>
            </td>
            <td width="50%" style="vertical-align: top; text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};">
                <div style="font-size: 16pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    {{ __('Payment Receipt') }}
                </div>
                <div style="font-size: 8pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    {{ __('Receipt Number') }}: {{ $safePaymentRef }}
                </div>
                <div style="font-size: 7pt; color: #444444; margin-bottom: 6px;">
                    {{ __('Generated') }}: {{ $generatedDate->format('d/m/Y H:i') }}
                    @if($booking)
                        &nbsp;|&nbsp; {{ __('Booking') }}: {{ $safeBookingNum }}
                    @endif
                </div>
                <span class="badge">{{ $safeStatus }}</span>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Status Banner
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            <td style="border: 1.5px solid #000000; background: #f9f9f9;
                text-align: center; padding: 10px;">
                <div style="font-size: 11pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    @if((string) $payment->status === 'paid')
                        {{ __('PAYMENT RECEIVED SUCCESSFULLY') }}
                    @elseif((string) $payment->status === 'refunded')
                        {{ __('PAYMENT REFUNDED') }}
                    @else
                        {{ __('PAYMENT PROCESSED') }}
                    @endif
                </div>
                <div style="font-size: 20pt; font-weight: bold; color: #000000; margin: 4px 0;">
                    {{ number_format((float) $payment->amount, 3) }} {{ $safeCurrency }}
                </div>
                <div style="font-size: 7.5pt; color: #444444;">
                    {{ $safeTransId ? __('Transaction ID') . ': ' . $safeTransId : '' }}
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
                    <strong>{{ $safePlatformName }}</strong><br>
                    {{ $safePlatformAddress }}<br>
                    {{ __('Phone') }}: {{ $safePlatformPhone }}<br>
                    {{ __('Email') }}: {{ $safePlatformEmail }}
                </div>
            </td>
            <td width="2%"></td>
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Receipt To') }}</div>
                <div style="font-size: 8pt; line-height: 1.8;">
                    @if($booking)
                        <strong>{{ $safeCustomerName }}</strong><br>
                        {{ __('Phone') }}: {{ $safeCustomerPhone }}<br>
                        {{ __('Email') }}: {{ $safeCustomerEmail }}
                    @else
                        <span style="color: #444444;">{{ __('N/A') }}</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 2: Payment Details + Booking Details (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            {{-- Payment Details --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Payment Details') }}</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="info-label">{{ __('Payment Method') }}</td>
                        <td class="text-right">{{ $safePaymentMethod }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('Currency') }}</td>
                        <td class="text-right">{{ $safeCurrency }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('Payment Date') }}</td>
                        <td class="text-right">
                            {{ ($payment->paid_at ?? $payment->created_at)?->format('d/m/Y H:i') ?? 'N/A' }}
                        </td>
                    </tr>
                    @if($safeTransId)
                    <tr>
                        <td class="info-label">{{ __('Transaction ID') }}</td>
                        <td class="text-right">{{ $safeTransId }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 1.5px solid #000000;">
                        <td style="font-weight: bold; padding: 6px 8px; font-size: 9pt;">
                            {{ __('Amount Paid') }}
                        </td>
                        <td class="text-right" style="font-weight: bold; padding: 6px 8px; font-size: 9pt;">
                            {{ number_format((float) $payment->amount, 3) }} {{ $safeCurrency }}
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Booking Details --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('Booking Details') }}</div>
                @if($booking)
                <table class="data-table" width="100%">
                    <tr>
                        <td class="info-label">{{ __('Booking Number') }}</td>
                        <td class="text-right">{{ $safeBookingNum }}</td>
                    </tr>
                    @if($hall)
                    <tr>
                        <td class="info-label">{{ __('Hall Name') }}</td>
                        <td class="text-right">{{ $safeHallName }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="info-label">{{ __('Event Date') }}</td>
                        <td class="text-right">
                            {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('Time Slot') }}</td>
                        <td class="text-right">{{ $safeTimeSlot }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('Payment Type') }}</td>
                        <td class="text-right">{{ $safePaymentType }}</td>
                    </tr>
                </table>
                @else
                <div style="font-size: 7.5pt; color: #444444; padding: 8px;">
                    {{ __('No booking linked to this payment.') }}
                </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Refund Section (only if applicable)
        ======================================================================== --}}
    @if((float)($payment->refund_amount ?? 0) > 0)
    <div class="section-title" style="margin-bottom: 6px;">{{ __('Refund Information') }}</div>
    <table class="data-table" width="100%" style="margin-bottom: 12px;">
        <tr>
            <td class="info-label">{{ __('Refund Amount') }}</td>
            <td class="text-right" style="font-weight: bold;">
                {{ number_format((float) $payment->refund_amount, 3) }} {{ $safeCurrency }}
            </td>
        </tr>
        @if($safeRefundReason)
        <tr>
            <td class="info-label">{{ __('Refund Reason') }}</td>
            <td class="text-right">{{ $safeRefundReason }}</td>
        </tr>
        @endif
    </table>
    @endif

    {{-- ========================================================================
        Notice
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            <td style="border: 1px solid #cccccc; background: #f9f9f9; padding: 8px; font-size: 7.5pt; line-height: 1.8;">
                &bull; {{ __('Please keep this receipt as proof of payment.') }}<br>
                &bull; {{ __('This is a computer-generated receipt and does not require a signature.') }}<br>
                &bull; {{ __('All prices are in Omani Rial (OMR).') }}<br>
                &bull; {{ __('For any payment disputes or queries, please contact our support team.') }}
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
                <strong>{{ $safePlatformName }}</strong> &mdash;
                {{ $safePlatformAddress }} &nbsp;|&nbsp; {{ $safePlatformPhone }} &nbsp;|&nbsp; {{ $safePlatformEmail }}
            </td>
            <td width="40%" style="vertical-align: middle; text-align: {{ app()->isLocale('ar') ? 'left' : 'right' }};" class="footer-text">
                {{ __('Thank you for your business!') }}<br>
                {{ __('This is an automated receipt. Generated on') }}: {{ $generatedDate->format('d/m/Y H:i:s') }}
            </td>
        </tr>
    </table>

</body>
</html>
