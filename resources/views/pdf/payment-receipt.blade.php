@php
    $locale   = app()->getLocale();
    $isRtl    = app()->isLocale('ar');
    $dir      = $isRtl ? 'rtl' : 'ltr';
    $align    = $isRtl ? 'right' : 'left';
    $alignOpp = $isRtl ? 'left' : 'right';

    $resolveStr = fn(mixed $v, string $fb) => match(true) {
        is_array($v)               => (string)($v[$locale] ?? $v['ar'] ?? $v['en'] ?? $fb),
        is_string($v) && $v !== '' => $v,
        is_numeric($v)             => (string)$v,
        default                    => $fb,
    };

    $safeHallName        = $hall    ? $resolveStr($hall->name, 'N/A')                                              : 'N/A';
    $safeCustomerName    = $resolveStr($booking?->customer_name,  'N/A');
    $safeCustomerPhone   = $resolveStr($booking?->customer_phone, 'N/A');
    $safeCustomerEmail   = $resolveStr($booking?->customer_email, 'N/A');
    $safeTimeSlot        = $booking ? ucfirst(str_replace('_', ' ', $resolveStr($booking->time_slot,    'N/A'))) : 'N/A';
    $safePaymentType     = $booking ? ucfirst(str_replace('_', ' ', $resolveStr($booking->payment_type, 'N/A'))) : 'N/A';
    $safePaymentMethod   = ucfirst($resolveStr($payment->payment_method, 'N/A'));
    $safeStatus          = strtoupper(str_replace('_', ' ', $resolveStr($payment->status, 'N/A')));
    $safeRefundReason    = $resolveStr($payment->refund_reason, '');
    $safePaymentRef      = $resolveStr($payment->payment_reference, 'N/A');
    $safeCurrency        = $resolveStr($payment->currency, 'OMR');
    $safeTransId         = $resolveStr($payment->transaction_id, '');
    $safeBookingNum      = $booking ? $resolveStr($booking->booking_number, 'N/A') : 'N/A';
    $safePlatformName    = $resolveStr($platformName,    'Majalis');
    $safePlatformAddress = $resolveStr($platformAddress, 'Muscat, Oman');
    $safePlatformPhone   = $resolveStr($platformPhone,   '+968 9999 9999');
    $safePlatformEmail   = $resolveStr($platformEmail,   'info@majalis.om');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Payment Receipt - {{ $safePaymentRef }}</title>
