{{--
    Email Template: Booking Approved
    Sent to customer when hall owner approves their booking.
    Supports EN/AR with RTL layout.
--}}

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $strings['approved_message'] }}</title>
    <style>
        /* Reset */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        
        /* Base Styles */
        body {
            font-family: {{ $isRtl ? "'Segoe UI', Tahoma, Arial, sans-serif" : "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" }};
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }
        
        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            padding: 30px 40px;
            text-align: center;
        }
        .email-header img {
            max-height: 50px;
            width: auto;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 15px 0 0 0;
            font-weight: 600;
        }
        
        /* Success Banner */
        .success-banner {
            background-color: #10b981;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .success-banner .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .success-banner h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        
        /* Content */
        .email-content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message-text {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 30px;
        }
        
        /* Booking Details Card */
        .booking-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .booking-card-header {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .booking-detail {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .booking-detail:last-child {
            border-bottom: none;
        }
        .booking-detail-label {
            color: #64748b;
            font-size: 14px;
        }
        .booking-detail-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
            text-align: {{ $isRtl ? 'left' : 'right' }};
        }
        .booking-number {
            background-color: #1e40af;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 14px;
        }
        
        /* Total Amount */
        .total-row {
            background-color: #1e40af;
            color: #ffffff;
            margin: 20px -25px -25px -25px;
            padding: 20px 25px;
            border-radius: 0 0 12px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total-label {
            font-size: 16px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: 700;
        }
        
        /* What's Next Section */
        .whats-next {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .whats-next h3 {
            color: #92400e;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .whats-next p {
            color: #78350f;
            font-size: 14px;
            margin: 0;
        }
        
        /* CTA Button */
        .cta-container {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        }
        
        /* Footer */
        .email-footer {
            background-color: #1f2937;
            color: #9ca3af;
            padding: 30px 40px;
            text-align: center;
        }
        .footer-text {
            font-size: 14px;
            margin-bottom: 15px;
        }
        .footer-links a {
            color: #60a5fa;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }
        .footer-copyright {
            font-size: 12px;
            margin-top: 20px;
            color: #6b7280;
        }
        
        /* RTL Specific */
        @if($isRtl)
        .booking-detail {
            flex-direction: row-reverse;
        }
        .total-row {
            flex-direction: row-reverse;
        }
        @endif
        
        /* Mobile Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-content {
                padding: 20px !important;
            }
            .booking-card {
                padding: 15px !important;
            }
            .total-row {
                margin: 15px -15px -15px -15px !important;
                padding: 15px !important;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
        <tr>
            <td style="padding: 20px 0;">
                <table role="presentation" class="email-container" cellspacing="0" cellpadding="0" border="0" width="600" align="center">
                    
                    {{-- Header --}}
                    <tr>
                        <td class="email-header">
                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="max-height: 50px;">
                            <h1>{{ $appName }}</h1>
                        </td>
                    </tr>
                    
                    {{-- Success Banner --}}
                    <tr>
                        <td class="success-banner">
                            <div class="icon">✓</div>
                            <h2>{{ $strings['approved_message'] }}</h2>
                        </td>
                    </tr>
                    
                    {{-- Content --}}
                    <tr>
                        <td class="email-content">
                            {{-- Greeting --}}
                            <p class="greeting">
                                {{ $strings['greeting'] }} <strong>{{ $customerName }}</strong>,
                            </p>
                            
                            <p class="message-text">
                                @if($isRtl)
                                    يسعدنا إبلاغك بأن حجزك في <strong>{{ $hallName }}</strong> قد تمت الموافقة عليه من قبل مالك القاعة.
                                @else
                                    We're pleased to inform you that your booking at <strong>{{ $hallName }}</strong> has been approved by the hall owner.
                                @endif
                            </p>
                            
                            {{-- Booking Details Card --}}
                            <div class="booking-card">
                                <div class="booking-card-header">
                                    {{ $strings['booking_details'] }}
                                </div>
                                
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <span style="color: #64748b; font-size: 14px;">{{ $strings['booking_number'] }}</span>
                                        </td>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; text-align: {{ $isRtl ? 'left' : 'right' }};">
                                            <span class="booking-number">{{ $bookingNumber }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <span style="color: #64748b; font-size: 14px;">{{ $strings['hall'] }}</span>
                                        </td>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; text-align: {{ $isRtl ? 'left' : 'right' }};">
                                            <span style="color: #1f2937; font-weight: 600; font-size: 14px;">{{ $hallName }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <span style="color: #64748b; font-size: 14px;">{{ $strings['date'] }}</span>
                                        </td>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; text-align: {{ $isRtl ? 'left' : 'right' }};">
                                            <span style="color: #1f2937; font-weight: 600; font-size: 14px;">{{ $bookingDate }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                                            <span style="color: #64748b; font-size: 14px;">{{ $strings['time'] }}</span>
                                        </td>
                                        <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; text-align: {{ $isRtl ? 'left' : 'right' }};">
                                            <span style="color: #1f2937; font-weight: 600; font-size: 14px;">{{ $timeSlot }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0;">
                                            <span style="color: #64748b; font-size: 14px;">{{ $strings['guests'] }}</span>
                                        </td>
                                        <td style="padding: 12px 0; text-align: {{ $isRtl ? 'left' : 'right' }};">
                                            <span style="color: #1f2937; font-weight: 600; font-size: 14px;">{{ $numberOfGuests }}</span>
                                        </td>
                                    </tr>
                                </table>
                                
                                {{-- Total Amount --}}
                                <table width="100%" cellspacing="0" cellpadding="0" style="background-color: #1e40af; margin: 20px -25px -25px -25px; width: calc(100% + 50px); border-radius: 0 0 12px 12px;">
                                    <tr>
                                        <td style="padding: 20px 25px; color: #ffffff; font-size: 16px;">
                                            {{ $strings['total'] }}
                                        </td>
                                        <td style="padding: 20px 25px; color: #ffffff; font-size: 24px; font-weight: 700; text-align: {{ $isRtl ? 'left' : 'right' }};">
                                            {{ $totalAmount }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            {{-- What's Next --}}
                            <div class="whats-next">
                                <h3>{{ $strings['what_next'] }}</h3>
                                <p>{{ $strings['approved_next_steps'] }}</p>
                            </div>
                            
                            {{-- CTA Button --}}
                            <div class="cta-container">
                                <a href="{{ $viewBookingUrl }}" class="cta-button">
                                    {{ $strings['view_booking'] }}
                                </a>
                            </div>
                            
                            {{-- Support Text --}}
                            <p style="color: #6b7280; font-size: 14px; text-align: center;">
                                {{ $strings['support_text'] }}:<br>
                                <a href="mailto:{{ $supportEmail }}" style="color: #3b82f6;">{{ $supportEmail }}</a>
                            </p>
                        </td>
                    </tr>
                    
                    {{-- Footer --}}
                    <tr>
                        <td class="email-footer">
                            <p class="footer-text">{{ $strings['thank_you'] }}</p>
                            <div class="footer-links">
                                <a href="{{ $appUrl }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
                                <a href="{{ $supportUrl }}">{{ $strings['contact_us'] }}</a>
                            </div>
                            <p class="footer-copyright">
                                © {{ date('Y') }} {{ $appName }}. {{ $isRtl ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
