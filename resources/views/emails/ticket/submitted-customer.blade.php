@extends('emails.layouts.base')

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
@endphp

@section('header-subtitle')
    {{ __('emails.ticket.submitted_customer_subtitle') }}
@endsection

@section('content')
    {{-- ✅ Success Icon --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:24px;">
        <tr>
            <td align="center">
                <div
                    style="width:80px;height:80px;background:#3b82f6;border-radius:50%;display:inline-block;line-height:80px;text-align:center;">
                    <span style="font-size:40px;color:#ffffff;line-height:80px;">✓</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Heading --}}
    <h2 style="text-align:center;color:#1e40af;font-size:24px;font-weight:700;margin:0 0 12px 0;">
        {{ __('emails.ticket.submitted_customer_title') }}
    </h2>

    <p style="text-align:center;color:#374151;font-size:16px;margin:0 0 8px 0;">
        {{ __('emails.ticket.greeting', ['name' => $customerName]) }}
    </p>

    <p style="text-align:center;color:#6b7280;font-size:15px;margin:0 0 32px 0;">
        {{ __('emails.ticket.submitted_customer_intro') }}
    </p>

    {{-- Ticket Reference Box --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin-bottom:32px;background:#f3f4f6;border-radius:8px;padding:24px;">
        <tr>
            <td style="text-align:center;">
                <p
                    style="margin:0 0 8px 0;color:#6b7280;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                    {{ __('emails.ticket.reference_label') }}
                </p>
                <p style="margin:0;color:#1f2937;font-size:28px;font-weight:700;letter-spacing:1px;font-family:monospace;">
                    {{ $ticket->ticket_number }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Ticket Details --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin-bottom:32px;border:1px solid #e5e7eb;border-radius:8px;">
        {{-- Type Row --}}
        <tr>
            <td style="padding:16px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">{{ __('emails.ticket.type') }}</p>
            </td>
            <td style="padding:16px;background:#f9fafb;border-bottom:1px solid #e5e7eb;text-align:right;">
                <p style="margin:0;color:#1f2937;font-size:14px;font-weight:500;">{{ $ticket->type->getLabel() }}</p>
            </td>
        </tr>

        {{-- Subject Row --}}
        <tr>
            <td style="padding:16px;border-bottom:1px solid #e5e7eb;">
                <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">{{ __('emails.ticket.subject') }}</p>
            </td>
            <td style="padding:16px;border-bottom:1px solid #e5e7eb;text-align:right;">
                <p style="margin:0;color:#1f2937;font-size:14px;font-weight:500;">{{ $ticket->subject }}</p>
            </td>
        </tr>

        {{-- Status Row --}}
        <tr>
            <td style="padding:16px;">
                <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">{{ __('emails.ticket.status') }}</p>
            </td>
            <td style="padding:16px;text-align:right;">
                <p style="margin:0;color:#1f2937;font-size:14px;font-weight:500;">{{ $ticket->status->getLabel() }}</p>
            </td>
        </tr>
    </table>

    {{-- Message --}}
    <p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 24px 0;">
        {{ __('emails.ticket.submitted_customer_message') }}
    </p>

    {{-- CTA Button --}}
    @if ($ticketUrl)
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
            style="margin-bottom:32px;">
            <tr>
                <td align="center">
                    <a href="{{ $ticketUrl }}"
                        style="display:inline-block;padding:12px 32px;background:#3b82f6;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;">
                        {{ __('emails.ticket.view_ticket_btn') }}
                    </a>
                </td>
            </tr>
        </table>
    @endif

    {{-- Footer Info --}}
    <p style="color:#6b7280;font-size:13px;line-height:1.6;margin:0;">
        {{ __('emails.ticket.submitted_customer_footer') }}
    </p>
@endsection
