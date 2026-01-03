{{--
/**
 * Owner Account Verified Email Template
 *
 * Sent to hall owners when their account is verified by admin.
 * Welcomes them and provides next steps.
 *
 * @package Resources\Views\Emails\Owner
 *
 * Variables:
 * @var \App\Models\User $owner - The owner user instance
 */
--}}
@extends('emails.layouts.base')

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
@endphp

@section('header-subtitle')
    {{ __('emails.owner.verified.subtitle') }}
@endsection

@section('content')
    {{-- Success Icon --}}
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(34, 197, 94, 0.3);">
            <span style="font-size: 50px;">🎉</span>
        </div>
    </div>

    {{-- Title --}}
    <h2 style="text-align: center; color: #16a34a;">{{ __('emails.owner.verified.title') }}</h2>
    
    <p style="text-align: center; font-size: 18px;">
        {{ __('emails.owner.greeting', ['name' => $owner->name]) }}
    </p>
    
    <p style="text-align: center; color: #6b7280;">
        {{ __('emails.owner.verified.intro') }}
    </p>

    {{-- Verified Badge --}}
    <div style="text-align: center; margin: 30px 0;">
        <div style="display: inline-block; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 16px 32px; border-radius: 50px; border: 2px solid #86efac;">
            <span style="color: #16a34a; font-weight: 700; font-size: 16px;">
                ✓ {{ __('emails.owner.verified.badge') }}
            </span>
        </div>
    </div>

    {{-- What You Can Do Now --}}
    <div class="info-box" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #93c5fd;">
        <div class="info-box-header" style="color: #1e40af; border-bottom-color: #93c5fd;">
            {{ __('emails.owner.verified.what_you_can_do') }}
        </div>
        
        <div style="display: flex; align-items: flex-start; margin-bottom: 16px;">
            <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-{{ $isRtl ? 'left' : 'right' }}: 12px; flex-shrink: 0;">
                <span style="color: white; font-weight: 700;">1</span>
            </div>
            <div>
                <p style="margin: 0; font-weight: 600; color: #1f2937;">{{ __('emails.owner.verified.step_1_title') }}</p>
                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">{{ __('emails.owner.verified.step_1_desc') }}</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: flex-start; margin-bottom: 16px;">
            <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-{{ $isRtl ? 'left' : 'right' }}: 12px; flex-shrink: 0;">
                <span style="color: white; font-weight: 700;">2</span>
            </div>
            <div>
                <p style="margin: 0; font-weight: 600; color: #1f2937;">{{ __('emails.owner.verified.step_2_title') }}</p>
                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">{{ __('emails.owner.verified.step_2_desc') }}</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: flex-start; margin-bottom: 16px;">
            <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-{{ $isRtl ? 'left' : 'right' }}: 12px; flex-shrink: 0;">
                <span style="color: white; font-weight: 700;">3</span>
            </div>
            <div>
                <p style="margin: 0; font-weight: 600; color: #1f2937;">{{ __('emails.owner.verified.step_3_title') }}</p>
                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">{{ __('emails.owner.verified.step_3_desc') }}</p>
            </div>
        </div>
        
        <div style="display: flex; align-items: flex-start;">
            <div style="width: 32px; height: 32px; background: #4f46e5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-{{ $isRtl ? 'left' : 'right' }}: 12px; flex-shrink: 0;">
                <span style="color: white; font-weight: 700;">4</span>
            </div>
            <div>
                <p style="margin: 0; font-weight: 600; color: #1f2937;">{{ __('emails.owner.verified.step_4_title') }}</p>
                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">{{ __('emails.owner.verified.step_4_desc') }}</p>
            </div>
        </div>
    </div>

    {{-- Tips for Success --}}
    <div class="highlight-box" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-{{ $isRtl ? 'right' : 'left' }}-color: #f59e0b;">
        <p style="margin: 0 0 12px 0; font-weight: 600; color: #92400e;">
            💡 {{ __('emails.owner.verified.tips_title') }}
        </p>
        <ul style="margin: 0; padding-{{ $isRtl ? 'right' : 'left' }}: 20px; color: #78350f; font-size: 14px;">
            <li style="margin-bottom: 8px;">{{ __('emails.owner.verified.tip_1') }}</li>
            <li style="margin-bottom: 8px;">{{ __('emails.owner.verified.tip_2') }}</li>
            <li>{{ __('emails.owner.verified.tip_3') }}</li>
        </ul>
    </div>

    {{-- Support --}}
    <div class="highlight-box info">
        <p style="margin: 0 0 8px 0; font-weight: 600; color: #1e40af;">
            {{ __('emails.owner.verified.need_help') }}
        </p>
        <p style="margin: 0; color: #3b82f6; font-size: 14px;">
            {{ __('emails.owner.verified.support_desc') }}
            <a href="mailto:{{ config('mail.support_email', 'support@majalis.om') }}" style="color: #4f46e5;">
                {{ config('mail.support_email', 'support@majalis.om') }}
            </a>
        </p>
    </div>

    {{-- CTA Button --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('filament.owner.pages.dashboard') }}" class="btn btn-success">
            {{ __('emails.owner.verified.go_to_dashboard') }}
        </a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        {{ __('emails.owner.verified.welcome_message') }}
    </p>

    <p style="text-align: center; margin-top: 20px;">
        {{ __('emails.booking.regards') }}<br>
        <strong>{{ __('emails.booking.team', ['app' => config('app.name', 'Majalis')]) }}</strong>
    </p>
@endsection
