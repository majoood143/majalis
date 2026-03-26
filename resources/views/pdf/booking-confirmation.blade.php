{{--
|--------------------------------------------------------------------------
| Enhanced Booking Confirmation PDF Template - Booking.com Style
|--------------------------------------------------------------------------
|
| This template generates a professional, single-page booking confirmation
| inspired by Booking.com's clean and information-rich PDF design.
|
| Features:
| - Modern header with booking reference and confirmation badge
| - Prominent property details with contact info
| - Two-column layout for efficient space usage
| - Detailed price breakdown with advance payment info
| - Static map using OpenStreetMap tiles
| - Important information and policies section
| - Compact footer with contact details and timestamp
|
| @package    Majalis
| @version    3.0.0
| @author     Majalis Development Team
| @requires   DomPDF, Laravel 12, PHP 8.4.12
|
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {{-- <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp"> --}}
    <title>{{ __('halls.booking_confirmation') }} - {{ $booking->booking_number }}</title>

    <style>
        /* Page setup is handled via mPDF constructor config (format + margins).
           Do NOT use @page CSS — it conflicts with mPDF's format config
           and causes phantom pages in the output. */

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'tajawal', 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #2c3e50;
            line-height: 1.45;
            background: #ffffff;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        /* ==========================================================================
           Typography & Colors
           ========================================================================== */
        h1, h2, h3 {
            font-weight: 500;
            margin: 0 0 6px 0;
        }

        .text-primary { color: #0066b3; }      /* Booking.com blue */
        .text-success { color: #008b5d; }       /* Green for confirmed */
        .text-muted { color: #6b7a8d; }
        .text-small { font-size: 7.5pt; }
        .text-large { font-size: 12pt; font-weight: 500; }

        .bg-light { background-color: #f8fafc; }
        .border-bottom { border-bottom: 1px solid #e4e7eb; }
        .border-top { border-top: 1px solid #e4e7eb; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mt-3 { margin-top: 12px; }
        .p-2 { padding: 8px; }
        .p-3 { padding: 12px; }
        .rounded { border-radius: 6px; }

        /* ==========================================================================
           Header - Booking.com Style
           ========================================================================== */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #0066b3;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .logo {
            font-size: 24pt;
            font-weight: 500;
            color: #0066b3;
        }

        .logo span {
            font-weight: 300;
            color: #4a5a6e;
        }

        .confirmation-badge {
            background: #008b5d;
            color: white;
            padding: 6px 14px;
            font-size: 9pt;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 5px;
        }

        .booking-ref {
            font-size: 14pt;
            font-weight: 500;
            color: #1a2b3c;
        }

        .booking-ref-label {
            font-size: 7.5pt;
            color: #6b7a8d;
        }

        /* ==========================================================================
           Property Card
           ========================================================================== */
        .property-card {
            background: #f1f9ff;
            border-left: 4px solid #0066b3;
            padding: 12px 14px;
            margin-bottom: 16px;
            border-radius: 0 8px 8px 0;
        }

        .property-name {
            font-size: 16pt;
            font-weight: 500;
            color: #1a2b3c;
            margin-bottom: 4px;
        }

        .property-address {
            font-size: 8.5pt;
            color: #4a5a6e;
            margin-bottom: 6px;
            line-height: 1.5;
        }

        .property-contact {
            font-size: 8pt;
            color: #2c3e50;
        }

        .property-contact i {  /* dummy for icon alignment */
            display: inline-block;
            width: 16px;
            text-align: center;
        }

        /* ==========================================================================
           Two Column Layout
           ========================================================================== */
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }

        .col-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 10px;
        }

        .col-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 10px;
        }

        /* ==========================================================================
           Info Grid (Booking.com style date/guest boxes)
           ========================================================================== */
        .info-grid {
            display: table;
            width: 100%;
            background: white;
            border: 1px solid #e4e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 12px 8px;
            border-bottom: 1px solid #e4e7eb;
            vertical-align: middle;
        }

        .info-cell:last-child {
            border-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: none;
        }

        .info-label {
            font-size: 7pt;
            color: #6b7a8d;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 11pt;
            font-weight: 500;
            color: #1a2b3c;
        }

        .info-sub {
            font-size: 7.5pt;
            color: #6b7a8d;
        }

        /* ==========================================================================
           Section Titles
           ========================================================================== */
        .section-title {
            font-size: 11pt;
            font-weight: 500;
            color: #1a2b3c;
            margin-bottom: 8px;
        }

        .section-title i {  /* icon placeholder */
            color: #0066b3;
            margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 6px;
        }

        /* ==========================================================================
           Customer & Booking Details
           ========================================================================== */
        .detail-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .detail-label {
            display: table-cell;
            width: 30%;
            color: #6b7a8d;
            font-size: 8pt;
        }

        .detail-value {
            display: table-cell;
            width: 70%;
            font-weight: 500;
            color: #1a2b3c;
            font-size: 9pt;
        }

        /* ==========================================================================
           Price Table (Booking.com style)
           ========================================================================== */
        .price-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }

        .price-table td {
            padding: 6px 0;
            border-bottom: 1px dashed #e4e7eb;
        }

        .price-table td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .price-table .total-row {
            font-weight: 700;
            font-size: 10pt;
            color: #1a2b3c;
            border-top: 2px solid #1a2b3c;
            border-bottom: none;
        }

        .price-table .total-row td {
            padding-top: 10px;
            border-bottom: none;
        }

        .advance-box {
            background: #f0f7f0;
            border: 1px solid #c3e0c3;
            border-radius: 6px;
            padding: 8px 10px;
            margin-top: 8px;
        }

        .advance-row {
            display: table;
            width: 100%;
            font-size: 8.5pt;
        }

        .advance-label {
            display: table-cell;
            color: #2c5f2d;
        }

        .advance-value {
            display: table-cell;
            text-align: right;
            font-weight: 500;
            color: #1e7e34;
        }

        /* ==========================================================================
           Map & Location
           ========================================================================== */
        .map-container {
            border: 1px solid #e4e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .map-image {
            width: 100%;
            height: 130px;
            display: block;
            background: #eef2f6;
        }

        .map-coords {
            font-size: 7pt;
            color: #6b7a8d;
            text-align: center;
        }

        .map-link {
            font-size: 7pt;
            text-align: center;
            color: #0066b3;
            text-decoration: none;
        }

        /* ==========================================================================
           Important Notes Box
           ========================================================================== */
        .notes-box {
            background: #fef9e7;
            border: 1px solid #f5c542;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .notes-title {
            font-size: 9pt;
            font-weight: 600;
            color: #9e6b00;
            margin-bottom: 6px;
        }

        .notes-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .notes-list li {
            font-size: 7.5pt;
            color: #664d00;
            padding: 3px 0;
        }

        /* ==========================================================================
           Footer
           ========================================================================== */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #d0d7de;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
            font-size: 7pt;
            color: #6b7a8d;
        }

        .footer-right {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            font-size: 7pt;
            color: #8a9cb0;
        }

        .footer-contact strong {
            color: #1a2b3c;
        }

        .footer-logo {
            font-size: 12pt;
            font-weight: 500;
            color: #0066b3;
        }

        /* ==========================================================================
           Payment Status Badge
           ========================================================================== */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 7.5pt;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-paid {
            background: #cce5ff;
            color: #004085;
        }

        .status-partial {
            background: #e2d5f1;
            color: #563d7c;
        }

        /* ==========================================================================
           RTL Adjustments
           ========================================================================== */
        [dir="rtl"] .property-card {
            border-left: none;
            border-right: 4px solid #0066b3;
            border-radius: 8px 0 0 8px;
        }

        [dir="rtl"] .notes-list li {
            padding: 3px 16px 3px 0;
        }

        [dir="rtl"] .notes-list li:before {
            left: auto;
            right: 4px;
        }

        [dir="rtl"] .price-table td:last-child {
            text-align: left;
        }

        [dir="rtl"] .advance-value {
            text-align: left;
        }
    </style>
</head>

<body>

    @php
        $locale = app()->getLocale();
        $hallName = is_array($booking->hall->name)
            ? ($booking->hall->name[$locale] ?? $booking->hall->name['en'] ?? 'N/A')
            : (string) $booking->hall->name;

        $cityName = is_array($booking->hall->city->name)
            ? ($booking->hall->city->name[$locale] ?? $booking->hall->city->name['en'] ?? '')
            : (string) $booking->hall->city->name;

        $regionName = is_array($booking->hall->city->region->name)
            ? ($booking->hall->city->region->name[$locale] ?? $booking->hall->city->region->name['en'] ?? '')
            : (string) $booking->hall->city->region->name;

        $bookingDate = $booking->booking_date instanceof \Carbon\Carbon
            ? $booking->booking_date
            : \Carbon\Carbon::parse($booking->booking_date);

        $pdfSupportEmail = \App\Models\Setting::get('contact', 'support_email') ?? \App\Models\Setting::get('contact', 'email');
        $pdfSupportPhone = \App\Models\Setting::get('contact', 'phone');
    @endphp

    {{-- ========================================================================
        Header
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 2px solid #0066b3; margin-bottom: 14px;">
        <tr>
            <td width="50%" style="vertical-align: middle; padding-bottom: 8px;">
                <div class="logo">Majalis<span>.om</span></div>
            </td>
            <td width="50%" style="vertical-align: middle; padding-bottom: 8px; text-align: {{ $locale === 'ar' ? 'left' : 'right' }};">
                <div class="confirmation-badge">{{ __('halls.booking_confirmed') }}</div><br>
                <div class="booking-ref-label">{{ __('halls.booking_reference') }}</div>
                <div class="booking-ref">{{ $booking->booking_number }}</div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Property Card
        ======================================================================== --}}
    <div class="property-card" style="margin-bottom: 14px;">
        <div class="property-name">{{ $hallName }}</div>
        <div class="property-address">{{ $booking->hall->address }}, {{ $cityName }}, {{ $regionName }}</div>
        @if ($booking->hall->phone)
            <div class="property-contact">
                {{ __('halls.phone') }}: {{ $booking->hall->phone }}
                @if ($booking->hall->email) &nbsp;|&nbsp; {{ __('halls.email') }}: {{ $booking->hall->email }} @endif
            </div>
        @endif
    </div>

    {{-- ========================================================================
        Booking Details (4 columns)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e4e7eb; margin-bottom: 14px;">
        <tr>
            <td style="vertical-align: middle; padding: 8px; border-bottom: 1px solid #e4e7eb;">
                <div class="info-label">{{ __('halls.date') }}</div>
                <div class="info-value">{{ $bookingDate->format('d M Y') }}</div>
                <div class="info-sub">{{ $bookingDate->format('l') }}</div>
            </td>
            <td style="vertical-align: middle; padding: 8px; border-bottom: 1px solid #e4e7eb; border-{{ $locale === 'ar' ? 'right' : 'left' }}: 1px solid #e4e7eb;">
                <div class="info-label">{{ __('halls.time_slot') }}</div>
                <div class="info-value">{{ __('halls.' . $booking->time_slot) }}</div>
            </td>
            <td style="vertical-align: middle; padding: 8px; border-bottom: 1px solid #e4e7eb; border-{{ $locale === 'ar' ? 'right' : 'left' }}: 1px solid #e4e7eb;">
                <div class="info-label">{{ __('halls.guests_count') }}</div>
                <div class="info-value">{{ $booking->number_of_guests }}</div>
                <div class="info-sub">{{ __('halls.persons') }}</div>
            </td>
            <td style="vertical-align: middle; padding: 8px; border-bottom: 1px solid #e4e7eb; border-{{ $locale === 'ar' ? 'right' : 'left' }}: 1px solid #e4e7eb;">
                <div class="info-label">{{ __('halls.payment_status') }}</div>
                <div style="background: #cce5ff; color: #004085; padding: 3px 8px; font-size: 7.5pt; font-weight: 600; display: inline-block;">{{ __('halls.payment_' . $booking->payment_status) }}</div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Two Column: Customer Info + Price Summary
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 14px;">
        <tr>
            {{-- Customer Information --}}
            <td width="49%" style="vertical-align: top;">
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 8px;"><tr><td style="font-size: 11pt; font-weight: 500; color: #1a2b3c; padding-bottom: 4px; border-bottom: 1px solid #d0d7de;">{{ __('halls.customer_information') }}</td></tr></table>
                <table width="100%" cellpadding="3" cellspacing="0">
                    <tr>
                        <td width="35%" style="color: #6b7a8d; font-size: 8pt;">{{ __('halls.name') }}</td>
                        <td style="font-weight: 500; font-size: 9pt;">{{ $booking->customer_name }}</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7a8d; font-size: 8pt;">{{ __('halls.email') }}</td>
                        <td style="font-size: 8pt;">{{ $booking->customer_email }}</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7a8d; font-size: 8pt;">{{ __('halls.phone') }}</td>
                        <td style="font-size: 9pt;">{{ $booking->customer_phone }}</td>
                    </tr>
                    @if ($booking->event_type)
                    <tr>
                        <td style="color: #6b7a8d; font-size: 8pt;">{{ __('halls.event') }}</td>
                        <td style="font-size: 9pt;">{{ __('halls.' . $booking->event_type) }}</td>
                    </tr>
                    @endif
                </table>
            </td>

            {{-- Spacer --}}
            <td width="2%"></td>

            {{-- Price Summary --}}
            <td width="49%" style="vertical-align: top;">
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 8px;"><tr><td style="font-size: 11pt; font-weight: 500; color: #1a2b3c; padding-bottom: 4px; border-bottom: 1px solid #d0d7de;">{{ __('halls.price_details') }}</td></tr></table>
                <table class="price-table" width="100%">
                    <tr>
                        <td>{{ __('halls.hall_price') }} ({{ __('halls.' . $booking->time_slot) }})</td>
                        <td style="text-align: {{ $locale === 'ar' ? 'left' : 'right' }}; font-weight: 500;">{{ number_format($booking->hall_price, 3) }} OMR</td>
                    </tr>
                    @foreach ($booking->extraServices as $service)
                        @php
                            $svcName = $service->service_name;
                            if (is_string($svcName)) { $svcName = json_decode($svcName, true) ?? $svcName; }
                            $svcDisplay = is_array($svcName) ? ($svcName[$locale] ?? $svcName['en'] ?? 'Service') : $svcName;
                        @endphp
                        <tr>
                            <td>{{ $svcDisplay }} ({{ $service->quantity }} x {{ number_format((float)$service->unit_price, 3) }})</td>
                            <td style="text-align: {{ $locale === 'ar' ? 'left' : 'right' }}; font-weight: 500;">{{ number_format((float)$service->total_price, 3) }} OMR</td>
                        </tr>
                    @endforeach
                    @if ($booking->platform_fee > 0)
                    <tr>
                        <td>{{ __('halls.platform_fee') }}</td>
                        <td style="text-align: {{ $locale === 'ar' ? 'left' : 'right' }}; font-weight: 500;">{{ number_format($booking->platform_fee, 3) }} OMR</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>{{ __('halls.total') }}</td>
                        <td style="text-align: {{ $locale === 'ar' ? 'left' : 'right' }};">{{ number_format($booking->total_amount, 3) }} OMR</td>
                    </tr>
                </table>
                @if ($booking->payment_type === 'advance' && $booking->advance_amount > 0)
                    <table width="100%" cellpadding="4" cellspacing="0" style="background: #f0f7f0; border: 1px solid #c3e0c3; font-size: 8.5pt; margin-top: 8px;">
                        <tr>
                            <td style="color: #2c5f2d;">{{ __('halls.advance_paid') }}</td>
                            <td style="text-align: {{ $locale === 'ar' ? 'left' : 'right' }}; font-weight: 500; color: #1e7e34;">{{ number_format($booking->advance_amount, 3) }} OMR</td>
                        </tr>
                        <tr>
                            <td style="color: #2c5f2d;">{{ __('halls.balance_due') }}</td>
                            <td style="text-align: {{ $locale === 'ar' ? 'left' : 'right' }}; font-weight: 500; color: #1e7e34;">{{ number_format($booking->balance_due, 3) }} OMR</td>
                        </tr>
                    </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Two Column: Location + Important Notes
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 14px;">
        <tr>
            {{-- Location --}}
            <td width="49%" style="vertical-align: top;">
                @if ($booking->hall->latitude && $booking->hall->longitude)
                    @php
                        $lat = number_format($booking->hall->latitude, 6, '.', '');
                        $lng = number_format($booking->hall->longitude, 6, '.', '');
                    @endphp
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 8px;"><tr><td style="font-size: 11pt; font-weight: 500; color: #1a2b3c; padding-bottom: 4px; border-bottom: 1px solid #d0d7de;">{{ __('halls.location') }}</td></tr></table>
                    <div style="font-size: 8pt; color: #4a5a6e; padding: 4px 0;">GPS: {{ $lat }}, {{ $lng }}</div>
                    <div style="font-size: 7.5pt; color: #0066b3; word-break: break-all;">
                        https://maps.google.com/?q={{ $lat }},{{ $lng }}
                    </div>
                @endif
            </td>

            {{-- Spacer --}}
            <td width="2%"></td>

            {{-- Important Notes --}}
            <td width="49%" style="vertical-align: top;">
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 8px;"><tr><td style="font-size: 11pt; font-weight: 500; color: #1a2b3c; padding-bottom: 4px; border-bottom: 1px solid #d0d7de;">{{ __('halls.important_info') }}</td></tr></table>
                <table width="100%" cellpadding="6" cellspacing="0" style="background: #fef9e7; border: 1px solid #f5c542;">
                    <tr><td style="font-size: 7.5pt; color: #664d00;">- {{ __('halls.bring_confirmation') }}</td></tr>
                    <tr><td style="font-size: 7.5pt; color: #664d00;">- {{ __('halls.arrive_on_time') }}</td></tr>
                    <tr><td style="font-size: 7.5pt; color: #664d00;">- {{ __('halls.cancellation_policy') }}</td></tr>
                    <tr><td style="font-size: 7.5pt; color: #664d00;">- {{ __('halls.contact_property') }}</td></tr>
                    @if ($booking->hall->check_in_instructions)
                        <tr><td style="font-size: 7.5pt; color: #664d00;">- {{ $booking->hall->check_in_instructions }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Footer
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 16px;">
        <tr>
            <td width="60%" style="vertical-align: middle; font-size: 7pt; color: #6b7a8d; padding-top: 10px; border-top: 1px solid #d0d7de;">
                <strong style="color: #1a2b3c;">{{ __('halls.need_help') }}</strong>
                @if($pdfSupportEmail){{ __('halls.email') }}: {{ $pdfSupportEmail }}@endif
                @if($pdfSupportEmail && $pdfSupportPhone) &nbsp;|&nbsp; @endif
                @if($pdfSupportPhone){{ __('halls.phone') }}: {{ $pdfSupportPhone }}@endif
            </td>
            <td width="40%" style="vertical-align: middle; text-align: {{ $locale === 'ar' ? 'left' : 'right' }}; font-size: 7pt; color: #8a9cb0; padding-top: 10px; border-top: 1px solid #d0d7de;">
                <div style="font-size: 12pt; font-weight: 500; color: #0066b3;">Majalis.om</div>
                <div>{{ __('halls.pdf_generated') }}: {{ now()->format('d M Y, H:i') }}</div>
            </td>
        </tr>
    </table>

</body>

</html>
