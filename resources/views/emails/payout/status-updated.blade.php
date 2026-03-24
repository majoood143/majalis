@extends('emails.Layouts.base')

@php
    use App\Enums\PayoutStatus;

    $locale  = $locale ?? app()->getLocale();
    $isRtl   = $locale === 'ar';
    $subject = $title;

    $colorMap = [
        'warning' => ['bg' => '#fffbeb', 'border' => '#f59e0b', 'badge_bg' => '#fef3c7', 'badge_text' => '#92400e', 'header' => 'linear-gradient(135deg,#f59e0b 0%,#d97706 100%)'],
        'info'    => ['bg' => '#eff6ff', 'border' => '#3b82f6', 'badge_bg' => '#dbeafe', 'badge_text' => '#1e40af', 'header' => 'linear-gradient(135deg,#3b82f6 0%,#2563eb 100%)'],
        'success' => ['bg' => '#ecfdf5', 'border' => '#10b981', 'badge_bg' => '#d1fae5', 'badge_text' => '#065f46', 'header' => 'linear-gradient(135deg,#10b981 0%,#059669 100%)'],
        'danger'  => ['bg' => '#fef2f2', 'border' => '#ef4444', 'badge_bg' => '#fee2e2', 'badge_text' => '#991b1b', 'header' => 'linear-gradient(135deg,#ef4444 0%,#dc2626 100%)'],
        'gray'    => ['bg' => '#f9fafb', 'border' => '#6b7280', 'badge_bg' => '#f3f4f6', 'badge_text' => '#374151', 'header' => 'linear-gradient(135deg,#6b7280 0%,#4b5563 100%)'],
    ];

    $statusColor = $payout->status->getColor();
    $colors      = $colorMap[$statusColor] ?? $colorMap['gray'];
    $statusLabel = $payout->status->getLabel();
    $borderSide  = $isRtl ? 'border-right' : 'border-left';
@endphp

@section('header-subtitle')
    {{ $payout->payout_number }}
@endsection

@section('content')

{{-- Greeting --}}
<h2 style="color:#1f2937; font-size:22px; font-weight:700; margin:0 0 8px 0;">
    {{ $isRtl ? 'مرحباً' : 'Hello' }}, {{ $owner->name }}
</h2>
<p style="color:#4b5563; font-size:15px; margin:0 0 24px 0;">
    {{ $body }}
</p>

{{-- Status Badge --}}
<p style="margin:0 0 24px 0;">
    <span style="display:inline-block; padding:6px 18px; font-size:13px; font-weight:700;
                 border-radius:20px; background-color:{{ $colors['badge_bg'] }};
                 color:{{ $colors['badge_text'] }}; letter-spacing:0.5px; text-transform:uppercase;">
        {{ $statusLabel }}
    </span>
</p>

