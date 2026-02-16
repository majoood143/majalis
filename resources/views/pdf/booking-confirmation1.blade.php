<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('halls.booking_confirmation') }} - {{ $booking->booking_number }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.5;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .container {
            padding: 30px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 30px;
            margin: -30px -30px 30px -30px;
        }

        .logo {
            font-size: 32pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-title {
            font-size: 18pt;
            margin-bottom: 20px;
        }

        .confirmation-box {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .confirmation-number {
            font-size: 24pt;
            font-weight: bold;
            letter-spacing: 2px;
        }

        /* Info Box */
        .info-box {
            background: #f0f9ff;
            border: 2px solid #0284c7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-cell {
            display: table-cell;
            width: 25%;
            vertical-align: top;
        }

        .info-label {
            color: #666;
            font-size: 9pt;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 14pt;
            font-weight: bold;
            color: #0369a1;
        }

        /* Hall Details */
        .hall-section {
            margin-bottom: 30px;
        }

        .hall-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .hall-name {
            font-size: 18pt;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 5px;
        }

        .hall-address {
            color: #666;
            margin-bottom: 15px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th {
            background: #f0f9ff;
            padding: 12px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-weight: bold;
            border-bottom: 2px solid #0284c7;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background: #f9fafb;
            font-weight: bold;
            font-size: 12pt;
        }

        .total-amount {
            color: #0369a1;
            font-size: 16pt;
        }

        /* Features */
        .features-grid {
            display: table;
            width: 100%;
        }

        .feature-row {
            display: table-row;
        }

        .feature-cell {
            display: table-cell;
            width: 50%;
            padding: 8px;
        }

        .feature-item {
            padding: 8px;
            background: #f9fafb;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        /* Map Placeholder */
        .map-box {
            background: #e5e7eb;
            height: 250px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
        }

        /* Footer */
        .footer {
            background: #f9fafb;
            padding: 20px;
            margin: 30px -30px -30px -30px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .contact-info {
            margin: 15px 0;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        /* Utilities */
        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: bold;
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
    </style>
</head>

<body>
    <div class="container">

        <!-- Header -->
        <div class="header">
            <div class="logo">Majalis</div>
            <div class="header-title">{{ __('halls.booking_confirmed') }}</div>

            <div class="confirmation-box">
                <div style="font-size: 10pt; margin-bottom: 5px;">{{ __('halls.booking_reference') }}</div>
                <div class="confirmation-number">{{ $booking->booking_number }}</div>
            </div>
        </div>

        <!-- Check-in/Check-out Info -->
        <div class="info-box">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">{{ __('halls.check_in') }}</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</div>
                    <div style="font-size: 9pt; color: #666;">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('F') }}</div>
                    <div style="font-size: 9pt; color: #666;">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('l') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">{{ __('halls.check_out') }}</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($booking->booking_date)->addDay()->format('d') }}
                    </div>
                    <div style="font-size: 9pt; color: #666;">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->addDay()->format('F') }}</div>
                    <div style="font-size: 9pt; color: #666;">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->addDay()->format('l') }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">{{ __('halls.time_slot') }}</div>
                    <div class="info-value">{{ __('halls.' . $booking->time_slot) }}</div>
                </div>
                <div class="info-cell">
                    <div class="info-label">{{ __('halls.guests') }}</div>
                    <div class="info-value">{{ $booking->number_of_guests }}</div>
                </div>
            </div>
        </div>

        <!-- Hall Details -->
        <div class="hall-section">
            @if ($booking->hall->featured_image)
                <img src="{{ public_path('storage/' . $booking->hall->featured_image) }}" class="hall-image"
                    alt="{{ is_array($booking->hall->name) ? $booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] : $booking->hall->name }}">
            @endif

            <div class="hall-name">
                {{ is_array($booking->hall->name) ? $booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] : $booking->hall->name }}
            </div>
            <div class="hall-address">
                {{ $booking->hall->address }},
                {{ is_array($booking->hall->city->name) ? $booking->hall->city->name[app()->getLocale()] ?? $booking->hall->city->name['en'] : $booking->hall->city->name }},
                {{ is_array($booking->hall->city->region->name) ? $booking->hall->city->region->name[app()->getLocale()] ?? $booking->hall->city->region->name['en'] : $booking->hall->city->region->name }}
            </div>

            @if ($booking->hall->phone)
                <div style="margin-bottom: 5px;">{{ __('halls.phone') }}: {{ $booking->hall->phone }}</div>
            @endif

            @if ($booking->hall->latitude && $booking->hall->longitude)
                <div>GPS: {{ number_format($booking->hall->latitude, 6) }},
                    {{ number_format($booking->hall->longitude, 6) }}</div>
            @endif
        </div>

        <!-- Customer Information -->
        <div class="mb-20">
            <h3 style="font-size: 14pt; color: #0369a1; margin-bottom: 10px;">{{ __('halls.customer_information') }}
            </h3>
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                <div class="mb-10"><strong>{{ __('halls.name') }}:</strong> {{ $booking->customer_name }}</div>
                <div class="mb-10"><strong>{{ __('halls.email') }}:</strong> {{ $booking->customer_email }}</div>
                <div class="mb-10"><strong>{{ __('halls.phone') }}:</strong> {{ $booking->customer_phone }}</div>
                @if ($booking->event_type)
                    <div><strong>{{ __('halls.event_type') }}:</strong> {{ __('halls.' . $booking->event_type) }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Price Breakdown -->
        <h3 style="font-size: 14pt; color: #0369a1; margin-bottom: 10px;">{{ __('halls.price_details') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('halls.description') }}</th>
                    <th class="text-right">{{ __('halls.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('halls.hall_price') }} ({{ __('halls.' . $booking->time_slot) }})</td>
                    <td class="text-right">{{ number_format($booking->hall_price, 3) }} OMR</td>
                </tr>

                {{-- @if ($booking->extraServices->count() > 0)
                    @foreach ($booking->extraServices as $service)
                        <tr>
                            <td>
                                {{ is_array($service->name) ? $service->name[app()->getLocale()] ?? $service->name['en'] : $service->name }}
                                ({{ $service->pivot->quantity }} × {{ number_format($service->pivot->unit_price, 3) }}
                                OMR)
                            </td>
                            <td class="text-right">{{ number_format($service->pivot->total_price, 3) }} OMR</td>
                        </tr>
                    @endforeach
                @endif --}}

                @if ($booking->extraServices->count() > 0)
                    @foreach ($booking->extraServices as $service)
                        <tr>
                            <td>
                                {{-- FIX: Use service_name (direct attribute) instead of name --}}
                                {{-- FIX: Access quantity/unit_price directly, NOT via pivot --}}
                                @php
                                    $serviceName = $service->service_name;
                                    if (is_string($serviceName)) {
                                        $serviceName = json_decode($serviceName, true) ?? $serviceName;
                                    }
                                @endphp
                                {{ is_array($serviceName) ? $serviceName[app()->getLocale()] ?? ($serviceName['en'] ?? 'N/A') : $serviceName }}
                                ({{ $service->quantity }} × {{ number_format((float) $service->unit_price, 3) }}
                                OMR)
                            </td>
                            <td class="text-right">{{ number_format((float) $service->total_price, 3) }} OMR</td>
                        </tr>
                    @endforeach
                @endif

                @if ($booking->platform_fee > 0)
                    <tr>
                        <td>{{ __('halls.platform_fee') }}</td>
                        <td class="text-right">{{ number_format($booking->platform_fee, 3) }} OMR</td>
                    </tr>
                @endif

                <tr class="total-row">
                    <td>{{ __('halls.total') }}</td>
                    <td class="text-right total-amount">{{ number_format($booking->total_amount, 3) }} OMR</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Status -->
        <div style="text-align: center; margin: 20px 0;">
            <span class="status-badge status-{{ $booking->payment_status }}">
                {{ __('halls.payment_status') }}: {{ strtoupper($booking->payment_status) }}
            </span>
        </div>

        <!-- Map (Placeholder) -->
        @if ($booking->hall->latitude && $booking->hall->longitude)
            <div class="map-box">
                <div style="text-align: center; color: #666;">
                    <div style="font-size: 14pt; margin-bottom: 5px;">{{ __('halls.location') }}</div>
                    <div>GPS: {{ number_format($booking->hall->latitude, 6) }},
                        {{ number_format($booking->hall->longitude, 6) }}</div>
                    <div style="font-size: 9pt; margin-top: 10px;">{{ __('halls.view_on_map') }}: maps.google.com</div>
                </div>
            </div>
        @endif

        <!-- Important Information -->
        <div style="background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <h4 style="color: #92400e; margin-bottom: 10px;">{{ __('halls.important_info') }}</h4>
            <ul style="margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 20px; color: #78350f;">
                <li>{{ __('halls.bring_confirmation') }}</li>
                <li>{{ __('halls.arrive_on_time') }}</li>
                <li>{{ __('halls.cancellation_policy') }}</li>
                <li>{{ __('halls.contact_property') }}</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="contact-info">
                <strong>{{ __('halls.need_help') }}</strong><br>
                {{ __('halls.email') }}: support@majalis.om | {{ __('halls.phone') }}: +968 XXXX XXXX<br>
                {{ __('halls.website') }}: www.majalis.om
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                {{ __('halls.pdf_generated') }}: {{ now()->format('d M Y, H:i') }}<br>
                {{ __('halls.thank_you') }}
            </div>
        </div>

    </div>
</body>

</html>
