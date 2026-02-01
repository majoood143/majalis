{{--
|--------------------------------------------------------------------------
| Booking Confirmation PDF Template - Single Page Professional Design
|--------------------------------------------------------------------------
|
| This template generates a compact, professional booking confirmation
| receipt designed to fit on a single A4 page.
|
| Features:
| - Compact header with booking reference
| - Two-column layout for efficient space usage
| - Static map using OpenStreetMap tiles
| - Clean pricing table
| - Professional footer with contact info
|
| @package    Majalis
| @version    2.0.0
| @author     Majalis Development Team
| @requires   DomPDF, Laravel 12, PHP 8.4.12
|
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('halls.booking_confirmation') }} - {{ $booking->booking_number }}</title>

    <style>
        /* ==========================================================================
           Page Setup - A4 Single Page Configuration
           ========================================================================== */
        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', sans-serif;
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.4;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        /* ==========================================================================
           Header Section - Compact Brand Header
           ========================================================================== */
        .header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 12px 20px;
            margin: -15mm -15mm 12px -15mm;
            position: relative;
        }

        .header-content {
            display: table;
            width: 100%;
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
            font-size: 22pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .tagline {
            font-size: 8pt;
            opacity: 0.9;
            margin-top: 2px;
        }

        .booking-ref-box {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 6px;
            display: inline-block;
        }

        .booking-ref-label {
            font-size: 7pt;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .booking-ref-number {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* ==========================================================================
           Main Content - Two Column Layout
           ========================================================================== */
        .main-content {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .col-left {
            display: table-cell;
            width: 58%;
            vertical-align: top;
            padding-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 12px;
        }

        .col-right {
            display: table-cell;
            width: 42%;
            vertical-align: top;
            padding-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 12px;
            border-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 1px solid #e5e7eb;
        }

        /* ==========================================================================
           Section Styling
           ========================================================================== */
        .section {
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #0369a1;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        /* ==========================================================================
           Booking Details Grid
           ========================================================================== */
        .booking-grid {
            display: table;
            width: 100%;
            background: #f0f9ff;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
        }

        .booking-grid-row {
            display: table-row;
        }

        .booking-grid-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 5px;
            vertical-align: top;
        }

        .grid-label {
            font-size: 7pt;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .grid-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0369a1;
        }

        .grid-sub {
            font-size: 7pt;
            color: #64748b;
        }

        /* ==========================================================================
           Hall Information
           ========================================================================== */
        .hall-info {
            background: #f8fafc;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .hall-name {
            font-size: 12pt;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 4px;
        }

        .hall-address {
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 6px;
        }

        .hall-contact {
            font-size: 8pt;
            color: #374151;
        }

        /* ==========================================================================
           Customer Information
           ========================================================================== */
        .customer-info {
            font-size: 8pt;
        }

        .customer-info div {
            margin-bottom: 4px;
        }

        .customer-label {
            color: #64748b;
            display: inline-block;
            width: 50px;
        }

        /* ==========================================================================
           Pricing Table - Compact Design
           ========================================================================== */
        .price-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .price-table th {
            background: #f0f9ff;
            padding: 6px 8px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-weight: bold;
            color: #0369a1;
            border-bottom: 2px solid #0284c7;
        }

        .price-table th:last-child {
            text-align: right;
            width: 90px;
        }

        .price-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .price-table td:last-child {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .price-table .total-row {
            background: #0369a1;
            color: white;
            font-weight: bold;
            font-size: 10pt;
        }

        .price-table .total-row td {
            padding: 8px;
            border: none;
        }

        /* ==========================================================================
           Payment Status Badge
           ========================================================================== */
        .status-container {
            text-align: center;
            margin: 10px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-paid {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-partial {
            background: #e0e7ff;
            color: #3730a3;
        }

        /* ==========================================================================
           Map Section - Static OpenStreetMap
           ========================================================================== */
        .map-section {
            margin-bottom: 10px;
        }

        .map-container {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .map-image {
            width: 100%;
            height: 120px;
            display: block;
        }

        .map-coords {
            font-size: 7pt;
            color: #64748b;
            text-align: center;
        }

        .map-link {
            font-size: 7pt;
            color: #0284c7;
            text-align: center;
            margin-top: 4px;
        }

        /* ==========================================================================
           Important Notes - Compact Box
           ========================================================================== */
        .notes-box {
            background: #fffbeb;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 10px;
        }

        .notes-title {
            font-size: 8pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }

        .notes-list {
            font-size: 7pt;
            color: #78350f;
            margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 12px;
        }

        .notes-list li {
            margin-bottom: 2px;
        }

        /* ==========================================================================
           Footer - Compact Contact Information
           ========================================================================== */
        .footer {
            background: #f1f5f9;
            padding: 10px 15px;
            /* margin: 0 -15mm -12mm -15mm; */
            border-top: 2px solid #0284c7;
            /* position: fixed; */
            bottom: 0;
            left: 0;
            right: 0;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .footer-right {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .footer-contact {
            font-size: 7pt;
            color: #64748b;
        }

        .footer-brand {
            font-size: 10pt;
            font-weight: bold;
            color: #0369a1;
        }

        .footer-generated {
            font-size: 6pt;
            color: #94a3b8;
            margin-top: 3px;
        }

        /* ==========================================================================
           Utilities
           ========================================================================== */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-muted {
            color: #64748b;
        }

        .text-small {
            font-size: 7pt;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        .mb-8 {
            margin-bottom: 8px;
        }

        .divider {
            border-top: 1px solid #e5e7eb;
            margin: 8px 0;
        }
    </style>
</head>

<body>
    {{-- ========================================================================
        Header Section - Brand & Booking Reference
        ======================================================================== --}}
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="logo">Majalis</div>
                <div class="tagline">{{ __('halls.booking_confirmed') }}</div>
            </div>
            <div class="header-right">
                <div class="booking-ref-box">
                    <div class="booking-ref-label">{{ __('halls.booking_reference') }}</div>
                    <div class="booking-ref-number">{{ $booking->booking_number }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================
        Booking Details Grid - Date, Time, Guests
        ======================================================================== --}}
    <div class="booking-grid">
        <div class="booking-grid-row">
            {{-- Booking Date --}}
            <div class="booking-grid-cell">
                <div class="grid-label">{{ __('halls.date') }}</div>
                <div class="grid-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M') }}</div>
                <div class="grid-sub">{{ \Carbon\Carbon::parse($booking->booking_date)->format('l, Y') }}</div>
            </div>

            {{-- Time Slot --}}
            <div class="booking-grid-cell">
                <div class="grid-label">{{ __('halls.time_slot') }}</div>
                <div class="grid-value" style="font-size: 9pt;">{{ __('halls.' . $booking->time_slot) }}</div>
            </div>

            {{-- Number of Guests --}}
            <div class="booking-grid-cell">
                <div class="grid-label">{{ __('halls.guests') }}</div>
                <div class="grid-value">{{ $booking->number_of_guests }}</div>
                <div class="grid-sub">{{ __('halls.persons') }}</div>
            </div>

            {{-- Payment Status --}}
            <div class="booking-grid-cell">
                <div class="grid-label">{{ __('halls.status') }}</div>
                <span class="status-badge status-{{ $booking->payment_status }}">
                    {{ __('halls.payment_' . $booking->payment_status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ========================================================================
        Main Content - Two Column Layout
        ======================================================================== --}}
    <div class="main-content">
        {{-- Left Column - Hall & Customer Info --}}
        <div class="col-left">
            {{-- Hall Information Section --}}
            <div class="section">
                <div class="section-title">{{ __('halls.venue_details') }}</div>
                <div class="hall-info">
                    {{-- Extract hall name with locale support --}}
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

                    <div class="hall-name">{{ $hallName }}</div>
                    <div class="hall-address">
                        {{ $booking->hall->address }}, {{ $cityName }}, {{ $regionName }}
                    </div>

                    @if ($booking->hall->phone)
                        <div class="hall-contact">
                            <strong>{{ __('halls.phone') }}:</strong> {{ $booking->hall->phone }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Customer Information Section --}}
            <div class="section">
                <div class="section-title">{{ __('halls.customer_information') }}</div>
                <div class="customer-info">
                    <div>
                        <span class="customer-label">{{ __('halls.name') }}:</span>
                        <strong>{{ $booking->customer_name }}</strong>
                    </div>
                    <div>
                        <span class="customer-label">{{ __('halls.email') }}:</span>
                        {{ $booking->customer_email }}
                    </div>
                    <div>
                        <span class="customer-label">{{ __('halls.phone') }}:</span>
                        {{ $booking->customer_phone }}
                    </div>
                    @if ($booking->event_type)
                        <div>
                            <span class="customer-label">{{ __('halls.event') }}:</span>
                            {{ __('halls.' . $booking->event_type) }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Price Breakdown Table --}}
            <div class="section">
                <div class="section-title">{{ __('halls.price_details') }}</div>
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>{{ __('halls.description') }}</th>
                            <th>{{ __('halls.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Hall Base Price --}}
                        <tr>
                            <td>{{ __('halls.hall_price') }} ({{ __('halls.' . $booking->time_slot) }})</td>
                            <td>{{ number_format($booking->hall_price, 3) }} OMR</td>
                        </tr>

                        {{-- Extra Services (Fixed: direct attributes, not pivot) --}}
                        @if ($booking->extraServices->count() > 0)
                            @foreach ($booking->extraServices as $service)
                                @php
                                    // Handle service_name which may be JSON string or array
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
                                        <span class="text-muted">
                                            ({{ $service->quantity }} × {{ number_format((float) $service->unit_price, 3) }})
                                        </span>
                                    </td>
                                    <td>{{ number_format((float) $service->total_price, 3) }} OMR</td>
                                </tr>
                            @endforeach
                        @endif

                        {{-- Platform Fee (if applicable) --}}
                        @if ($booking->platform_fee > 0)
                            <tr>
                                <td>{{ __('halls.platform_fee') }}</td>
                                <td>{{ number_format($booking->platform_fee, 3) }} OMR</td>
                            </tr>
                        @endif

                        {{-- Total Amount --}}
                        <tr class="total-row">
                            <td>{{ __('halls.total') }}</td>
                            <td>{{ number_format($booking->total_amount, 3) }} OMR</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Advance Payment Info (if applicable) --}}
                @if ($booking->payment_type === 'advance' && $booking->advance_amount > 0)
                    <div style="background: #f0f9ff; padding: 6px 8px; border-radius: 4px; margin-top: 6px; font-size: 7pt;">
                        <div style="display: table; width: 100%;">
                            <div style="display: table-row;">
                                <div style="display: table-cell;">{{ __('halls.advance_paid') }}:</div>
                                <div style="display: table-cell; text-align: right; color: #059669; font-weight: bold;">
                                    {{ number_format($booking->advance_amount, 3) }} OMR
                                </div>
                            </div>
                            <div style="display: table-row;">
                                <div style="display: table-cell;">{{ __('halls.balance_due') }}:</div>
                                <div style="display: table-cell; text-align: right; color: #dc2626; font-weight: bold;">
                                    {{ number_format($booking->balance_due, 3) }} OMR
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column - Map & Notes --}}
        <div class="col-right">
            {{-- Location Map Section --}}
            @if ($booking->hall->latitude && $booking->hall->longitude)
                <div class="section">
                    <div class="section-title">{{ __('halls.location') }}</div>
                    <div class="map-section">
                        <div class="map-container">
                            {{--
                                Static Map using OpenStreetMap via staticmap.openstreetmap.de
                                This service generates map images without JavaScript
                                Parameters:
                                - center: latitude,longitude
                                - zoom: map zoom level (15 is good for venue location)
                                - size: image dimensions in pixels
                                - markers: latitude,longitude,marker-style
                            --}}
                            @php
                                $lat = number_format($booking->hall->latitude, 6, '.', '');
                                $lng = number_format($booking->hall->longitude, 6, '.', '');
                                $mapUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-s+ff0000({$lng},{$lat})/{$lng},{$lat},15,0/400x150?access_token=YOUR_TOKEN";

                                //$mapUrl = "https://staticmap.openstreetmap.de/staticmap.php?center={$lat},{$lng}&zoom=15&size=400x150&maptype=osmarenderer&markers={$lat},{$lng},red-pushpin";
                            @endphp
                            <img src="{{ $mapUrl }}"
                                 alt="{{ __('halls.location_map') }}"
                                 class="map-image"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            {{-- Fallback if map image fails to load --}}
                            <div style="display: none; height: 120px; background: #e5e7eb; text-align: center; padding-top: 45px; color: #64748b;">
                                {{ __('halls.map_unavailable') }}
                            </div>
                        </div>

                        {{-- GPS Coordinates --}}
                        <div class="map-coords">
                            GPS: {{ number_format($booking->hall->latitude, 6) }}, {{ number_format($booking->hall->longitude, 6) }}
                        </div>

                        {{-- Google Maps Link --}}
                        <div class="map-link">
                            {{ __('halls.view_on_map') }}:
                            maps.google.com/?q={{ $lat }},{{ $lng }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Important Information Box --}}
            <div class="section">
                <div class="notes-box">
                    <div class="notes-title">{{ __('halls.important_info') }}</div>
                    <ul class="notes-list">
                        <li>{{ __('halls.bring_confirmation') }}</li>
                        <li>{{ __('halls.arrive_on_time') }}</li>
                        <li>{{ __('halls.cancellation_policy') }}</li>
                        <li>{{ __('halls.contact_property') }}</li>
                    </ul>
                </div>
            </div>

            {{-- QR Code Placeholder (Optional - can be implemented later) --}}
            {{--
            <div class="text-center section">
                <div class="section-title">{{ __('halls.scan_qr') }}</div>
                <img src="{{ $qrCodeUrl }}" alt="QR Code" style="width: 80px; height: 80px;">
            </div>
            --}}
        </div>
    </div>

    {{-- ========================================================================
        Footer - Contact Information & Generation Timestamp
        ======================================================================== --}}
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <div class="footer-contact">
                    <strong>{{ __('halls.need_help') }}</strong>
                    {{ __('halls.email') }}: support@majalis.om |
                    {{ __('halls.phone') }}: +968 XXXX XXXX |
                    {{ __('halls.website') }}: www.majalis.om
                </div>
            </div>
            <div class="footer-right">
                <div class="footer-brand">{{ __('halls.thank_you') }}</div>
                <div class="footer-generated">
                    {{ __('halls.pdf_generated') }}: {{ now()->format('d M Y, H:i') }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