{{-- Payout Details Box --}}
<div style="background-color:{{ $colors['bg'] }}; border-radius:12px; padding:24px;
            margin:0 0 24px 0; {{ $borderSide }}:4px solid {{ $colors['border'] }};">

    <p style="font-size:13px; font-weight:700; color:#6b7280; text-transform:uppercase;
              letter-spacing:0.5px; margin:0 0 16px 0; padding-bottom:12px;
              border-bottom:1px solid {{ $colors['border'] }}40;">
        {{ $isRtl ? 'تفاصيل الدفعة' : 'Payout Details' }}
    </p>

    {{-- Payout Number --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
           style="margin-bottom:8px;">
        <tr>
            <td style="color:#6b7280; font-size:14px; padding:6px 0;">
                {{ $isRtl ? 'رقم الدفعة' : 'Payout Number' }}
            </td>
            <td style="color:#1f2937; font-size:14px; font-weight:700;
                       text-align:{{ $isRtl ? 'left' : 'right' }}; padding:6px 0;">
                {{ $payout->payout_number }}
            </td>
        </tr>
        <tr>
            <td style="color:#6b7280; font-size:14px; padding:6px 0;
                       border-top:1px dashed #e5e7eb;">
                {{ $isRtl ? 'الفترة' : 'Period' }}
            </td>
            <td style="color:#1f2937; font-size:14px; font-weight:600;
                       text-align:{{ $isRtl ? 'left' : 'right' }}; padding:6px 0;
                       border-top:1px dashed #e5e7eb;">
                {{ $payout->period_start->format('d M Y') }} – {{ $payout->period_end->format('d M Y') }}
            </td>
        </tr>
        <tr>
            <td style="color:#6b7280; font-size:14px; padding:6px 0;
                       border-top:1px dashed #e5e7eb;">
                {{ $isRtl ? 'الإيرادات الإجمالية' : 'Gross Revenue' }}
            </td>
            <td style="color:#1f2937; font-size:14px; font-weight:600;
                       text-align:{{ $isRtl ? 'left' : 'right' }}; padding:6px 0;
                       border-top:1px dashed #e5e7eb;">
                {{ number_format((float) $payout->gross_revenue, 3) }} OMR
            </td>
        </tr>
        <tr>
            <td style="color:#6b7280; font-size:14px; padding:6px 0;
                       border-top:1px dashed #e5e7eb;">
                {{ $isRtl ? 'العمولة' : 'Commission' }}
            </td>
            <td style="color:#ef4444; font-size:14px; font-weight:600;
                       text-align:{{ $isRtl ? 'left' : 'right' }}; padding:6px 0;
                       border-top:1px dashed #e5e7eb;">
                – {{ number_format((float) $payout->commission_amount, 3) }} OMR
            </td>
        </tr>
        <tr>
            <td style="color:#6b7280; font-size:14px; padding:10px 0 6px;
                       border-top:2px solid {{ $colors['border'] }};">
                <strong>{{ $isRtl ? 'صافي المبلغ' : 'Net Payout' }}</strong>
            </td>
            <td style="font-size:20px; font-weight:800; color:#059669;
                       text-align:{{ $isRtl ? 'left' : 'right' }}; padding:10px 0 6px;
                       border-top:2px solid {{ $colors['border'] }};">
                {{ number_format((float) $payout->net_payout, 3) }} OMR
            </td>
        </tr>
    </table>

</div>

{{-- Status-specific message --}}
@if($payout->status === PayoutStatus::FAILED && $payout->failure_reason)
<div style="background-color:#fef2f2; border-radius:8px; padding:16px; margin:0 0 24px 0;
            {{ $borderSide }}:4px solid #ef4444;">
    <p style="color:#991b1b; font-size:14px; font-weight:700; margin:0 0 6px 0;">
        {{ $isRtl ? 'سبب الفشل' : 'Failure Reason' }}
    </p>
    <p style="color:#7f1d1d; font-size:14px; margin:0;">{{ $payout->failure_reason }}</p>
</div>
@endif

@if($payout->status === PayoutStatus::COMPLETED && $payout->transaction_reference)
<div style="background-color:#ecfdf5; border-radius:8px; padding:16px; margin:0 0 24px 0;
            {{ $borderSide }}:4px solid #10b981;">
    <p style="color:#065f46; font-size:14px; font-weight:700; margin:0 0 6px 0;">
        {{ $isRtl ? 'رقم المرجع' : 'Transaction Reference' }}
    </p>
    <p style="color:#064e3b; font-size:14px; font-family:monospace; margin:0;">
        {{ $payout->transaction_reference }}
    </p>
</div>
@endif

{{-- CTA Button --}}
<p style="text-align:center; margin:32px 0 24px;">
    <a href="{{ url('/owner/payouts/' . $payout->id) }}"
       style="display:inline-block; padding:14px 36px; font-size:15px; font-weight:700;
              text-decoration:none; border-radius:8px; color:#ffffff !important;
              background:{{ $colors['header'] }};">
        {{ $isRtl ? 'عرض تفاصيل الدفعة' : 'View Payout Details' }}
    </a>
</p>

{{-- Footer note --}}
<div class="divider"></div>
<p style="color:#9ca3af; font-size:13px; text-align:center; margin:0;">
    {{ $isRtl
        ? 'هذا إشعار تلقائي. يرجى عدم الرد على هذا البريد.'
        : 'This is an automated notification. Please do not reply to this email.' }}
</p>

@endsection
