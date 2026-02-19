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
        /* ==========================================================================
           Page Setup - A4 Single Page
           ========================================================================== */
        @page {
            size: A4;
            margin: 15mm 15mm 12mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', 'Helvetica', sans-serif;
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
            letter-spacing: -0.5px;
        }

        .logo span {
            font-weight: 300;
            color: #4a5a6e;
        }

        .confirmation-badge {
            background: #008b5d;
            color: white;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 9pt;
            font-weight: 500;
            display: inline-block;
            letter-spacing: 0.3px;
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
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            text-transform: uppercase;
            letter-spacing: 0.3px;
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
            padding-bottom: 4px;
            border-bottom: 1px solid #d0d7de;
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
            padding: 3px 0 3px 16px;
            position: relative;
        }

        .notes-list li:before {
            content: "•";
            color: #f5c542;
            font-weight: bold;
            position: absolute;
            {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 4px;
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
            border-radius: 30px;
            font-size: 7.5pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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

    {{-- ========================================================================
        Header - Logo & Booking Reference
        ======================================================================== --}}
    <div class="header">
        <div class="header-left">
            <div class="logo">Majalis<span>.om</span></div>
        </div>
        <div class="header-right">
            <div class="confirmation-badge">{{ __('halls.booking_confirmed') }}</div>
            <div class="booking-ref-label">{{ __('halls.booking_reference') }}</div>
            <div class="booking-ref">{{ $booking->booking_number }}</div>
        </div>
    </div>

    {{-- ========================================================================
        Property Card (Prominent)
        ======================================================================== --}}
    @php
        $hallName = is_array($booking->hall->name)
            ? ($booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] ?? 'N/A')
            : $booking->hall->name;

        $cityName = is_array($booking->hall->city->name)
            ? ($booking->hall->city->name[app()->getLocale()] ?? $booking->hall->city->name['en'] ?? '')
            : $booking->hall->city->name;

        $regionName = is_array($booking->hall->city->region->name)
            ? ($booking->hall->city->region->name[app()->getLocale()] ?? $booking->hall->city->region->name['en'] ?? '')
            : $booking->hall->city->region->name;
    @endphp

    <div class="property-card">
        <div class="property-name">{{ $hallName }}</div>
        <div class="property-address">
            {{ $booking->hall->address }}, {{ $cityName }}, {{ $regionName }}
        </div>
        @if ($booking->hall->phone)
            <div class="property-contact">
                <span>📞</span> {{ $booking->hall->phone }}
                @if ($booking->hall->email) | ✉️ {{ $booking->hall->email }} @endif
            </div>
        @endif
    </div>

    {{-- ========================================================================
        Booking Details Grid (Date, Time, Guests, Status)
        ======================================================================== --}}
    <div class="info-grid">
        <div class="info-row">
            {{-- Booking Date --}}
            <div class="info-cell" style="width: 25%;">
                <div class="info-label">{{ __('halls.date') }}</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
                <div class="info-sub">{{ \Carbon\Carbon::parse($booking->booking_date)->format('l') }}</div>
            </div>

            {{-- Time Slot --}}
            <div class="info-cell" style="width: 25%;">
                <div class="info-label">{{ __('halls.time_slot') }}</div>
                <div class="info-value">{{ __('halls.' . $booking->time_slot) }}</div>
            </div>

            {{-- Guests --}}
            <div class="info-cell" style="width: 25%;">
                <div class="info-label">{{ __('halls.guests_count') }}</div>
                <div class="info-value">{{ $booking->number_of_guests }}</div>
                <div class="info-sub">{{ __('halls.persons') }}</div>
            </div>

            {{-- Status --}}
            <div class="info-cell" style="width: 25%;">
                <div class="info-label">{{ __('halls.payment_status') }}</div>
                <span class="status-badge status-{{ $booking->payment_status }}">
                    {{ __('halls.payment_' . $booking->payment_status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ========================================================================
        Two Column Layout - Guest & Price Info
        ======================================================================== --}}
    <div class="two-column">
        {{-- Left Column: Guest Information + Event --}}
        <div class="col-left">
            <div class="section-title">
                <span>{{ __('halls.customer_information') }}</span>
            </div>
            <div class="detail-row">
                <div class="detail-label">{{ __('halls.name') }}</div>
                <div class="detail-value">{{ $booking->customer_name }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">{{ __('halls.email') }}</div>
                <div class="detail-value">{{ $booking->customer_email }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">{{ __('halls.phone') }}</div>
                <div class="detail-value">{{ $booking->customer_phone }}</div>
            </div>
            @if ($booking->event_type)
                <div class="detail-row">
                    <div class="detail-label">{{ __('halls.event') }}</div>
                    <div class="detail-value">{{ __('halls.' . $booking->event_type) }}</div>
                </div>
            @endif

            {{-- Optional: Include any special requests if available --}}
            @if ($booking->special_requests)
                <div class="mt-3 text-small text-muted">
                    <strong>{{ __('halls.special_requests') }}:</strong><br>
                    {{ $booking->special_requests }}
                </div>
            @endif
        </div>

        {{-- Right Column: Price Summary --}}
        <div class="col-right">
            <div class="section-title">
                <span>{{ __('halls.price_details') }}</span>
            </div>
            <table class="price-table">
                {{-- Base Price --}}
                <tr>
                    <td>{{ __('halls.hall_price') }} ({{ __('halls.' . $booking->time_slot) }})</td>
                    <td>{{ number_format($booking->hall_price, 3) }} OMR</td>
                </tr>

                {{-- Extra Services --}}
                @if ($booking->extraServices->count() > 0)
                    @foreach ($booking->extraServices as $service)
                        @php
                            $serviceName = $service->service_name;
                            if (is_string($serviceName)) {
                                $serviceName = json_decode($serviceName, true) ?? $serviceName;
                            }
                            $displayName = is_array($serviceName)
                                ? ($serviceName[app()->getLocale()] ?? $serviceName['en'] ?? 'Service')
                                : $serviceName;
                        @endphp
                        <tr>
                            <td>
                                {{ $displayName }}
                                <span class="text-muted">({{ $service->quantity }} × {{ number_format((float) $service->unit_price, 3) }})</span>
                            </td>
                            <td>{{ number_format((float) $service->total_price, 3) }} OMR</td>
                        </tr>
                    @endforeach
                @endif

                {{-- Platform Fee --}}
                @if ($booking->platform_fee > 0)
                    <tr>
                        <td>{{ __('halls.platform_fee') }}</td>
                        <td>{{ number_format($booking->platform_fee, 3) }} OMR</td>
                    </tr>
                @endif

                {{-- Total --}}
                <tr class="total-row">
                    <td>{{ __('halls.total') }}</td>
                    <td>{{ number_format($booking->total_amount, 3) }} OMR</td>
                </tr>
            </table>

            {{-- Advance Payment Info --}}
            @if ($booking->payment_type === 'advance' && $booking->advance_amount > 0)
                <div class="advance-box">
                    <div class="advance-row">
                        <div class="advance-label">{{ __('halls.advance_paid') }}</div>
                        <div class="advance-value">{{ number_format($booking->advance_amount, 3) }} OMR</div>
                    </div>
                    <div class="advance-row">
                        <div class="advance-label">{{ __('halls.balance_due') }}</div>
                        <div class="advance-value">{{ number_format($booking->balance_due, 3) }} OMR</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ========================================================================
        Map & Important Notes - Two Column Layout
        ======================================================================== --}}
    <div class="two-column">
        {{-- Left Column: Map (if coordinates exist) --}}
        <div class="col-left">
            @if ($booking->hall->latitude && $booking->hall->longitude)
                <div class="section-title">
                    <span>{{ __('halls.location') }}</span>
                </div>
                @php
                    $lat = number_format($booking->hall->latitude, 6, '.', '');
                    $lng = number_format($booking->hall->longitude, 6, '.', '');
                    // Using a reliable static map service (openstreetmap)
                    $mapUrl = "https://staticmap.openstreetmap.de/staticmap.php?center={$lat},{$lng}&zoom=15&size=400x150&maptype=osmarenderer&markers={$lat},{$lng},red-pushpin";
                @endphp
                <div class="map-container">
                    <img src="{{ $mapUrl }}"
                         alt="{{ __('halls.map') }}"
                         class="map-image"
                         onerror="this.style.display='none'; this.parentNode.querySelector('.map-fallback').style.display='block';">
                    <div class="map-fallback" style="display: none; height: 130px; background: #eef2f6; text-align: center; padding-top: 50px; color: #6b7a8d;">
                        {{ __('halls.map_unavailable') }}
                    </div>
                </div>
                <div class="map-coords">
                    GPS: {{ $lat }}, {{ $lng }}
                </div>
                <div class="map-link">
                    <a href="https://maps.google.com/?q={{ $lat }},{{ $lng }}" style="color: #0066b3; text-decoration: none;">{{ __('halls.view_on_map') }} (Google Maps)</a>
                </div>
            @endif
        </div>

        {{-- Right Column: Important Information --}}
        <div class="col-right">
            <div class="section-title">
                <span>{{ __('halls.important_info') }}</span>
            </div>
            <div class="notes-box">
                <ul class="notes-list">
                    <li>{{ __('halls.bring_confirmation') }}</li>
                    <li>{{ __('halls.arrive_on_time') }}</li>
                    <li>{{ __('halls.cancellation_policy') }}</li>
                    <li>{{ __('halls.contact_property') }}</li>
                    @if ($booking->hall->check_in_instructions)
                        <li>{{ $booking->hall->check_in_instructions }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- ========================================================================
        Footer - Contact & Timestamp
        ======================================================================== --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-contact">
                <strong>{{ __('halls.need_help') }}</strong> {{ __('halls.email') }}: <a href="mailto:support@majalis.om" style="color: #0066b3; text-decoration: none;">support@majalis.om</a> |
                {{ __('halls.phone') }}: +968 1234 5678
            </div>
        </div>
        <div class="footer-right">
            <div class="footer-logo">Majalis.om</div>
            <div>{{ __('halls.pdf_generated') }}: {{ now()->format('d M Y, H:i') }}</div>
        </div>
    </div>

</body>

</html>
