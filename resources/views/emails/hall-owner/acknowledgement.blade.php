@extends('emails.layouts.base')

@php
    $locale = app()->getLocale();
    $isRtl  = $locale === 'ar';
@endphp

@section('header-subtitle')
    {{ __('hall-owner.registration.email.ack_header_subtitle') }}
@endsection

@section('content')
    {{-- Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #B9916D 0%, #a47a5a 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(185,145,109,0.35);">
            <span style="font-size: 38px;">✅</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #1f2937; font-size: 22px; font-weight: 700; margin: 0 0 8px 0;">
        {{ __('hall-owner.registration.email.ack_title') }}
    </h2>
    <p style="text-align: center; color: #6b7280; margin: 0 0 24px 0;">
        {!! __('hall-owner.registration.email.ack_greeting', ['name' => $applicant->name]) !!}
    </p>

    {{-- Intro paragraph --}}
    <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 0 0 24px 0;">
        {!! __('hall-owner.registration.email.ack_intro', ['business' => $businessName]) !!}
    </p>

    {{-- Application Summary Box --}}
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
        <h3 style="color: #374151; font-size: 14px; font-weight: 600; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.05em;">
            {{ __('hall-owner.registration.email.ack_details_title') }}
        </h3>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #6b7280; font-size: 13px; width: 45%; border-bottom: 1px dashed #e5e7eb;">
                    {{ __('hall-owner.registration.email.ack_business_name') }}
                </td>
                <td style="padding: 8px 0; color: #111827; font-size: 13px; font-weight: 600; border-bottom: 1px dashed #e5e7eb;">
                    {{ $businessName }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280; font-size: 13px; border-bottom: 1px dashed #e5e7eb;">
                    {{ __('hall-owner.registration.email.ack_submitted_at') }}
                </td>
                <td style="padding: 8px 0; color: #111827; font-size: 13px; font-weight: 600; border-bottom: 1px dashed #e5e7eb;">
                    {{ now()->format('d M Y, H:i') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280; font-size: 13px;">
                    {{ __('hall-owner.registration.email.ack_status') }}
                </td>
                <td style="padding: 8px 0;">
                    <span style="display: inline-block; padding: 3px 12px; background-color: #fef3c7; color: #92400e; font-size: 12px; font-weight: 600; border-radius: 20px;">
                        {{ __('hall-owner.registration.email.ack_status_value') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Next Steps --}}
    <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; padding: 20px; margin-bottom: 24px; border-{{ $isRtl ? 'right' : 'left' }}: 4px solid #22c55e;">
        <h3 style="color: #15803d; font-size: 14px; font-weight: 700; margin: 0 0 14px 0;">
            {{ __('hall-owner.registration.email.ack_next_title') }}
        </h3>
        <table style="width: 100%; border-collapse: collapse;">
            @foreach([
                __('hall-owner.registration.email.ack_next_1'),
                __('hall-owner.registration.email.ack_next_2'),
                __('hall-owner.registration.email.ack_next_3'),
            ] as $i => $step)
            <tr>
                <td style="padding: 5px 0; vertical-align: top; width: 28px;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background-color: #22c55e; color: #fff; border-radius: 50%; font-size: 11px; font-weight: 700;">
                        {{ $i + 1 }}
                    </span>
                </td>
                <td style="padding: 5px 0 5px 8px; color: #374151; font-size: 13px; line-height: 1.6;">
                    {{ $step }}
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    {{-- Footer note --}}
    <div style="border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 8px;">
        <p style="color: #9ca3af; font-size: 12px; text-align: center; margin: 0;">
            {{ __('hall-owner.registration.email.ack_footer_note') }}
        </p>
    </div>

    <p style="text-align: center; margin-top: 24px; color: #374151;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
