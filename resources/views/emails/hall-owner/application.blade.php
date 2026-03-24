@extends('emails.layouts.base')

@php
    $locale = app()->getLocale();
    $isRtl  = $locale === 'ar';
@endphp

@section('header-subtitle')
    {{ __('hall-owner.registration.email.header_subtitle') }}
@endsection

@section('content')
    {{-- Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3);">
            <span style="font-size: 40px;">🏛️</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #1f2937; font-size: 22px; font-weight: 700; margin: 0 0 8px 0;">
        {{ __('hall-owner.registration.email.title') }}
    </h2>
    <p style="text-align: center; color: #6b7280; margin: 0 0 24px 0;">
        {{ __('hall-owner.registration.email.greeting', ['name' => $admin->name]) }}
    </p>

    {{-- Applicant Info Box --}}
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
        <h3 style="color: #374151; font-size: 14px; font-weight: 600; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.05em;">
            {{ __('hall-owner.registration.email.applicant_info') }}
        </h3>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 6px 0; color: #6b7280; font-size: 13px; width: 40%;">
                    {{ __('hall-owner.registration.email.applicant_name') }}
                </td>
                <td style="padding: 6px 0; color: #111827; font-size: 13px; font-weight: 600;">
                    {{ $applicant->name }}
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280; font-size: 13px;">
                    {{ __('hall-owner.registration.email.applicant_email') }}
                </td>
                <td style="padding: 6px 0; color: #111827; font-size: 13px; font-weight: 600;">
                    {{ $applicant->email }}
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280; font-size: 13px;">
                    {{ __('hall-owner.registration.email.applicant_phone') }}
                </td>
                <td style="padding: 6px 0; color: #111827; font-size: 13px; font-weight: 600;">
                    {{ $applicant->phone ?? '—' }}
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280; font-size: 13px;">
                    {{ __('hall-owner.registration.email.business_name') }}
                </td>
                <td style="padding: 6px 0; color: #111827; font-size: 13px; font-weight: 600;">
                    {{ $businessName }}
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280; font-size: 13px;">
                    {{ __('hall-owner.registration.email.submitted_at') }}
                </td>
                <td style="padding: 6px 0; color: #111827; font-size: 13px; font-weight: 600;">
                    {{ now()->format('d M Y, H:i') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- CTA --}}
    <div style="text-align: center; margin: 28px 0;">
        <a href="{{ $adminUrl }}"
           style="display: inline-block; padding: 13px 28px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);">
            {{ __('hall-owner.registration.email.review_button') }}
        </a>
    </div>

    <div style="border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 8px;">
        <p style="color: #9ca3af; font-size: 12px; text-align: center; margin: 0;">
            {{ __('hall-owner.registration.email.footer_note') }}
        </p>
    </div>

    <p style="text-align: center; margin-top: 24px; color: #374151;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
