<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {{-- <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp"> --}}
    <title>{{ __('pdf.financial_report.title') }}</title>
    <style>
        /* ==============================================================
         * Majalis - Financial Report PDF Template
         * ==============================================================
         * This template generates comprehensive financial reports for owners.
         * Supports multiple report types: monthly, yearly, hall-based, comparison.
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
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }

        /* Page Layout */
        .page {
            padding: 20px 25px;
        }

        /* Header Section */
        .header {
            display: table;
            width: 100%;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 3px solid #0d9488;
        }

        .header-left {
            display: table-cell;
            width: 35%;
            vertical-align: middle;
        }

        .header-center {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
            text-align: center;
        }

        .header-right {
            display: table-cell;
            width: 35%;
            vertical-align: middle;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
        }

        .logo {
            max-width: 90px;
            height: auto;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #0d9488;
        }

        .report-badge {
            display: inline-block;
            background: #0d9488;
            color: #fff;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header-meta {
            font-size: 9px;
            color: #6b7280;
        }

        /* Report Title Box */
        .report-title-box {
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 5px;
        }

        .report-subtitle {
            font-size: 11px;
            color: #115e59;
        }

        .report-period {
            font-size: 10px;
            color: #134e4a;
            margin-top: 5px;
        }

        /* Owner Info Bar */
        .owner-bar {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px 15px;
            margin-bottom: 15px;
            display: table;
            width: 100%;
        }

        .owner-bar-item {
            display: table-cell;
            width: 25%;
            padding: 0 10px;
        }

        .owner-bar-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .owner-bar-value {
            font-size: 11px;
            color: #111827;
            font-weight: 600;
        }

        /* Summary Cards Grid */
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-card {
            display: table-cell;
            width: 16.66%;
            padding: 4px;
        }

        .summary-card-inner {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            min-height: 65px;
        }

        .summary-card-inner.primary {
            background: #0d9488;
            border-color: #0d9488;
        }

        .summary-card-inner.primary .summary-label,
        .summary-card-inner.primary .summary-value {
            color: #fff;
        }

        .summary-card-inner.success {
            border-color: #10b981;
            border-width: 2px;
        }

        .summary-card-inner.success .summary-value {
            color: #059669;
        }

        .summary-card-inner.danger {
            border-color: #ef4444;
            border-width: 2px;
        }

        .summary-card-inner.danger .summary-value {
            color: #dc2626;
        }

        .summary-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }

        .summary-change {
            font-size: 8px;
            margin-top: 3px;
        }

        .summary-change.positive { color: #059669; }
        .summary-change.negative { color: #dc2626; }

        /* Section Styling */
        .section {
            margin-bottom: 20px;
        }

        .section-header {
            font-size: 13px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #99f6e4;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.compact {
            font-size: 9px;
        }

        thead th {
            background: #0d9488;
            color: #fff;
            padding: 8px 6px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        tbody td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
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

        /* Total Rows */
        .total-row {
            background: #f0fdfa !important;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #0d9488;
            padding-top: 10px;
        }

        .grand-total-row {
            background: #0d9488 !important;
            color: #fff;
        }

        .grand-total-row td {
            color: #fff;
            font-weight: bold;
            font-size: 10px;
        }

        /* Breakdown Box */
        .breakdown-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .breakdown-row {
            display: table;
            width: 100%;
            padding: 6px 0;
            border-bottom: 1px dotted #d1d5db;
        }

        .breakdown-row:last-child {
            border-bottom: none;
        }

        .breakdown-label {
            display: table-cell;
            width: 65%;
            font-size: 10px;
            color: #374151;
        }

        .breakdown-value {
            display: table-cell;
            width: 35%;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            font-size: 10px;
            font-weight: 600;
            color: #111827;
        }

        .breakdown-row.highlight {
            background: #f0fdfa;
            margin: 0 -12px;
            padding: 8px 12px;
        }

        .breakdown-row.highlight .breakdown-label,
        .breakdown-row.highlight .breakdown-value {
            font-weight: bold;
            color: #0f766e;
        }

        /* Two Column Layout */
        .two-col {
            display: table;
            width: 100%;
        }

        .two-col > div {
            display: table-cell;
            width: 50%;
            padding: 0 8px;
            vertical-align: top;
        }

        .two-col > div:first-child {
            padding-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 0;
        }

        .two-col > div:last-child {
            padding-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 0;
        }

        /* Comparison Table Styling */
        .comparison-positive { color: #059669; }
        .comparison-negative { color: #dc2626; }

        .change-arrow {
            font-size: 10px;
        }

        /* Hall Performance Cards */
        .hall-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .hall-card-header {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .hall-card-name {
            display: table-cell;
            width: 60%;
            font-weight: bold;
            color: #111827;
            font-size: 11px;
        }

        .hall-card-earnings {
            display: table-cell;
            width: 40%;
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            font-weight: bold;
            color: #0f766e;
            font-size: 12px;
        }

        .hall-card-stats {
            display: table;
            width: 100%;
            font-size: 9px;
            color: #6b7280;
        }

        .hall-card-stat {
            display: table-cell;
            width: 33.33%;
            text-align: center;
        }

        /* Progress Bar (simple version for PDF) */
        .progress-bar {
            background: #e5e7eb;
            border-radius: 4px;
            height: 8px;
            overflow: hidden;
            margin-top: 5px;
        }

        .progress-bar-fill {
            background: #0d9488;
            height: 100%;
            border-radius: 4px;
        }

        /* Notes Section */
        .notes-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .notes-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .notes-content {
            font-size: 9px;
            color: #78350f;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px 25px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            font-size: 8px;
            color: #6b7280;
        }

        .footer-content {
            display: table;
            width: 100%;
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
            <div class="header-left">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="Majalis" class="logo">
                @else
                    <div style="font-size: 22px; font-weight: bold; color: #0d9488;">مجالس</div>
                @endif
            </div>
            <div class="header-center">
                <span class="report-badge">
                    {{ __('pdf.financial_report.types.' . $reportType) }}
                </span>
            </div>
            <div class="header-right">
                <div class="header-meta">
                    {{ __('pdf.financial_report.generated') }}: {{ now()->format('d/m/Y H:i') }}
                    <br>
                    {{ __('pdf.financial_report.report_id') }}: {{ $reportId ?? 'FR-' . now()->format('YmdHis') }}
                </div>
            </div>
        </div>

        <!-- Report Title -->
        <div class="report-title-box">
            <div class="report-title">{{ __('pdf.financial_report.title') }}</div>
            <div class="report-subtitle">{{ $owner->name }}</div>
            <div class="report-period">
                @if($reportType === 'monthly')
                    {{ __('pdf.financial_report.period_monthly', ['month' => __('months.' . $month), 'year' => $year]) }}
                @elseif($reportType === 'yearly')
                    {{ __('pdf.financial_report.period_yearly', ['year' => $year]) }}
                @elseif($reportType === 'comparison')
                    {{ __('pdf.financial_report.period_comparison', ['current' => __('months.' . $currentMonth), 'previous' => __('months.' . $previousMonth), 'year' => $year]) }}
                @else
                    {{ __('pdf.financial_report.period_custom', ['start' => $startDate->format('d M Y'), 'end' => $endDate->format('d M Y')]) }}
                @endif
            </div>
        </div>

        <!-- Owner Info Bar -->
        <div class="owner-bar">
            <div class="owner-bar-item">
                <div class="owner-bar-label">{{ __('pdf.financial_report.owner') }}</div>
                <div class="owner-bar-value">{{ $owner->name }}</div>
            </div>
            <div class="owner-bar-item">
                <div class="owner-bar-label">{{ __('pdf.financial_report.total_halls') }}</div>
                <div class="owner-bar-value">{{ $totalHalls ?? 0 }}</div>
            </div>
            <div class="owner-bar-item">
                <div class="owner-bar-label">{{ __('pdf.financial_report.active_halls') }}</div>
                <div class="owner-bar-value">{{ $activeHalls ?? 0 }}</div>
            </div>
            <div class="owner-bar-item">
                <div class="owner-bar-label">{{ __('pdf.financial_report.member_since') }}</div>
                <div class="owner-bar-value">{{ $owner->created_at->format('M Y') }}</div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-card-inner primary">
                    <div class="summary-label">{{ __('pdf.financial_report.net_earnings') }}</div>
                    <div class="summary-value currency">{{ number_format($summary['net_earnings'], 3) }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-inner">
                    <div class="summary-label">{{ __('pdf.financial_report.gross_revenue') }}</div>
                    <div class="summary-value currency">{{ number_format($summary['gross_revenue'], 3) }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-inner danger">
                    <div class="summary-label">{{ __('pdf.financial_report.commission') }}</div>
                    <div class="summary-value currency">-{{ number_format($summary['total_commission'], 3) }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-inner">
                    <div class="summary-label">{{ __('pdf.financial_report.total_bookings') }}</div>
                    <div class="summary-value">{{ $summary['total_bookings'] }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-inner">
                    <div class="summary-label">{{ __('pdf.financial_report.avg_per_booking') }}</div>
                    <div class="summary-value currency">{{ number_format($summary['avg_per_booking'] ?? 0, 3) }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-inner success">
                    <div class="summary-label">{{ __('pdf.financial_report.occupancy_rate') }}</div>
                    <div class="summary-value">{{ number_format($summary['occupancy_rate'] ?? 0, 1) }}%</div>
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- MONTHLY REPORT CONTENT --}}
        {{-- ============================================================== --}}
        @if($reportType === 'monthly')

        <div class="two-col">
            <div>
                <!-- Daily Breakdown -->
                <div class="section">
                    <div class="section-header">{{ __('pdf.financial_report.daily_breakdown') }}</div>
                    <table class="compact">
                        <thead>
                            <tr>
                                <th>{{ __('pdf.financial_report.date') }}</th>
                                <th class="text-center">{{ __('pdf.financial_report.bookings') }}</th>
                                <th class="text-right">{{ __('pdf.financial_report.gross') }}</th>
                                <th class="text-right">{{ __('pdf.financial_report.net') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyData as $day)
                            <tr>
                                <td>{{ $day['date'] }}</td>
                                <td class="text-center">{{ $day['bookings_count'] }}</td>
                                <td class="text-right currency">{{ number_format($day['gross_revenue'], 3) }}</td>
                                <td class="text-right currency">{{ number_format($day['net_earnings'], 3) }}</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>{{ __('pdf.financial_report.total') }}</strong></td>
                                <td class="text-center">{{ $summary['total_bookings'] }}</td>
                                <td class="text-right currency">{{ number_format($summary['gross_revenue'], 3) }}</td>
                                <td class="text-right currency">{{ number_format($summary['net_earnings'], 3) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <!-- Financial Breakdown -->
                <div class="section">
                    <div class="section-header">{{ __('pdf.financial_report.financial_breakdown') }}</div>
                    <div class="breakdown-box">
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.hall_revenue') }}</span>
                            <span class="breakdown-value currency">{{ number_format($summary['hall_revenue'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.services_revenue') }}</span>
                            <span class="breakdown-value currency">{{ number_format($summary['services_revenue'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                        <div class="breakdown-row highlight">
                            <span class="breakdown-label">{{ __('pdf.financial_report.gross_total') }}</span>
                            <span class="breakdown-value currency">{{ number_format($summary['gross_revenue'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.platform_commission') }}</span>
                            <span class="breakdown-value currency" style="color: #dc2626;">-{{ number_format($summary['total_commission'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                        <div class="breakdown-row highlight">
                            <span class="breakdown-label">{{ __('pdf.financial_report.net_total') }}</span>
                            <span class="breakdown-value currency" style="color: #0f766e;">{{ number_format($summary['net_earnings'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Time Slot Breakdown -->
                @if(isset($slotBreakdown) && count($slotBreakdown) > 0)
                <div class="section">
                    <div class="section-header">{{ __('pdf.financial_report.slot_breakdown') }}</div>
                    <table class="compact">
                        <thead>
                            <tr>
                                <th>{{ __('pdf.financial_report.slot') }}</th>
                                <th class="text-center">{{ __('pdf.financial_report.count') }}</th>
                                <th class="text-right">{{ __('pdf.financial_report.revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($slotBreakdown as $slot)
                            <tr>
                                <td>{{ __('slots.' . $slot['slot']) }}</td>
                                <td class="text-center">{{ $slot['count'] }}</td>
                                <td class="text-right currency">{{ number_format($slot['revenue'], 3) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- YEARLY REPORT CONTENT --}}
        {{-- ============================================================== --}}
        @elseif($reportType === 'yearly')

        <!-- Monthly Breakdown Table -->
        <div class="section">
            <div class="section-header">{{ __('pdf.financial_report.monthly_breakdown') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('pdf.financial_report.month') }}</th>
                        <th class="text-center">{{ __('pdf.financial_report.bookings') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.hall_rev') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.services_rev') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.gross') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.commission') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.net') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $month)
                    <tr>
                        <td>{{ __('months.' . $month['month']) }}</td>
                        <td class="text-center">{{ $month['bookings_count'] }}</td>
                        <td class="text-right currency">{{ number_format($month['hall_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($month['services_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($month['gross_revenue'], 3) }}</td>
                        <td class="text-right currency" style="color: #dc2626;">-{{ number_format($month['commission'], 3) }}</td>
                        <td class="text-right currency" style="font-weight: bold;">{{ number_format($month['net_earnings'], 3) }}</td>
                    </tr>
                    @endforeach
                    <tr class="grand-total-row">
                        <td><strong>{{ __('pdf.financial_report.year_total') }}</strong></td>
                        <td class="text-center">{{ $yearTotals['total_bookings'] }}</td>
                        <td class="text-right currency">{{ number_format($yearTotals['hall_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($yearTotals['services_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($yearTotals['gross_revenue'], 3) }}</td>
                        <td class="text-right currency">-{{ number_format($yearTotals['total_commission'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($yearTotals['net_earnings'], 3) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Yearly Statistics -->
        <div class="two-col">
            <div>
                <div class="section">
                    <div class="section-header">{{ __('pdf.financial_report.year_stats') }}</div>
                    <div class="breakdown-box">
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.best_month') }}</span>
                            <span class="breakdown-value">{{ __('months.' . $bestMonth['month']) }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.best_month_earnings') }}</span>
                            <span class="breakdown-value currency">{{ number_format($bestMonth['net_earnings'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.avg_monthly') }}</span>
                            <span class="breakdown-value currency">{{ number_format($avgMonthly, 3) }} {{ __('currency.omr') }}</span>
                        </div>
                        <div class="breakdown-row highlight">
                            <span class="breakdown-label">{{ __('pdf.financial_report.total_year_earnings') }}</span>
                            <span class="breakdown-value currency">{{ number_format($yearTotals['net_earnings'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                @if(isset($payoutSummary))
                <div class="section">
                    <div class="section-header">{{ __('pdf.financial_report.payout_summary') }}</div>
                    <div class="breakdown-box">
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.total_received') }}</span>
                            <span class="breakdown-value currency">{{ number_format($payoutSummary['total_received'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.pending_payout') }}</span>
                            <span class="breakdown-value currency">{{ number_format($payoutSummary['pending'], 3) }} {{ __('currency.omr') }}</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">{{ __('pdf.financial_report.payout_count') }}</span>
                            <span class="breakdown-value">{{ $payoutSummary['payout_count'] }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- HALL REPORT CONTENT --}}
        {{-- ============================================================== --}}
        @elseif($reportType === 'hall')

        <!-- Hall Performance Cards -->
        <div class="section">
            <div class="section-header">{{ __('pdf.financial_report.hall_performance') }}</div>

            @foreach($hallData as $hall)
            <div class="hall-card">
                <div class="hall-card-header">
                    <div class="hall-card-name">{{ $hall['name'] }}</div>
                    <div class="hall-card-earnings currency">{{ number_format($hall['net_earnings'], 3) }} {{ __('currency.omr') }}</div>
                </div>
                <div class="hall-card-stats">
                    <div class="hall-card-stat">
                        <strong>{{ $hall['bookings_count'] }}</strong><br>
                        {{ __('pdf.financial_report.bookings') }}
                    </div>
                    <div class="hall-card-stat">
                        <strong class="currency">{{ number_format($hall['gross_revenue'], 3) }}</strong><br>
                        {{ __('pdf.financial_report.gross') }}
                    </div>
                    <div class="hall-card-stat">
                        <strong>{{ number_format($hall['contribution_percentage'] ?? 0, 1) }}%</strong><br>
                        {{ __('pdf.financial_report.contribution') }}
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width: {{ min(100, $hall['contribution_percentage'] ?? 0) }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Hall Comparison Table -->
        <div class="section">
            <div class="section-header">{{ __('pdf.financial_report.hall_comparison') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('pdf.financial_report.hall') }}</th>
                        <th class="text-center">{{ __('pdf.financial_report.bookings') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.hall_rev') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.services_rev') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.net') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.share') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hallData as $hall)
                    <tr>
                        <td>{{ $hall['name'] }}</td>
                        <td class="text-center">{{ $hall['bookings_count'] }}</td>
                        <td class="text-right currency">{{ number_format($hall['hall_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($hall['services_revenue'], 3) }}</td>
                        <td class="text-right currency" style="font-weight: bold;">{{ number_format($hall['net_earnings'], 3) }}</td>
                        <td class="text-right">{{ number_format($hall['contribution_percentage'] ?? 0, 1) }}%</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td><strong>{{ __('pdf.financial_report.total') }}</strong></td>
                        <td class="text-center">{{ $summary['total_bookings'] }}</td>
                        <td class="text-right currency">{{ number_format($summary['hall_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($summary['services_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($summary['net_earnings'], 3) }}</td>
                        <td class="text-right">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ============================================================== --}}
        {{-- COMPARISON REPORT CONTENT --}}
        {{-- ============================================================== --}}
        @elseif($reportType === 'comparison')

        <div class="section">
            <div class="section-header">{{ __('pdf.financial_report.month_comparison') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('pdf.financial_report.metric') }}</th>
                        <th class="text-right">{{ __('months.' . $previousMonth) }}</th>
                        <th class="text-right">{{ __('months.' . $currentMonth) }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.change') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ __('pdf.financial_report.total_bookings') }}</td>
                        <td class="text-right">{{ $previousData['total_bookings'] }}</td>
                        <td class="text-right">{{ $currentData['total_bookings'] }}</td>
                        <td class="text-right {{ $changes['bookings'] >= 0 ? 'comparison-positive' : 'comparison-negative' }}">
                            {{ $changes['bookings'] >= 0 ? '+' : '' }}{{ number_format($changes['bookings'], 1) }}%
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('pdf.financial_report.gross_revenue') }}</td>
                        <td class="text-right currency">{{ number_format($previousData['gross_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($currentData['gross_revenue'], 3) }}</td>
                        <td class="text-right {{ $changes['gross_revenue'] >= 0 ? 'comparison-positive' : 'comparison-negative' }}">
                            {{ $changes['gross_revenue'] >= 0 ? '+' : '' }}{{ number_format($changes['gross_revenue'], 1) }}%
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('pdf.financial_report.hall_revenue') }}</td>
                        <td class="text-right currency">{{ number_format($previousData['hall_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($currentData['hall_revenue'], 3) }}</td>
                        <td class="text-right {{ $changes['hall_revenue'] >= 0 ? 'comparison-positive' : 'comparison-negative' }}">
                            {{ $changes['hall_revenue'] >= 0 ? '+' : '' }}{{ number_format($changes['hall_revenue'], 1) }}%
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('pdf.financial_report.services_revenue') }}</td>
                        <td class="text-right currency">{{ number_format($previousData['services_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($currentData['services_revenue'], 3) }}</td>
                        <td class="text-right {{ $changes['services_revenue'] >= 0 ? 'comparison-positive' : 'comparison-negative' }}">
                            {{ $changes['services_revenue'] >= 0 ? '+' : '' }}{{ number_format($changes['services_revenue'], 1) }}%
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('pdf.financial_report.commission') }}</td>
                        <td class="text-right currency" style="color: #dc2626;">-{{ number_format($previousData['total_commission'], 3) }}</td>
                        <td class="text-right currency" style="color: #dc2626;">-{{ number_format($currentData['total_commission'], 3) }}</td>
                        <td class="text-right {{ $changes['commission'] >= 0 ? 'comparison-negative' : 'comparison-positive' }}">
                            {{ $changes['commission'] >= 0 ? '+' : '' }}{{ number_format($changes['commission'], 1) }}%
                        </td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>{{ __('pdf.financial_report.net_earnings') }}</strong></td>
                        <td class="text-right currency">{{ number_format($previousData['net_earnings'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($currentData['net_earnings'], 3) }}</td>
                        <td class="text-right {{ $changes['net_earnings'] >= 0 ? 'comparison-positive' : 'comparison-negative' }}">
                            <strong>{{ $changes['net_earnings'] >= 0 ? '+' : '' }}{{ number_format($changes['net_earnings'], 1) }}%</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('pdf.financial_report.avg_per_booking') }}</td>
                        <td class="text-right currency">{{ number_format($previousData['avg_per_booking'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($currentData['avg_per_booking'], 3) }}</td>
                        <td class="text-right {{ $changes['avg_per_booking'] >= 0 ? 'comparison-positive' : 'comparison-negative' }}">
                            {{ $changes['avg_per_booking'] >= 0 ? '+' : '' }}{{ number_format($changes['avg_per_booking'], 1) }}%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Analysis Notes -->
        <div class="notes-box">
            <div class="notes-title">{{ __('pdf.financial_report.analysis') }}</div>
            <div class="notes-content">
                @if($changes['net_earnings'] > 0)
                    {{ __('pdf.financial_report.analysis_positive', ['change' => number_format($changes['net_earnings'], 1)]) }}
                @elseif($changes['net_earnings'] < 0)
                    {{ __('pdf.financial_report.analysis_negative', ['change' => number_format(abs($changes['net_earnings']), 1)]) }}
                @else
                    {{ __('pdf.financial_report.analysis_neutral') }}
                @endif
            </div>
        </div>

        @endif

        {{-- ============================================================== --}}
        {{-- HALL BREAKDOWN (for all report types except hall) --}}
        {{-- ============================================================== --}}
        @if($reportType !== 'hall' && isset($hallBreakdown) && count($hallBreakdown) > 1)
        <div class="section">
            <div class="section-header">{{ __('pdf.financial_report.hall_breakdown') }}</div>
            <table class="compact">
                <thead>
                    <tr>
                        <th>{{ __('pdf.financial_report.hall') }}</th>
                        <th class="text-center">{{ __('pdf.financial_report.bookings') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.gross') }}</th>
                        <th class="text-right">{{ __('pdf.financial_report.net') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hallBreakdown as $hall)
                    <tr>
                        <td>{{ $hall['name'] }}</td>
                        <td class="text-center">{{ $hall['bookings_count'] }}</td>
                        <td class="text-right currency">{{ number_format($hall['gross_revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($hall['net_earnings'], 3) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
