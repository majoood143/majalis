{{--
    Print Invoice View

    A print-optimized invoice template for Majalis hall bookings.
    Supports bilingual display (English/Arabic) with RTL layout.
    Auto-triggers print dialog on page load.

    @package Resources\Views\Invoices
    @var \App\Models\Booking $booking
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('booking.invoice.title') }} - {{ $booking->booking_number }}</title>

    <style>
        /* ============================================
           CSS Variables & Reset
           ============================================ */
        :root {
            --primary-color: #1e40af;
            --secondary-color: #64748b;
            --success-color: #16a34a;
            --warning-color: #ca8a04;
            --danger-color: #dc2626;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ============================================
           Base Styles
           ============================================ */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: var(--text-dark);
            background: white;
            padding: 20px;
        }

        /* RTL Support */
        [dir="rtl"] body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        /* ============================================
           Header Section
           ============================================ */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--primary-color);
            margin-bottom: 20px;
        }

        [dir="rtl"] .invoice-header {
            flex-direction: row-reverse;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .invoice-title-section {
            text-align: right;
        }

        [dir="rtl"] .invoice-title-section {
            text-align: left;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-number {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .invoice-number strong {
            color: var(--text-dark);
            font-size: 16px;
        }

        /* ============================================
           Status Badge
           ============================================ */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ============================================
           Info Grid
           ============================================ */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-box {
            background: var(--bg-light);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .info-box-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 11px;
        }

        [dir="rtl"] .info-row {
            flex-direction: row-reverse;
        }

        .info-label {
            color: var(--text-muted);
        }

        .info-value {
            font-weight: 500;
            color: var(--text-dark);
        }

        /* ============================================
           Hall Details
           ============================================ */
        .hall-section {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #bfdbfe;
        }

        .hall-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .hall-details {
            display: flex;
            gap: 20px;
            font-size: 11px;
            color: var(--text-muted);
        }

        [dir="rtl"] .hall-details {
            flex-direction: row-reverse;
        }

        /* ============================================
           Tables
           ============================================ */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table th {
            background: var(--primary-color);
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        [dir="rtl"] .invoice-table th {
            text-align: right;
        }

        .invoice-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border-color);
            font-size: 11px;
        }

        .invoice-table tbody tr:hover {
            background: var(--bg-light);
        }

        .invoice-table .text-right {
            text-align: right;
        }

        [dir="rtl"] .invoice-table .text-right {
            text-align: left;
        }

        .invoice-table .text-center {
            text-align: center;
        }

        /* ============================================
           Totals Section
           ============================================ */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        [dir="rtl"] .totals-section {
            justify-content: flex-start;
        }

        .totals-box {
            width: 300px;
            background: var(--bg-light);
            border-radius: 8px;
            padding: 15px;
            border: 1px solid var(--border-color);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 12px;
        }

        [dir="rtl"] .total-row {
            flex-direction: row-reverse;
        }

        .total-row.subtotal {
            border-top: 1px dashed var(--border-color);
            margin-top: 5px;
            padding-top: 10px;
        }

        .total-row.grand-total {
            border-top: 2px solid var(--primary-color);
            margin-top: 10px;
            padding-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: var(--primary-color);
        }

        /* ============================================
           Advance Payment Section
           ============================================ */
        .advance-payment-section {
            background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
            border: 1px solid #fde047;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .advance-payment-title {
            font-size: 12px;
            font-weight: 600;
            color: #92400e;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .advance-payment-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .advance-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }

        .advance-item-label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .advance-item-value {
            font-size: 14px;
            font-weight: 600;
        }

        .advance-item-value.paid {
            color: var(--success-color);
        }

        .advance-item-value.pending {
            color: var(--danger-color);
        }

        /* ============================================
           Footer Section
           ============================================ */
        .invoice-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .footer-notes {
            font-size: 10px;
            color: var(--text-muted);
            margin-bottom: 15px;
            padding: 10px;
            background: var(--bg-light);
            border-radius: 6px;
        }

        .footer-notes strong {
            color: var(--text-dark);
        }

        .thank-you {
            text-align: center;
            font-size: 14px;
            color: var(--primary-color);
            font-weight: 500;
            margin-top: 20px;
        }

        .footer-contact {
            text-align: center;
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 10px;
        }

        /* ============================================
           QR Code Section (Optional)
           ============================================ */
        .qr-section {
            text-align: center;
            margin-top: 20px;
        }

        .qr-code {
            width: 80px;
            height: 80px;
        }

        .qr-label {
            font-size: 9px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        /* ============================================
           Print Styles
           ============================================ */
        @media print {
            body {
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .invoice-container {
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            .invoice-table th {
                background: var(--primary-color) !important;
                color: white !important;
            }

            .status-badge,
            .info-box,
            .hall-section,
            .advance-payment-section {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                margin: 15mm;
                size: A4;
            }
        }

        /* ============================================
           Print Button (Hidden on Print)
           ============================================ */
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        [dir="rtl"] .print-actions {
            right: auto;
            left: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #1e3a8a;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    {{-- Print Actions (Hidden on Print) --}}
    <div class="print-actions no-print">
        <button class="btn btn-primary" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
            </svg>
            {{ __('booking.invoice.print') }}
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ __('booking.invoice.close') }}
        </button>
    </div>

    <div class="invoice-container">
        {{-- Header --}}
        <div class="invoice-header">
            <div class="company-info">
                @if(config('app.logo'))
                    <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}" class="company-logo">
                @endif
                <div class="company-name">{{ config('app.name', 'Majalis') }}</div>
                <div class="company-details">
                    {{ __('booking.invoice.company_tagline') }}<br>
                    {{ config('app.address', 'Muscat, Oman') }}<br>
                    {{ __('booking.invoice.phone') }}: {{ config('app.phone', '+968 XXXX XXXX') }}<br>
                    {{ __('booking.invoice.email') }}: {{ config('app.email', 'info@majalis.om') }}
                </div>
            </div>
            <div class="invoice-title-section">
                <div class="invoice-title">{{ __('booking.invoice.title') }}</div>
                <div class="invoice-number">
                    {{ __('booking.invoice.number') }}: <strong>{{ $booking->booking_number }}</strong>
                </div>
                <div class="invoice-number">
                    {{ __('booking.invoice.date') }}: {{ $booking->created_at->format('d M Y') }}
                </div>

                {{-- Payment Status Badge --}}
                @php
                    $statusClass = match($booking->payment_status) {
                        'paid' => 'status-paid',
                        'pending' => 'status-pending',
                        'refunded' => 'status-cancelled',
                        default => 'status-pending'
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">
                    {{ __('booking.payment_statuses.' . $booking->payment_status) }}
                </span>
            </div>
        </div>

        {{-- Customer & Booking Info Grid --}}
        <div class="info-grid">
            {{-- Customer Information --}}
            <div class="info-box">
                <div class="info-box-title">{{ __('booking.invoice.bill_to') }}</div>
                <div class="info-row">
                    <span class="info-label">{{ __('booking.invoice.customer_name') }}:</span>
                    <span class="info-value">{{ $booking->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('booking.invoice.phone') }}:</span>
                    <span class="info-value">{{ $booking->customer_phone }}</span>
                </div>
                @if($booking->customer_email)
                <div class="info-row">
                    <span class="info-label">{{ __('booking.invoice.email') }}:</span>
                    <span class="info-value">{{ $booking->customer_email }}</span>
                </div>
                @endif
                @if($booking->user)
                <div class="info-row">
                    <span class="info-label">{{ __('booking.invoice.account') }}:</span>
                    <span class="info-value">{{ $booking->user->name }}</span>
                </div>
                @endif
            </div>

            {{-- Booking Information --}}
            <div class="info-box">
                <div class="info-box-title">{{ __('booking.invoice.booking_details') }}</div>
                <div class="info-row">
                    <span class="info-label">{{ __('booking.invoice.booking_date') }}:</span>
                    <span class="info-value">{{ $booking->booking_date->format('l, d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('booking.invoice.time_slot') }}:</span>
                    <span class="info-value">{{ __('booking.time_slots.' . $booking->time_slot) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('booking.invoice.guests') }}:</span>
                    <span class="info-value">{{ $booking->number_of_guests }} {{ __('booking.invoice.persons') }}</span>
                </div>
                @if($booking->event_type)
                <div class="info-row">
                    <span class="info-label">{{ __('booking.invoice.event_type') }}:</span>
                    <span class="info-value">{{ __('booking.event_types.' . $booking->event_type) }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Hall Information --}}
        <div class="hall-section">
            @php
                $hallName = is_array($booking->hall->name)
                    ? ($booking->hall->name[app()->getLocale()] ?? $booking->hall->name['en'] ?? 'N/A')
                    : $booking->hall->name;

                $cityName = is_array($booking->hall->city->name ?? null)
                    ? ($booking->hall->city->name[app()->getLocale()] ?? $booking->hall->city->name['en'] ?? '')
                    : ($booking->hall->city->name ?? '');
            @endphp
            <div class="hall-name">{{ $hallName }}</div>
            <div class="hall-details">
                <span>📍 {{ $cityName }}</span>
                <span>👥 {{ __('booking.invoice.capacity') }}: {{ $booking->hall->capacity_min }} - {{ $booking->hall->capacity_max }}</span>
                @if($booking->hall->owner)
                    <span>🏢 {{ $booking->hall->owner->name }}</span>
                @endif
            </div>
        </div>

        {{-- Services Table --}}
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 50%">{{ __('booking.invoice.description') }}</th>
                    <th class="text-center" style="width: 15%">{{ __('booking.invoice.quantity') }}</th>
                    <th class="text-right" style="width: 17.5%">{{ __('booking.invoice.unit_price') }}</th>
                    <th class="text-right" style="width: 17.5%">{{ __('booking.invoice.total') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- Hall Rental --}}
                <tr>
                    <td>
                        <strong>{{ __('booking.invoice.hall_rental') }}</strong><br>
                        <small style="color: var(--text-muted);">
                            {{ $hallName }} - {{ __('booking.time_slots.' . $booking->time_slot) }}
                        </small>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format((float)$booking->hall_price, 3) }} {{ __('booking.invoice.currency') }}</td>
                    <td class="text-right">{{ number_format((float)$booking->hall_price, 3) }} {{ __('booking.invoice.currency') }}</td>
                </tr>

                {{-- Extra Services --}}
                @forelse($booking->extraServices as $service)
                    @php
                        $serviceName = is_array($service->name)
                            ? ($service->name[app()->getLocale()] ?? $service->name['en'] ?? 'Service')
                            : $service->name;
                    @endphp
                    <tr>
                        <td>{{ $serviceName }}</td>
                        <td class="text-center">{{ $service->pivot->quantity }}</td>
                        <td class="text-right">{{ number_format((float)$service->pivot->unit_price, 3) }} {{ __('booking.invoice.currency') }}</td>
                        <td class="text-right">{{ number_format((float)$service->pivot->total_price, 3) }} {{ __('booking.invoice.currency') }}</td>
                    </tr>
                @empty
                    {{-- No extra services --}}
                @endforelse
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            <div class="totals-box">
                <div class="total-row">
                    <span>{{ __('booking.invoice.hall_price') }}</span>
                    <span>{{ number_format((float)$booking->hall_price, 3) }} {{ __('booking.invoice.currency') }}</span>
                </div>
                @if((float)$booking->services_price > 0)
                <div class="total-row">
                    <span>{{ __('booking.invoice.services_total') }}</span>
                    <span>{{ number_format((float)$booking->services_price, 3) }} {{ __('booking.invoice.currency') }}</span>
                </div>
                @endif
                <div class="total-row subtotal">
                    <span>{{ __('booking.invoice.subtotal') }}</span>
                    <span>{{ number_format((float)$booking->subtotal, 3) }} {{ __('booking.invoice.currency') }}</span>
                </div>
                @if((float)$booking->platform_fee > 0)
                <div class="total-row">
                    <span>{{ __('booking.invoice.platform_fee') }}</span>
                    <span>{{ number_format((float)$booking->platform_fee, 3) }} {{ __('booking.invoice.currency') }}</span>
                </div>
                @endif
                <div class="total-row grand-total">
                    <span>{{ __('booking.invoice.grand_total') }}</span>
                    <span>{{ number_format((float)$booking->total_amount, 3) }} {{ __('booking.invoice.currency') }}</span>
                </div>
            </div>
        </div>

        {{-- Advance Payment Details (if applicable) --}}
        @if($booking->isAdvancePayment())
        <div class="advance-payment-section">
            <div class="advance-payment-title">
                ⚡ {{ __('booking.invoice.advance_payment_details') }}
            </div>
            <div class="advance-payment-grid">
                <div class="advance-item">
                    <div class="advance-item-label">{{ __('booking.invoice.advance_paid') }}</div>
                    <div class="advance-item-value paid">
                        {{ number_format((float)$booking->advance_amount, 3) }} {{ __('booking.invoice.currency') }}
                    </div>
                </div>
                <div class="advance-item">
                    <div class="advance-item-label">{{ __('booking.invoice.balance_due') }}</div>
                    <div class="advance-item-value {{ $booking->balance_paid_at ? 'paid' : 'pending' }}">
                        {{ number_format((float)$booking->balance_due, 3) }} {{ __('booking.invoice.currency') }}
                    </div>
                </div>
                <div class="advance-item">
                    <div class="advance-item-label">{{ __('booking.invoice.balance_status') }}</div>
                    <div class="advance-item-value {{ $booking->balance_paid_at ? 'paid' : 'pending' }}">
                        @if($booking->balance_paid_at)
                            ✓ {{ __('booking.invoice.paid_on') }} {{ $booking->balance_paid_at->format('d M Y') }}
                        @else
                            ⏳ {{ __('booking.invoice.pending_payment') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="invoice-footer">
            {{-- Notes --}}
            @if($booking->customer_notes || $booking->admin_notes)
            <div class="footer-notes">
                @if($booking->customer_notes)
                    <strong>{{ __('booking.invoice.customer_notes') }}:</strong> {{ $booking->customer_notes }}<br>
                @endif
            </div>
            @endif

            {{-- Terms --}}
            <div class="footer-notes">
                <strong>{{ __('booking.invoice.terms_title') }}:</strong><br>
                • {{ __('booking.invoice.terms_1') }}<br>
                • {{ __('booking.invoice.terms_2') }}<br>
                • {{ __('booking.invoice.terms_3') }}
            </div>

            <div class="thank-you">
                {{ __('booking.invoice.thank_you') }}
            </div>

            <div class="footer-contact">
                {{ config('app.name', 'Majalis') }} | {{ config('app.url') }} | {{ config('app.phone', '+968 XXXX XXXX') }}
            </div>
        </div>
    </div>

    {{-- Auto-print Script --}}
    <script>
        // Auto-trigger print dialog after page loads
        window.onload = function() {
            // Small delay to ensure styles are applied
            setTimeout(function() {
                // Uncomment the line below to auto-print on page load
                // window.print();
            }, 500);
        };

        // Handle print completion (close window after print)
        window.onafterprint = function() {
            // Optional: close window after printing
            // window.close();
        };
    </script>
</body>
</html>