<style>
*{margin:0;padding:0}
body{font-family:'DejaVu Sans',sans-serif;font-size:8pt;color:#000;line-height:1.4;background:#fff;direction:{{ $dir }};text-align:{{ $align }}}
.section-title{font-size:9pt;font-weight:bold;padding-bottom:3px;border-bottom:1.5px solid #000;margin-bottom:8px}
.data-table{width:100%;border-collapse:collapse;font-size:7.5pt}
.data-table th{background:#f0f0f0;font-weight:bold;font-size:7pt;text-transform:uppercase;padding:5px 8px;border-bottom:1px solid #999;text-align:{{ $align }}}
.data-table td{padding:5px 8px;border-bottom:1px solid #e5e5e5;text-align:{{ $align }}}
.data-table tbody tr:nth-child(even) td{background:#fafafa}
.text-right{text-align:{{ $alignOpp }}}
.text-center{text-align:center}
.info-label{color:#444;font-size:7pt}
.badge{display:inline-block;padding:2px 8px;font-size:7pt;font-weight:bold;text-transform:uppercase;border:1.5px solid #000}
.footer-text{font-size:6.5pt;color:#555}
</style>
</head>
<body>

<table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:2px solid #000;margin-bottom:12px;padding-bottom:8px">
    <tr>
        <td width="50%" style="vertical-align:top">
            <img src="{{ public_path(config('app.logo_path')) }}" alt="{{ $safePlatformName }}" style="height:40px;display:block;margin-bottom:6px">
            <div style="font-size:7pt;color:#444;line-height:1.6">
                {{ $safePlatformAddress }}<br>
                {{ __('Phone') }}: {{ $safePlatformPhone }}<br>
                {{ __('Email') }}: {{ $safePlatformEmail }}
            </div>
        </td>
        <td width="50%" style="vertical-align:top;text-align:{{ $alignOpp }}">
            <div style="font-size:16pt;font-weight:bold;margin-bottom:2px">{{ __('Payment Receipt') }}</div>
            <div style="font-size:8pt;font-weight:bold;margin-bottom:2px">{{ __('Receipt Number') }}: {{ $safePaymentRef }}</div>
            <div style="font-size:7pt;color:#444;margin-bottom:6px">
                {{ __('Generated') }}: {{ $generatedDate->format('d/m/Y H:i') }}
                @if($booking) &nbsp;|&nbsp; {{ __('Booking') }}: {{ $safeBookingNum }} @endif
            </div>
            <span class="badge">{{ $safeStatus }}</span>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px">
    <tr>
        <td style="border:1.5px solid #000;background:#f9f9f9;text-align:center;padding:10px">
            <div style="font-size:11pt;font-weight:bold;margin-bottom:2px">
                @if((string)$payment->status === 'paid') {{ __('PAYMENT RECEIVED SUCCESSFULLY') }}
                @elseif((string)$payment->status === 'refunded') {{ __('PAYMENT REFUNDED') }}
                @else {{ __('PAYMENT PROCESSED') }}
                @endif
            </div>
            <div style="font-size:20pt;font-weight:bold;margin:4px 0">{{ number_format((float)$payment->amount, 3) }} {{ $safeCurrency }}</div>
            @if($safeTransId)<div style="font-size:7.5pt;color:#444">{{ __('Transaction ID') }}: {{ $safeTransId }}</div>@endif
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px">
    <tr>
        <td width="49%" style="vertical-align:top">
            <div class="section-title">{{ __('From') }}</div>
            <div style="font-size:8pt;line-height:1.8">
                <strong>{{ $safePlatformName }}</strong><br>
                {{ $safePlatformAddress }}<br>
                {{ __('Phone') }}: {{ $safePlatformPhone }}<br>
                {{ __('Email') }}: {{ $safePlatformEmail }}
            </div>
        </td>
        <td width="2%"></td>
        <td width="49%" style="vertical-align:top">
            <div class="section-title">{{ __('Receipt To') }}</div>
            <div style="font-size:8pt;line-height:1.8">
                @if($booking)
                    <strong>{{ $safeCustomerName }}</strong><br>
                    {{ __('Phone') }}: {{ $safeCustomerPhone }}<br>
                    {{ __('Email') }}: {{ $safeCustomerEmail }}
                @else
                    <span style="color:#444">{{ __('N/A') }}</span>
                @endif
            </div>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px">
    <tr>
        <td width="49%" style="vertical-align:top">
            <div class="section-title">{{ __('Payment Details') }}</div>
            <table class="data-table" width="100%">
                <tr><td class="info-label">{{ __('Payment Method') }}</td><td class="text-right">{{ $safePaymentMethod }}</td></tr>
                <tr><td class="info-label">{{ __('Currency') }}</td><td class="text-right">{{ $safeCurrency }}</td></tr>
                <tr>
                    <td class="info-label">{{ __('Payment Date') }}</td>
                    <td class="text-right">{{ ($payment->paid_at ?? $payment->created_at)?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                </tr>
                @if($safeTransId)
                <tr><td class="info-label">{{ __('Transaction ID') }}</td><td class="text-right">{{ $safeTransId }}</td></tr>
                @endif
                <tr style="border-top:1.5px solid #000">
                    <td style="font-weight:bold;padding:6px 8px;font-size:9pt">{{ __('Amount Paid') }}</td>
                    <td class="text-right" style="font-weight:bold;padding:6px 8px;font-size:9pt">{{ number_format((float)$payment->amount, 3) }} {{ $safeCurrency }}</td>
                </tr>
            </table>
        </td>
        <td width="2%"></td>
        <td width="49%" style="vertical-align:top">
            <div class="section-title">{{ __('Booking Details') }}</div>
            @if($booking)
            <table class="data-table" width="100%">
                <tr><td class="info-label">{{ __('Booking Number') }}</td><td class="text-right">{{ $safeBookingNum }}</td></tr>
                @if($hall)
                <tr><td class="info-label">{{ __('Hall Name') }}</td><td class="text-right">{{ $safeHallName }}</td></tr>
                @endif
                <tr>
                    <td class="info-label">{{ __('Event Date') }}</td>
                    <td class="text-right">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
                <tr><td class="info-label">{{ __('Time Slot') }}</td><td class="text-right">{{ $safeTimeSlot }}</td></tr>
                <tr><td class="info-label">{{ __('Payment Type') }}</td><td class="text-right">{{ $safePaymentType }}</td></tr>
            </table>
            @else
            <div style="font-size:7.5pt;color:#444;padding:8px">{{ __('No booking linked to this payment.') }}</div>
            @endif
        </td>
    </tr>
</table>

@if((float)($payment->refund_amount ?? 0) > 0)
<div class="section-title" style="margin-bottom:6px">{{ __('Refund Information') }}</div>
<table class="data-table" width="100%" style="margin-bottom:12px">
    <tr>
        <td class="info-label">{{ __('Refund Amount') }}</td>
        <td class="text-right" style="font-weight:bold">{{ number_format((float)$payment->refund_amount, 3) }} {{ $safeCurrency }}</td>
    </tr>
    @if($safeRefundReason)
    <tr><td class="info-label">{{ __('Refund Reason') }}</td><td class="text-right">{{ $safeRefundReason }}</td></tr>
    @endif
</table>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px">
    <tr>
        <td style="border:1px solid #ccc;background:#f9f9f9;padding:8px;font-size:7.5pt;line-height:1.8">
            &bull; {{ __('Please keep this receipt as proof of payment.') }}<br>
            &bull; {{ __('This is a computer-generated receipt and does not require a signature.') }}<br>
            &bull; {{ __('All prices are in Omani Rial (OMR).') }}<br>
            &bull; {{ __('For any payment disputes or queries, please contact our support team.') }}
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #ccc;padding-top:6px">
    <tr>
        <td width="60%" style="vertical-align:middle" class="footer-text">
            <strong>{{ $safePlatformName }}</strong> &mdash; {{ $safePlatformAddress }} &nbsp;|&nbsp; {{ $safePlatformPhone }} &nbsp;|&nbsp; {{ $safePlatformEmail }}
        </td>
        <td width="40%" style="vertical-align:middle;text-align:{{ $alignOpp }}" class="footer-text">
            {{ __('Thank you for your business!') }}<br>
            {{ __('This is an automated receipt. Generated on') }}: {{ $generatedDate->format('d/m/Y H:i:s') }}
        </td>
    </tr>
</table>

</body>
</html>
