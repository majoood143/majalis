<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('pdf.earnings_report.title') }}</title>
    <style>
        /* ==============================================================
         * Majalis - Owner Earnings Report PDF Template
         * ==============================================================
         * This template generates PDF reports for hall owner earnings.
         * Supports Arabic (RTL) and English (LTR) layouts.
         * Uses DomPDF for rendering.
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
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }
        
        /* Page Layout */
        .page {
            padding: 20px 30px;
        }
        
        /* Header Section */
        .header {
            border-bottom: 3px solid #0d9488;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .logo-section {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
        }
        
        .logo {
            max-width: 120px;
            height: auto;
        }
        
        .company-info {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #0d9488;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            font-size: 10px;
            color: #6b7280;
        }
        
        /* Report Title */
        .report-title {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            border-radius: 8px;
        }
        
        .report-title h1 {
            font-size: 20px;
            color: #0f766e;
            margin-bottom: 5px;
        }
        
        .report-title .period {
            font-size: 12px;
            color: #115e59;
        }
        
        /* Owner Information Box */
        .owner-info {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .owner-info-grid {
            display: table;
            width: 100%;
        }
        
        .owner-info-item {
            display: table-cell;
            width: 50%;
            padding: 5px 10px;
        }
        
        .owner-info-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .owner-info-value {
            font-size: 12px;
            color: #111827;
            font-weight: 600;
        }
        
        /* Summary Cards */
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 5px;
        }
        
        .summary-card-inner {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }
        
        .summary-card-inner.highlight {
            background: #0d9488;
            border-color: #0d9488;
        }
        
        .summary-card-inner.highlight .summary-value,
        .summary-card-inner.highlight .summary-label {
            color: #fff;
        }
        
        .summary-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }
        
        .summary-value.positive {
            color: #059669;
        }
        
        .summary-value.negative {
            color: #dc2626;
        }
        
        /* Tables */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 10px;
            padding-bottom: 5px;
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
            padding: 10px 8px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        tbody td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        tbody tr:hover {
            background: #f0fdfa;
        }
        
        .text-right {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 600;
        }
        
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-completed { background: #dbeafe; color: #1e40af; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        
        /* Totals Row */
        .totals-row {
            background: #f0fdfa !important;
            font-weight: bold;
        }
        
        .totals-row td {
            border-top: 2px solid #0d9488;
            padding-top: 12px;
            padding-bottom: 12px;
        }
        
        /* Financial Breakdown */
        .breakdown-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .breakdown-item {
            display: table;
            width: 100%;
            padding: 8px 0;
            border-bottom: 1px dotted #d1d5db;
        }
        
        .breakdown-item:last-child {
            border-bottom: none;
        }
        
        .breakdown-label {
            display: table-cell;
            width: 70%;
            color: #374151;
        }
        
        .breakdown-value {
            display: table-cell;
            width: 30%;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            font-weight: 600;
            color: #111827;
        }
        
        .breakdown-item.total {
            background: #f0fdfa;
            margin: 10px -15px -15px;
            padding: 12px 15px;
            border-radius: 0 0 6px 6px;
        }
        
        .breakdown-item.total .breakdown-label,
        .breakdown-item.total .breakdown-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f766e;
        }
        
        /* Hall Performance Section */
        .hall-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .hall-card {
            display: table-cell;
            width: 50%;
            padding: 5px;
        }
        
        .hall-card-inner {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
        }
        
        .hall-name {
            font-weight: bold;
            color: #111827;
            margin-bottom: 8px;
            font-size: 12px;
        }
        
        .hall-stats {
            font-size: 10px;
            color: #6b7280;
        }
        
        .hall-stats span {
            display: inline-block;
            margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 10px;
        }
        
        /* Notes Section */
        .notes-section {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
        }
        
        .notes-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .notes-content {
            font-size: 10px;
            color: #78350f;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 30px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        
        .footer-content {
            display: table;
            width: 100%;
        }
        
        .footer-left {
            display: table-cell;
            width: 50%;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 9px;
            color: #6b7280;
        }
        
        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            font-size: 9px;
            color: #6b7280;
        }
        
        /* Page Break */
        .page-break {
            page-break-after: always;
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
            <div class="header-content">
                <div class="logo-section">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" alt="Majalis" class="logo">
                    @else
                        <div style="font-size: 24px; font-weight: bold; color: #0d9488;">مجالس</div>
                    @endif
                </div>
                <div class="company-info">
                    <div class="company-name">{{ __('pdf.company.name') }}</div>
                    <div class="company-subtitle">{{ __('pdf.company.tagline') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Report Title -->
        <div class="report-title">
            <h1>{{ __('pdf.earnings_report.title') }}</h1>
            <div class="period">
                {{ __('pdf.earnings_report.period') }}: 
                {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
            </div>
        </div>
        
        <!-- Owner Information -->
        <div class="owner-info">
            <div class="owner-info-grid">
                <div class="owner-info-item">
                    <div class="owner-info-label">{{ __('pdf.earnings_report.owner_name') }}</div>
                    <div class="owner-info-value">{{ $owner->name }}</div>
                </div>
                <div class="owner-info-item">
                    <div class="owner-info-label">{{ __('pdf.earnings_report.report_date') }}</div>
                    <div class="owner-info-value">{{ now()->format('d M Y, H:i') }}</div>
                </div>
            </div>
            <div class="owner-info-grid" style="margin-top: 5px;">
                <div class="owner-info-item">
                    <div class="owner-info-label">{{ __('pdf.earnings_report.email') }}</div>
                    <div class="owner-info-value">{{ $owner->email }}</div>
                </div>
                <div class="owner-info-item">
                    <div class="owner-info-label">{{ __('pdf.earnings_report.report_number') }}</div>
                    <div class="owner-info-value">{{ $reportNumber }}</div>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-card-inner highlight">
                    <div class="summary-label">{{ __('pdf.earnings_report.total_earnings') }}</div>
                    <div class="summary-value currency">{{ number_format($summary['net_earnings'], 3) }} {{ __('currency.omr') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-inner">
                    <div class="summary-label">{{ __('pdf.earnings_report.gross_revenue') }}</div>
                    <div class="summary-value currency">{{ number_format($summary['gross_revenue'], 3) }} {{ __('currency.omr') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-inner">
                    <div class="summary-label">{{ __('pdf.earnings_report.commission') }}</div>
                    <div class="summary-value currency negative">-{{ number_format($summary['total_commission'], 3) }} {{ __('currency.omr') }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-inner">
                    <div class="summary-label">{{ __('pdf.earnings_report.total_bookings') }}</div>
                    <div class="summary-value">{{ $summary['total_bookings'] }}</div>
                </div>
            </div>
        </div>
        
        <!-- Financial Breakdown -->
        @if($includeBreakdown ?? true)
        <div class="section-title">{{ __('pdf.earnings_report.financial_breakdown') }}</div>
        <div class="breakdown-box">
            <div class="breakdown-item">
                <span class="breakdown-label">{{ __('pdf.earnings_report.hall_rental_income') }}</span>
                <span class="breakdown-value currency">{{ number_format($summary['hall_revenue'], 3) }} {{ __('currency.omr') }}</span>
            </div>
            <div class="breakdown-item">
                <span class="breakdown-label">{{ __('pdf.earnings_report.services_income') }}</span>
                <span class="breakdown-value currency">{{ number_format($summary['services_revenue'], 3) }} {{ __('currency.omr') }}</span>
            </div>
            <div class="breakdown-item">
                <span class="breakdown-label">{{ __('pdf.earnings_report.gross_total') }}</span>
                <span class="breakdown-value currency">{{ number_format($summary['gross_revenue'], 3) }} {{ __('currency.omr') }}</span>
            </div>
            <div class="breakdown-item">
                <span class="breakdown-label">{{ __('pdf.earnings_report.platform_commission') }} ({{ $commissionRate ?? 10 }}%)</span>
                <span class="breakdown-value currency" style="color: #dc2626;">-{{ number_format($summary['total_commission'], 3) }} {{ __('currency.omr') }}</span>
            </div>
            <div class="breakdown-item total">
                <span class="breakdown-label">{{ __('pdf.earnings_report.net_earnings') }}</span>
                <span class="breakdown-value currency">{{ number_format($summary['net_earnings'], 3) }} {{ __('currency.omr') }}</span>
            </div>
        </div>
        @endif
        
        <!-- Earnings Details Table -->
        @if($includeDetails ?? true)
        <div class="section-title">{{ __('pdf.earnings_report.earnings_details') }}</div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('pdf.earnings_report.col_booking') }}</th>
                    <th>{{ __('pdf.earnings_report.col_date') }}</th>
                    <th>{{ __('pdf.earnings_report.col_hall') }}</th>
                    <th>{{ __('pdf.earnings_report.col_slot') }}</th>
                    <th class="text-right">{{ __('pdf.earnings_report.col_hall_price') }}</th>
                    <th class="text-right">{{ __('pdf.earnings_report.col_services') }}</th>
                    <th class="text-right">{{ __('pdf.earnings_report.col_commission') }}</th>
                    <th class="text-right">{{ __('pdf.earnings_report.col_net') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_number }}</td>
                    <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                    <td>{{ $booking->hall->name }}</td>
                    <td class="text-center">{{ __('slots.' . $booking->time_slot) }}</td>
                    <td class="text-right currency">{{ number_format((float) $booking->hall_price, 3) }}</td>
                    <td class="text-right currency">{{ number_format((float) $booking->services_price, 3) }}</td>
                    <td class="text-right currency" style="color: #dc2626;">-{{ number_format((float) $booking->commission_amount, 3) }}</td>
                    <td class="text-right currency" style="font-weight: bold;">{{ number_format((float) $booking->owner_payout, 3) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">{{ __('pdf.earnings_report.no_bookings') }}</td>
                </tr>
                @endforelse
                
                @if($bookings->count() > 0)
                <tr class="totals-row">
                    <td colspan="4"><strong>{{ __('pdf.earnings_report.totals') }}</strong></td>
                    <td class="text-right currency">{{ number_format($summary['hall_revenue'], 3) }}</td>
                    <td class="text-right currency">{{ number_format($summary['services_revenue'], 3) }}</td>
                    <td class="text-right currency" style="color: #dc2626;">-{{ number_format($summary['total_commission'], 3) }}</td>
                    <td class="text-right currency" style="color: #0f766e;">{{ number_format($summary['net_earnings'], 3) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
        
        <!-- Hall Performance (if multiple halls) -->
        @if(isset($hallBreakdown) && count($hallBreakdown) > 1)
        <div class="section-title">{{ __('pdf.earnings_report.hall_performance') }}</div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('pdf.earnings_report.col_hall') }}</th>
                    <th class="text-center">{{ __('pdf.earnings_report.col_bookings_count') }}</th>
                    <th class="text-right">{{ __('pdf.earnings_report.col_hall_revenue') }}</th>
                    <th class="text-right">{{ __('pdf.earnings_report.col_services_revenue') }}</th>
                    <th class="text-right">{{ __('pdf.earnings_report.col_total_revenue') }}</th>
                    <th class="text-right">{{ __('pdf.earnings_report.col_net_earnings') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hallBreakdown as $hall)
                <tr>
                    <td>{{ $hall['name'] }}</td>
                    <td class="text-center">{{ $hall['bookings_count'] }}</td>
                    <td class="text-right currency">{{ number_format($hall['hall_revenue'], 3) }}</td>
                    <td class="text-right currency">{{ number_format($hall['services_revenue'], 3) }}</td>
                    <td class="text-right currency">{{ number_format($hall['total_revenue'], 3) }}</td>
                    <td class="text-right currency" style="font-weight: bold;">{{ number_format($hall['net_earnings'], 3) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        
        <!-- Notes -->
        @if(isset($notes) && $notes)
        <div class="notes-section">
            <div class="notes-title">{{ __('pdf.earnings_report.notes') }}</div>
            <div class="notes-content">{{ $notes }}</div>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-left">
                    {{ __('pdf.footer.generated') }}: {{ now()->format('d/m/Y H:i:s') }}
                    <br>
                    {{ __('pdf.footer.system') }}: {{ config('app.name') }}
                </div>
                <div class="footer-right">
                    {{ __('pdf.footer.page') }} <span class="pagenum"></span>
                    <br>
                    {{ __('pdf.footer.confidential') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
