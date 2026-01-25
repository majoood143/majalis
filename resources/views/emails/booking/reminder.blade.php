{{--
    Booking Reminder Email - HTML Template
    Location: resources/views/emails/booking/reminder.blade.php

    This template renders the booking reminder email as proper HTML.
    Uses standard HTML email structure for maximum compatibility.

    Variables available:
    - $booking: Booking model instance
    - $hallName: Hall name (localized)
    - $hallAddress: Hall address/city
    - $customerName: Customer's name
    - $bookingDate: Formatted booking date
    - $timeSlot: Localized time slot
    - $daysUntil: Number of days until booking
    - $customMessage: Optional custom message from admin
    - $hasBalanceDue: Boolean indicating if payment is pending
    - $balanceDue: Outstanding balance amount
    - $bookingNumber: Booking reference number
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('Booking Reminder') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style type="text/css">
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f4f4f7;
        }
        /* RTL Support */
        .rtl {
            direction: rtl;
            text-align: right;
        }
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .fluid {
                max-width: 100% !important;
                height: auto !important;
            }
            .stack-column {
                display: block !important;
                width: 100% !important;
            }
            .padding-mobile {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <!-- Preheader Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        @if($daysUntil > 0)
            {{ __('Your booking at :hall is in :days days', ['hall' => $hallName, 'days' => $daysUntil]) }}
        @else
            {{ __('Your booking at :hall is today!', ['hall' => $hallName]) }}
        @endif
    </div>

    <!-- Email Wrapper -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f7;">
        <tr>
            <td style="padding: 40px 10px;">
                <!-- Email Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container" style="margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">

                    <!-- Logo Header -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center; background-color: #3b82f6; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                {{ config('app.name', 'Majalis') }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Reminder Badge -->
                    <tr>
                        <td style="padding: 30px 40px 20px 40px; text-align: center;">
                            <div style="display: inline-block; padding: 10px 24px; background-color: #fef3c7; color: #92400e; border-radius: 30px; font-size: 14px; font-weight: 600;">
                                @if($daysUntil === 0)
                                    🔔 {{ __('TODAY') }}
                                @elseif($daysUntil === 1)
                                    🔔 {{ __('TOMORROW') }}
                                @else
                                    🔔 {{ __(':days DAYS AWAY', ['days' => $daysUntil]) }}
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 0 40px;" class="padding-mobile">
                            <!-- Title -->
                            <h2 style="margin: 0 0 10px 0; color: #1f2937; font-size: 24px; font-weight: bold; text-align: center;">
                                {{ __('Booking Reminder') }}
                            </h2>

                            <!-- Greeting -->
                            <p style="margin: 20px 0; color: #374151; font-size: 16px; line-height: 1.6;">
                                {{ __('Hello') }} <strong>{{ $customerName }}</strong>,
                            </p>

                            <!-- Main Message -->
                            <p style="margin: 20px 0; color: #374151; font-size: 16px; line-height: 1.6;">
                                {{ __('This is a friendly reminder about your upcoming booking at') }}
                                <strong style="color: #3b82f6;">{{ $hallName }}</strong>.
                                {{ __('Please find the booking details below.') }}
                            </p>
                        </td>
                    </tr>

                    <!-- Booking Details Card -->
                    <tr>
                        <td style="padding: 20px 40px;" class="padding-mobile">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f9fafb; border-radius: 8px; border-left: 4px solid #3b82f6;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 20px 0; color: #1f2937; font-size: 18px; font-weight: bold;">
                                            {{ __('Booking Details') }}
                                        </h3>

                                        <!-- Details Table -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <!-- Booking Number -->
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: 600; width: 40%;">
                                                    {{ __('Reference Number:') }}
                                                </td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 600; font-family: monospace;">
                                                    #{{ $bookingNumber ?? $booking->booking_number ?? 'N/A' }}
                                                </td>
                                            </tr>

                                            <!-- Hall Name -->
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: 600;">
                                                    {{ __('Hall Name:') }}
                                                </td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 500;">
                                                    {{ $hallName }}
                                                </td>
                                            </tr>

                                            <!-- Location -->
                                            @if($hallAddress)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: 600;">
                                                    {{ __('Location:') }}
                                                </td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 500;">
                                                    {{ $hallAddress }}
                                                </td>
                                            </tr>
                                            @endif

                                            <!-- Booking Date -->
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: 600;">
                                                    {{ __('Booking Date:') }}
                                                </td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 600;">
                                                    📅 {{ $bookingDate }}
                                                </td>
                                            </tr>

                                            <!-- Time Slot -->
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; font-weight: 600;">
                                                    {{ __('Time:') }}
                                                </td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 500;">
                                                    🕐 {{ $timeSlot }}
                                                </td>
                                            </tr>

                                            <!-- Status -->
                                            <tr>
                                                <td style="padding: 12px 0; {{ $hasBalanceDue ? 'border-bottom: 1px solid #e5e7eb;' : '' }} color: #6b7280; font-size: 14px; font-weight: 600;">
                                                    {{ __('Status:') }}
                                                </td>
                                                <td style="padding: 12px 0; {{ $hasBalanceDue ? 'border-bottom: 1px solid #e5e7eb;' : '' }} color: #1f2937; font-size: 14px;">
                                                    <span style="display: inline-block; padding: 4px 12px; background-color: #dcfce7; color: #166534; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                                        ✓ {{ __('Confirmed') }}
                                                    </span>
                                                </td>
                                            </tr>

                                            <!-- Balance Due (if applicable) -->
                                            @if($hasBalanceDue)
                                            <tr>
                                                <td style="padding: 12px 0; color: #dc2626; font-size: 14px; font-weight: 600;">
                                                    {{ __('Balance Due:') }}
                                                </td>
                                                <td style="padding: 12px 0; color: #dc2626; font-size: 16px; font-weight: bold;">
                                                    {{ number_format((float)$balanceDue, 3) }} {{ __('OMR') }}
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Custom Message (if provided) -->
                    @if($customMessage)
                    <tr>
                        <td style="padding: 0 40px 20px 40px;" class="padding-mobile">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <p style="margin: 0; color: #92400e; font-size: 14px;">
                                            <strong>{{ __('Special Notice:') }}</strong><br>
                                            {{ $customMessage }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- Important Information -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;" class="padding-mobile">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <h4 style="margin: 0 0 12px 0; color: #15803d; font-size: 14px; font-weight: bold;">
                                            ℹ️ {{ __('Important Information') }}
                                        </h4>
                                        <ul style="margin: 0; padding-left: 20px; color: #166534; font-size: 13px; line-height: 1.8;">
                                            <li>{{ __('Please arrive 15 minutes before your scheduled time') }}</li>
                                            <li>{{ __('Bring your booking reference number with you') }}</li>
                                            <li>{{ __('If you need to reschedule, please contact us at least 24 hours in advance') }}</li>
                                            @if($hasBalanceDue)
                                            <li style="color: #dc2626; font-weight: 600;">{{ __('Please settle the remaining balance before or at the venue') }}</li>
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Contact Section -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;" class="padding-mobile">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <h4 style="margin: 0 0 12px 0; color: #1e40af; font-size: 14px; font-weight: bold;">
                                            📞 {{ __('Need Help?') }}
                                        </h4>
                                        <p style="margin: 0; color: #1e3a8a; font-size: 13px; line-height: 1.6;">
                                            {{ __('If you have any questions or need to make changes to your booking, please contact our support team.') }}<br><br>
                                            <strong>{{ __('Email:') }}</strong> <a href="mailto:support@majalis.om" style="color: #2563eb;">support@majalis.om</a><br>
                                            <strong>{{ __('Phone:') }}</strong> <a href="tel:+96812345678" style="color: #2563eb;">+968 1234 5678</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f9fafb; border-radius: 0 0 8px 8px; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: #374151; font-size: 14px;">
                                {{ __('Thank you for choosing') }} <strong>{{ config('app.name', 'Majalis') }}</strong>!<br>
                                {{ __('We look forward to hosting your event.') }}
                            </p>
                            <p style="margin: 20px 0 0 0; color: #9ca3af; font-size: 12px;">
                                {{ __('If you did not make this booking or have any concerns, please reply to this email immediately.') }}
                            </p>
                            <p style="margin: 15px 0 0 0; color: #9ca3af; font-size: 11px;">
                                © {{ date('Y') }} {{ config('app.name', 'Majalis') }}. {{ __('All rights reserved.') }}
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
