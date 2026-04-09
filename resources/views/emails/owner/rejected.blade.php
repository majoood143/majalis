{{--
/**
 * Owner Account Rejected Email Template
 *
 * Sent to hall owners when their account verification is rejected by admin.
 *
 * @package Resources\Views\Emails\Owner
 *
 * Variables:
 * @var \App\Models\User        $owner  - The owner user instance
 * @var string|null             $reason - The rejection reason/notes
 */
--}}
@extends('emails.layouts.base')

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $supportEmail = \App\Models\Setting::get('contact', 'support_email') ?? \App\Models\Setting::get('contact', 'email') ?? config('mail.support_email', 'support@majalis.om');
@endphp

@section('header-subtitle')
    {{ __('emails.owner.rejected.subtitle') }}
@endsection

@section('content')
    {{-- Rejected Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);">
            <span style="font-size: 50px;">✗</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #b91c1c;">{{ __('emails.owner.rejected.title') }}</h2>

    <p style="text-align: center; font-size: 18px;">
        {{ __('emails.owner.greeting', ['name' => $owner->name]) }}
    </p>

    <p style="text-align: center; color: #6b7280;">
        {{ __('emails.owner.rejected.intro') }}
    </p>

    {{-- Rejection Reason --}}
    @if($reason)
    <div class="info-box" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-color: #fca5a5;">
        <div class="info-box-header" style="color: #991b1b; border-bottom-color: #fca5a5;">
            {{ __('emails.owner.rejected.reason_title') }}
        </div>
        <p style="margin: 0; color: #7f1d1d; font-size: 15px;">{{ $reason }}</p>
    </div>
    @endif

    {{-- What To Do Next --}}
    <div class="info-box" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #93c5fd;">
        <div class="info-box-header" style="color: #1e40af; border-bottom-color: #93c5fd;">
            {{ __('emails.owner.rejected.what_next') }}
        </div>

        <div style="display: flex; align-items: flex-start; margin-bottom: 16px;">
            <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-{{ $isRtl ? 'left' : 'right' }}: 12px; flex-shrink: 0;">
                <span style="color: white; font-weight: 700;">1</span>
            </div>
            <div>
                <p style="margin: 0; font-weight: 600; color: #1f2937;">{{ __('emails.owner.rejected.step_1_title') }}</p>
                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">{{ __('emails.owner.rejected.step_1_desc') }}</p>
            </div>
        </div>

        <div style="display: flex; align-items: flex-start;">
            <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-{{ $isRtl ? 'left' : 'right' }}: 12px; flex-shrink: 0;">
                <span style="color: white; font-weight: 700;">2</span>
            </div>
            <div>
                <p style="margin: 0; font-weight: 600; color: #1f2937;">{{ __('emails.owner.rejected.step_2_title') }}</p>
                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">{{ __('emails.owner.rejected.step_2_desc') }}</p>
            </div>
        </div>
    </div>

    {{-- Support --}}
    <div class="highlight-box info">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #1e40af;">
            {{ __('emails.owner.rejected.need_help') }}
        </p>
        <p style="margin: 0; color: #3b82f6; font-size: 14px;">
            {{ __('emails.owner.rejected.support_desc') }}
            <a href="mailto:{{ $supportEmail }}" style="color: #4f46e5;">
                {{ $supportEmail }}
            </a>
        </p>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
