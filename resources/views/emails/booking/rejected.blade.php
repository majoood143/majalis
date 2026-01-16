{{--
    Email Template: Booking Rejected
    Sent to customer when hall owner rejects their booking.
    Includes rejection reason and alternative options.
    Supports EN/AR with RTL layout.
--}}

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $strings['rejected_message'] }}</title>
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
        
        /* Alert Banner */
        .alert-banner {
            background-color: #fef2f2;
            border-bottom: 3px solid #ef4444;
            padding: 25px;
            text-align: center;
        }
        .alert-banner .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .alert-banner h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #991b1b;
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
        
        /* Rejection Reason Box */
        .rejection-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .rejection-box h3 {
            color: #991b1b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 10px 0;
        }
        .rejection-box p {
            color: #7f1d1d;
            font-size: 15px;
            margin: 0;
            font-style: italic;
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
        .booking-number {
            background-color: #64748b;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 14px;
        }
        
        /* What's Next Section */
        .whats-next {
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .whats-next h3 {
            color: #0369a1;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .whats-next p {
            color: #0c4a6e;
            font-size: 14px;
            margin: 0;
        }
        .whats-next ul {
            margin: 15px 0 0 0;
            padding: {{ $isRtl ? '0 20px 0 0' : '0 0 0 20px' }};
            color: #0c4a6e;
            font-size: 14px;
        }
        .whats-next li {
            margin-bottom: 8px;
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
        }
        .cta-button-secondary {
            display: inline-block;
            background-color: #ffffff;
            color: #1e40af !important;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: 2px solid #1e40af;
            margin-top: 15px;
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
                    
                    {{-- Alert Banner --}}
                    <tr>
                        <td class="alert-banner">
                            <div class="icon">😔</div>
                            <h2>{{ $strings['rejected_message'] }}</h2>
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
                                    نأسف لإبلاغك بأن حجزك في <strong>{{ $hallName }}</strong> بتاريخ <strong>{{ $bookingDateShort }}</strong> قد تم رفضه من قبل مالك القاعة.
                                @else
                                    We're sorry to inform you that your booking at <strong>{{ $hallName }}</strong> on <strong>{{ $bookingDateShort }}</strong> has been declined by the hall owner.
                                @endif
                            </p>
                            
                            {{-- Rejection Reason --}}
                            @if($rejectionReason)
                            <div class="rejection-box">
                                <h3>{{ $strings['rejection_reason'] }}</h3>
                                <p>"{{ $rejectionReason }}"</p>
                            </div>
                            @endif
                            
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
                                        <td style="padding: 12px 0;">
                                            <span style="color: #64748b; font-size: 14px;">{{ $strings['time'] }}</span>
                                        </td>
                                        <td style="padding: 12px 0; text-align: {{ $isRtl ? 'left' : 'right' }};">
                                            <span style="color: #1f2937; font-weight: 600; font-size: 14px;">{{ $timeSlot }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            {{-- What's Next --}}
                            <div class="whats-next">
                                <h3>{{ $strings['what_next'] }}</h3>
                                <p>{{ $strings['rejected_next_steps'] }}</p>
                                <ul>
                                    @if($isRtl)
                                        <li>تصفح قاعات أخرى متاحة في نفس المنطقة</li>
                                        <li>جرب تواريخ أو أوقات بديلة</li>
                                        <li>تواصل مع فريق الدعم لمساعدتك في إيجاد قاعة مناسبة</li>
                                    @else
                                        <li>Browse other available halls in the same area</li>
                                        <li>Try alternative dates or time slots</li>
                                        <li>Contact our support team for assistance in finding a suitable hall</li>
                                    @endif
                                </ul>
                            </div>
                            
                            {{-- CTA Buttons --}}
                            <div class="cta-container">
                                <a href="{{ $appUrl }}/halls" class="cta-button">
                                    {{ $isRtl ? 'تصفح القاعات' : 'Browse Halls' }}
                                </a>
                                <br>
                                <a href="{{ $supportUrl }}" class="cta-button-secondary">
                                    {{ $strings['contact_us'] }}
                                </a>
                            </div>
                            
                            {{-- Apology --}}
                            <p style="color: #6b7280; font-size: 14px; text-align: center; margin-top: 30px;">
                                @if($isRtl)
                                    نعتذر عن أي إزعاج قد سببه هذا الأمر. نحن هنا لمساعدتك في إيجاد القاعة المثالية لفعاليتك.
                                @else
                                    We apologize for any inconvenience this may have caused. We're here to help you find the perfect venue for your event.
                                @endif
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
