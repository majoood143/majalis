{{--
/**
 * Base Email Layout Template
 *
 * Master template for all Majalis email notifications.
 * Features:
 * - Bilingual support (English/Arabic)
 * - RTL layout for Arabic
 * - Responsive design
 * - Consistent branding
 *
 * @package Resources\Views\Emails\Layouts
 *
 * Variables:
 * @var string $subject - Email subject line
 * @var string $preheader - Preview text for email clients (optional)
 * @var string $locale - Current locale (en/ar)
 */
--}}
@php
    $locale = $locale ?? app()->getLocale();
    $isRtl = $locale === 'ar';
    $direction = $isRtl ? 'rtl' : 'ltr';
    $textAlign = $isRtl ? 'right' : 'left';
    $fontFamily = $isRtl ? "'Segoe UI', Tahoma, Arial, sans-serif" : "'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $subject ?? config('app.name') }}</title>

    {{-- Preheader text (preview in email clients) --}}
    @isset($preheader)
    <!--[if !mso]><!-->
    <div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
        {{ $preheader }}
    </div>
    <!--<![endif]-->
    @endisset

    <style type="text/css">
        /* Reset styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

        /* Base styles */
        body {
            font-family: {{ $fontFamily }};
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f7;
            direction: {{ $direction }};
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 30px 40px;
            text-align: center;
        }

        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .email-header .logo {
            max-width: 180px;
            margin-bottom: 15px;
        }

        /* Content */
        .email-content {
            padding: 40px;
            text-align: {{ $textAlign }};
        }

        .email-content h2 {
            color: #1f2937;
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 20px 0;
        }

        .email-content p {
            color: #4b5563;
            font-size: 16px;
            line-height: 1.7;
            margin: 0 0 16px 0;
        }

        /* Info Box */
        .info-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
            border: 1px solid #e2e8f0;
        }

        .info-box-header {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6b7280;
            font-size: 14px;
        }

        .info-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
        }

        /* Highlight Box */
        .highlight-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            border-{{ $isRtl ? 'right' : 'left' }}: 4px solid #10b981;
        }

        .highlight-box.warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-{{ $isRtl ? 'right' : 'left' }}-color: #f59e0b;
        }

        .highlight-box.danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-{{ $isRtl ? 'right' : 'left' }}-color: #ef4444;
        }

        .highlight-box.info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-{{ $isRtl ? 'right' : 'left' }}-color: #3b82f6;
        }

        /* Button */
        .btn {
            display: inline-block;
            padding: 14px 32px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            margin: 16px 0;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff !important;
        }

        .btn-outline {
            background: transparent;
            color: #4f46e5 !important;
            border: 2px solid #4f46e5;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-confirmed { background-color: #d1fae5; color: #065f46; }
        .status-completed { background-color: #dbeafe; color: #1e40af; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-refunded { background-color: #e0e7ff; color: #3730a3; }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 30px 0;
        }

        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 8px 0;
        }

        .email-footer a {
            color: #4f46e5;
            text-decoration: none;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
        }

        .social-links img {
            width: 32px;
            height: 32px;
        }

        /* Amount styles */
        .amount {
            font-family: 'SF Mono', Monaco, 'Courier New', monospace;
            font-weight: 700;
        }

        .amount-large {
            font-size: 32px;
            color: #059669;
        }

        /* Responsive */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-header, .email-content, .email-footer {
                padding: 20px !important;
            }
            .email-header h1 {
                font-size: 22px !important;
            }
            .info-row {
                flex-direction: column;
                text-align: {{ $textAlign }};
            }
            .info-label, .info-value {
                display: block;
                width: 100%;
            }
            .info-value {
                margin-top: 4px;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f7;">
        <tr>
            <td style="padding: 20px 0;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container" style="margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    {{-- Header --}}
                    <tr>
                        <td class="email-header">
                            @if(file_exists(public_path('images/logo-white.png')))
                                <img src="{{ asset('images/logo-white.png') }}" alt="{{ config('app.name') }}" class="logo">
                            @else
                                <h1>{{ config('app.name', 'Majalis') }}</h1>
                            @endif
                            @hasSection('header-subtitle')
                                <p style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 10px 0 0 0;">
                                    @yield('header-subtitle')
                                </p>
                            @endif
                        </td>
                    </tr>

                    {{-- Main Content --}}
                    <tr>
                        <td class="email-content">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td class="email-footer">
                            {{-- Social Links --}}
                            @if(config('services.social.instagram') || config('services.social.twitter') || config('services.social.facebook'))
                            <div class="social-links">
                                @if(config('services.social.instagram'))
                                <a href="{{ config('services.social.instagram') }}">
                                    <img src="{{ asset('images/icons/instagram.png') }}" alt="Instagram">
                                </a>
                                @endif
                                @if(config('services.social.twitter'))
                                <a href="{{ config('services.social.twitter') }}">
                                    <img src="{{ asset('images/icons/twitter.png') }}" alt="Twitter">
                                </a>
                                @endif
                                @if(config('services.social.facebook'))
                                <a href="{{ config('services.social.facebook') }}">
                                    <img src="{{ asset('images/icons/facebook.png') }}" alt="Facebook">
                                </a>
                                @endif
                            </div>
                            @endif

                            <p><strong>{{ config('app.name', 'Majalis') }}</strong></p>
                            <p>{{ __('emails.footer.tagline') }}</p>
                            <p>{{ __('emails.footer.location') }}</p>

                            <div class="divider"></div>

                            <p style="font-size: 11px;">
                                {{ __('emails.footer.auto_generated') }}
                            </p>

                            @hasSection('unsubscribe')
                            <p style="font-size: 11px;">
                                @yield('unsubscribe')
                            </p>
                            @endif

                            <p style="font-size: 11px; margin-top: 15px;">
                                &copy; {{ date('Y') }} {{ config('app.name', 'Majalis') }}. {{ __('emails.footer.rights') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
