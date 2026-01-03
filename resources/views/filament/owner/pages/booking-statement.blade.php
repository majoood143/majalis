<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('pdf.booking_statement.title') }} - {{ $booking->booking_number }}</title>
    <style>
        /* ==============================================================
         * Majalis - Booking Statement PDF Template
         * ==============================================================
         * This template generates individual booking statements for owners.
         * Shows detailed booking information, services, and financial breakdown.
         * Supports Arabic (RTL) and English (LTR) layouts.
         * ============================================================== */
        
        /* Base Fonts */
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path("fonts/DejaVuSans.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #1f2937;
            background: #fff;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        
        /* Page Layout */
        .page {
            padding: 25px 35px;
            max-width: 100%;
        }
        
        /* Header Section with Logo */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0d9488;
        }
        
        .header-left {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
        }
        
        .header-right {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
        
        .logo {
            max-width: 100px;
            height: auto;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #0d9488;
        }
        
        .company-tagline {
            font-size: 10px;
            color: #6b7280;
        }
        
        /* Document Title */
        .document-title {
            text-align: center;
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
        }
        
        .document-title h1 {
            font-size: 22px;
            color: #0f766e;
            margin-bottom: 8px;
        }
        
        .document-title .booking-number {
            font-size: 16px;
            color: #115e59;
            font-weight: 600;
        }
        
        .document-title .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 8px;
        }
        
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-completed { background: #dbeafe; color: #1e40af; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        
        /* Info Grid Layout */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .info-box {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }
        
        .info-box-inner {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            height: 100%;
        }
        
        .info-box-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #99f6e4;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .info-label {
            display: table-cell;
            width: 40%;
            font-size: 10px;
            color: #6b7280;
            padding: 3px 0;
        }
        
        .info-value {
            display: table-cell;
            width: 60%;
            font-size: 11px;
            color: #111827;
            font-weight: 500;
            padding: 3px 0;
        }
        
        /* Hall Details Section */
        .hall-section {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .hall-header {
            background: #0d9488;
            color: #fff;
            padding: 12px 15px;
        }
        
        .hall-header h3 {
            font-size: 14px;
            margin: 0;
        }
        
        .hall-body {
            padding: 15px;
        }
        
        .hall-details {
            display: table;
            width: 100%;
        }
        
        .hall-detail-item {
            display: table-cell;
            width: 33.33%;
            padding: 8px;
            text-align: center;
        }
        
        .hall-detail-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .hall-detail-value {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
        }
        
        /* Services Table */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #99f6e4;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        thead th {
            background: #0d9488;
            color: #fff;
            padding: 10px 12px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .text-right {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Financial Summary Box */
        .financial-summary {
            background: #fff;
            border: 2px solid #0d9488;
            border-radius: 8px;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .financial-header {
            background: #0d9488;
            color: #fff;
            padding: 12px 15px;
        }
        
        .financial-header h3 {
            font-size: 14px;
            margin: 0;
        }
        
        .financial-body {
            padding: 15px;
        }
        
        .financial-row {
            display: table;
            width: 100%;
            padding: 10px 0;
            border-bottom: 1px dotted #d1d5db;
        }
        
        .financial-row:last-child {
            border-bottom: none;
        }
        
        .financial-label {
            display: table-cell;
            width: 60%;
            font-size: 11px;
            color: #374151;
        }
        
        .financial-value {
            display: table-cell;
            width: 40%;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }
        
        .financial-row.subtotal {
            background: #f0fdfa;
            margin: 0 -15px;
            padding: 10px 15px;
        }
        
        .financial-row.deduction .financial-value {
            color: #dc2626;
        }
        
        .financial-row.total {
            background: #0d9488;
            color: #fff;
            margin: 15px -15px -15px;
            padding: 15px;
        }
        
        .financial-row.total .financial-label,
        .financial-row.total .financial-value {
            color: #fff;
            font-size: 14px;
            font-weight: bold;
        }
        
        /* Payment Details */
        .payment-info {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .payment-info-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        .payment-grid {
            display: table;
            width: 100%;
        }
        
        .payment-item {
            display: table-cell;
            width: 33.33%;
            padding: 5px;
        }
        
        .payment-label {
            font-size: 9px;
            color: #78350f;
            text-transform: uppercase;
        }
        
        .payment-value {
            font-size: 11px;
            color: #451a03;
            font-weight: 600;
        }
        
        /* Customer Notes */
        .notes-box {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .notes-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .notes-content {
            font-size: 10px;
            color: #4b5563;
            font-style: italic;
        }
        
        /* Timestamps Section */
        .timestamps {
            background: #f9fafb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
        }
        
        .timestamps-grid {
            display: table;
            width: 100%;
        }
        
        .timestamp-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 5px;
        }
        
        .timestamp-label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 2px;
        }
        
        .timestamp-value {
            font-size: 10px;
            color: #374151;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 35px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        
        .footer-content {
            display: table;
            width: 100%;
            font-size: 9px;
            color: #6b7280;
        }
        
        .footer-left {
            display: table-cell;
            width: 50%;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        
        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
        
        /* Currency Format */
        .currency {
            font-family: 'DejaVu Sans', monospace;
            white-space: nowrap;
        }
        
        /* Print Optimization */
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="Majalis" class="logo">
                @else
                    <div style="font-size: 28px; font-weight: bold; color: #0d9488;">مجالس</div>
                @endif
            </div>
            <div class="header-right">
                <div class="company-name">{{ __('pdf.company.name') }}</div>
                <div class="company-tagline">{{ __('pdf.company.tagline') }}</div>
            </div>
        </div>
        
        <!-- Document Title -->
        <div class="document-title">
            <h1>{{ __('pdf.booking_statement.title') }}</h1>
            <div class="booking-number">{{ $booking->booking_number }}</div>
            <div class="status-badge status-{{ $booking->status }}">
                {{ __('status.booking.' . $booking->status) }}
            </div>
        </div>
        
        <!-- Booking & Customer Information -->
        <div class="info-grid">
            <div class="info-box">
                <div class="info-box-inner">
                    <div class="info-box-title">{{ __('pdf.booking_statement.booking_info') }}</div>
                    
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.booking_number') }}</span>
                        <span class="info-value">{{ $booking->booking_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.booking_date') }}</span>
                        <span class="info-value">{{ $booking->booking_date->format('d M Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.time_slot') }}</span>
                        <span class="info-value">{{ __('slots.' . $booking->time_slot) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.booking_status') }}</span>
                        <span class="info-value">{{ __('status.booking.' . $booking->status) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.payment_status') }}</span>
                        <span class="info-value">{{ __('status.payment.' . $booking->payment_status) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="info-box">
                <div class="info-box-inner">
                    <div class="info-box-title">{{ __('pdf.booking_statement.customer_info') }}</div>
                    
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.customer_name') }}</span>
                        <span class="info-value">{{ $booking->customer_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.customer_phone') }}</span>
                        <span class="info-value" dir="ltr">{{ $booking->customer_phone }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.customer_email') }}</span>
                        <span class="info-value">{{ $booking->customer_email ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.guests_count') }}</span>
                        <span class="info-value">{{ $booking->guests_count }} {{ __('pdf.booking_statement.guests') }}</span>
                    </div>
                    @if($booking->event_type)
                    <div class="info-row">
                        <span class="info-label">{{ __('pdf.booking_statement.event_type') }}</span>
                        <span class="info-value">{{ __('event_types.' . $booking->event_type) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Hall Details -->
        <div class="hall-section">
            <div class="hall-header">
                <h3>{{ __('pdf.booking_statement.hall_details') }}</h3>
            </div>
            <div class="hall-body">
                <div style="font-size: 16px; font-weight: bold; color: #111827; margin-bottom: 15px;">
                    {{ $booking->hall->name }}
                </div>
                <div class="hall-details">
                    <div class="hall-detail-item">
                        <div class="hall-detail-label">{{ __('pdf.booking_statement.location') }}</div>
                        <div class="hall-detail-value">{{ $booking->hall->city }}</div>
                    </div>
                    <div class="hall-detail-item">
                        <div class="hall-detail-label">{{ __('pdf.booking_statement.capacity') }}</div>
                        <div class="hall-detail-value">{{ $booking->hall->capacity }} {{ __('pdf.booking_statement.persons') }}</div>
                    </div>
                    <div class="hall-detail-item">
                        <div class="hall-detail-label">{{ __('pdf.booking_statement.hall_type') }}</div>
                        <div class="hall-detail-value">{{ __('hall_types.' . $booking->hall->hall_type) }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Extra Services -->
        @if($booking->services && count($booking->services) > 0)
        <div class="section-title">{{ __('pdf.booking_statement.extra_services') }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">{{ __('pdf.booking_statement.service_name') }}</th>
                    <th class="text-center">{{ __('pdf.booking_statement.quantity') }}</th>
                    <th class="text-right">{{ __('pdf.booking_statement.unit_price') }}</th>
                    <th class="text-right">{{ __('pdf.booking_statement.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->services as $service)
                <tr>
                    <td>{{ $service['name'] ?? $service->name ?? '-' }}</td>
                    <td class="text-center">{{ $service['quantity'] ?? $service->pivot->quantity ?? 1 }}</td>
                    <td class="text-right currency">{{ number_format((float)($service['price'] ?? $service->price ?? 0), 3) }} {{ __('currency.omr') }}</td>
                    <td class="text-right currency">{{ number_format((float)($service['total'] ?? ($service['price'] ?? 0) * ($service['quantity'] ?? 1)), 3) }} {{ __('currency.omr') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        
        <!-- Financial Summary -->
        <div class="financial-summary">
            <div class="financial-header">
                <h3>{{ __('pdf.booking_statement.financial_summary') }}</h3>
            </div>
            <div class="financial-body">
                <div class="financial-row">
                    <span class="financial-label">{{ __('pdf.booking_statement.hall_rental') }}</span>
                    <span class="financial-value currency">{{ number_format((float) $booking->hall_price, 3) }} {{ __('currency.omr') }}</span>
                </div>
                
                @if((float) $booking->services_price > 0)
                <div class="financial-row">
                    <span class="financial-label">{{ __('pdf.booking_statement.services_total') }}</span>
                    <span class="financial-value currency">{{ number_format((float) $booking->services_price, 3) }} {{ __('currency.omr') }}</span>
                </div>
                @endif
                
                <div class="financial-row subtotal">
                    <span class="financial-label">{{ __('pdf.booking_statement.gross_total') }}</span>
                    <span class="financial-value currency">{{ number_format((float) $booking->total_amount, 3) }} {{ __('currency.omr') }}</span>
                </div>
                
                <div class="financial-row deduction">
                    <span class="financial-label">{{ __('pdf.booking_statement.platform_commission') }} ({{ $booking->commission_rate ?? 10 }}%)</span>
                    <span class="financial-value currency">-{{ number_format((float) $booking->commission_amount, 3) }} {{ __('currency.omr') }}</span>
                </div>
                
                <div class="financial-row total">
                    <span class="financial-label">{{ __('pdf.booking_statement.your_earnings') }}</span>
                    <span class="financial-value currency">{{ number_format((float) $booking->owner_payout, 3) }} {{ __('currency.omr') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        @if($booking->payment_status === 'paid')
        <div class="payment-info">
            <div class="payment-info-title">{{ __('pdf.booking_statement.payment_details') }}</div>
            <div class="payment-grid">
                <div class="payment-item">
                    <div class="payment-label">{{ __('pdf.booking_statement.payment_method') }}</div>
                    <div class="payment-value">{{ __('payment_methods.' . ($booking->payment_method ?? 'online')) }}</div>
                </div>
                <div class="payment-item">
                    <div class="payment-label">{{ __('pdf.booking_statement.paid_amount') }}</div>
                    <div class="payment-value currency">{{ number_format((float) $booking->paid_amount, 3) }} {{ __('currency.omr') }}</div>
                </div>
                <div class="payment-item">
                    <div class="payment-label">{{ __('pdf.booking_statement.payment_date') }}</div>
                    <div class="payment-value">{{ $booking->paid_at ? $booking->paid_at->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Customer Notes -->
        @if($booking->notes)
        <div class="notes-box">
            <div class="notes-title">{{ __('pdf.booking_statement.customer_notes') }}</div>
            <div class="notes-content">{{ $booking->notes }}</div>
        </div>
        @endif
        
        <!-- Timestamps -->
        <div class="timestamps">
            <div class="timestamps-grid">
                <div class="timestamp-item">
                    <div class="timestamp-label">{{ __('pdf.booking_statement.created_at') }}</div>
                    <div class="timestamp-value">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="timestamp-item">
                    <div class="timestamp-label">{{ __('pdf.booking_statement.updated_at') }}</div>
                    <div class="timestamp-value">{{ $booking->updated_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="timestamp-item">
                    <div class="timestamp-label">{{ __('pdf.booking_statement.statement_generated') }}</div>
                    <div class="timestamp-value">{{ now()->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-left">
                    {{ __('pdf.footer.generated') }}: {{ now()->format('d/m/Y H:i:s') }}
                    <br>
                    {{ __('pdf.footer.system') }}: {{ config('app.name') }}
                </div>
                <div class="footer-right">
                    {{ __('pdf.footer.document_id') }}: {{ $booking->booking_number }}
                    <br>
                    {{ __('pdf.footer.confidential') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
