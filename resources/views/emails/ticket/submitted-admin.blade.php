@extends('emails.layouts.base')

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
@endphp

@section('header-subtitle')
    {{ __('emails.ticket.submitted_admin_subtitle') }}
@endsection

@section('content')
    {{-- 🔔 Alert Icon --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:24px;">
        <tr>
            <td align="center">
                <div
                    style="width:80px;height:80px;background:#f59e0b;border-radius:50%;display:inline-block;line-height:80px;text-align:center;">
                    <span style="font-size:40px;color:#ffffff;line-height:80px;">🔔</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Heading --}}
    <h2 style="text-align:center;color:#b45309;font-size:24px;font-weight:700;margin:0 0 12px 0;">
        {{ __('emails.ticket.submitted_admin_title') }}
    </h2>

    <p style="text-align:center;color:#374151;font-size:16px;margin:0 0 32px 0;">
        {{ __('emails.ticket.new_ticket_submitted') }}
    </p>

    {{-- Ticket Reference Box --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin-bottom:32px;background:#fef3c7;border-left:4px solid #f59e0b;padding:20px;border-radius:4px;">
        <tr>
            <td>
                <p style="margin:0 0 8px 0;color:#6b7280;font-size:13px;font-weight:600;">Ticket #</p>
                <p style="margin:0;color:#1f2937;font-size:20px;font-weight:700;font-family:monospace;">
                    {{ $ticket->ticket_number }}</p>
            </td>
        </tr>
    </table>

    {{-- Customer Info --}}
    <h3 style="color:#1f2937;font-size:16px;font-weight:600;margin:24px 0 12px 0;">
        {{ __('emails.ticket.customer_information') }}
    </h3>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin-bottom:24px;border:1px solid #e5e7eb;border-radius:8px;">
        <tr>
            <td style="padding:12px 16px;background:#f9fafb;border-right:1px solid #e5e7eb;">
                <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">{{ __('emails.ticket.name') }}</p>
            </td>
            <td style="padding:12px 16px;background:#f9fafb;">
                <p style="margin:0;color:#1f2937;font-size:14px;">{{ $customerName }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-right:1px solid #e5e7eb;">
                <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">{{ __('emails.ticket.email') }}</p>
            </td>
            <td style="padding:12px 16px;">
                <p style="margin:0;color:#1f2937;font-size:14px;">
                    @if ($customerEmail !== 'N/A')
                        <a href="mailto:{{ $customerEmail }}"
                            style="color:#3b82f6;text-decoration:none;">{{ $customerEmail }}</a>
                    @else
                        {{ $customerEmail }}
                    @endif
                </p>
            </td>
        </tr>
        @if ($isGuestTicket)
            <tr>
                <td
                    style="padding:12px 16px;background:#fef3c7;border-top:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">
                    <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">
                        {{ __('emails.ticket.submission_type') }}</p>
                </td>
                <td style="padding:12px 16px;background:#fef3c7;border-top:1px solid #e5e7eb;">
                    <p style="margin:0;color:#b45309;font-size:14px;font-weight:600;">
                        {{ __('emails.ticket.guest_submission') }}</p>
                </td>
            </tr>
        @endif
    </table>

    {{-- Ticket Details --}}
    <h3 style="color:#1f2937;font-size:16px;font-weight:600;margin:24px 0 12px 0;">
        {{ __('emails.ticket.ticket_details') }}
    </h3>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin-bottom:24px;border:1px solid #e5e7eb;border-radius:8px;">
        <tr>
            <td style="padding:12px 16px;background:#f9fafb;border-right:1px solid #e5e7eb;">
                <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">{{ __('emails.ticket.type') }}</p>
            </td>
            <td style="padding:12px 16px;background:#f9fafb;">
                <p style="margin:0;color:#1f2937;font-size:14px;font-weight:500;">{{ $ticket->type->getLabel() }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:12px 16px;border-right:1px solid #e5e7eb;">
                <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">{{ __('emails.ticket.priority') }}</p>
            </td>
            <td style="padding:12px 16px;">
                <p style="margin:0;color:#1f2937;font-size:14px;font-weight:500;">{{ $ticket->priority->getLabel() }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:12px 16px;background:#f9fafb;border-top:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">
                <p style="margin:0;color:#6b7280;font-size:13px;font-weight:600;">{{ __('emails.ticket.subject') }}</p>
            </td>
            <td style="padding:12px 16px;background:#f9fafb;border-top:1px solid #e5e7eb;">
                <p style="margin:0;color:#1f2937;font-size:14px;font-weight:500;">{{ $ticket->subject }}</p>
            </td>
        </tr>
    </table>

    {{-- Description --}}
    <h3 style="color:#1f2937;font-size:16px;font-weight:600;margin:24px 0 12px 0;">
        {{ __('emails.ticket.description') }}
    </h3>

    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:24px;">
        <p style="color:#374151;font-size:14px;line-height:1.6;margin:0;white-space:pre-wrap;">{{ $ticket->description }}
        </p>
    </div>

    {{-- CTA Button --}}
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:32px;">
        <tr>
            <td align="center">
                <a href="{{ $ticketUrl }}"
                    style="display:inline-block;padding:12px 32px;background:#3b82f6;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;">
                    {{ __('emails.ticket.view_and_respond_btn') }}
                </a>
            </td>
        </tr>
    </table>

    {{-- Footer Info --}}
    <p style="color:#6b7280;font-size:13px;line-height:1.6;margin:0;">
        {{ __('emails.ticket.admin_footer_note') }}
    </p>
@endsection
