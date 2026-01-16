<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('owner_report.reports.pdf.title') }} - {{ $owner->name }}</title>
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 30px;
            margin-bottom: 30px;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-business {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .header-subtitle {
            font-size: 14px;
            opacity: 0.8;
        }

        .header-meta {
            margin-top: 15px;
            font-size: 11px;
            opacity: 0.7;
        }

        /* Owner Info Box */
        .owner-info {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
        }

        .owner-info-title {
            font-weight: 600;
            color: #166534;
            margin-bottom: 10px;
        }

        .owner-info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .owner-info-label {
            width: 120px;
            color: #6b7280;
            font-size: 11px;
        }

        .owner-info-value {
            font-weight: 500;
        }

        /* Stats Grid */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .stats-row {
            display: table-row;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .stat-box.highlight {
            background: #f0fdf4;
            border-color: #86efac;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-value.success { color: #059669; }
        .stat-value.primary { color: #4f46e5; }
        .stat-value.warning { color: #d97706; }
        .stat-value.danger { color: #dc2626; }

        .stat-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Section */
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            padding-bottom: 10px;
            border-bottom: 2px solid #059669;
            margin-bottom: 15px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px 12px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #f3f4f6;
            font-weight: 600;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
        }

        tr:hover {
            background: #f9fafb;
        }

        .text-right { text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; }
        .text-center { text-align: center; }

        .currency {
            font-family: monospace;
            font-weight: 600;
        }

        .currency.success { color: #059669; }
        .currency.danger { color: #dc2626; }
        .currency.primary { color: #4f46e5; }

        /* Summary Box */
        .summary-box {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #374151;
        }

        /* Earnings Highlight */
        .earnings-highlight {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin-bottom: 30px;
        }

        .earnings-amount {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .earnings-label {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Comparison Box */
        .comparison-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .comparison-box {
            display: table-cell;
            width: 50%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .comparison-change {
            font-size: 24px;
            font-weight: bold;
        }

        .comparison-change.positive { color: #059669; }
        .comparison-change.negative { color: #dc2626; }

        .comparison-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-title">{{ __('owner_report.reports.pdf.earnings_report') }}</div>
        @if($hallOwner)
            <div class="header-business">{{ $hallOwner->business_name ?? $owner->name }}</div>
        @endif
        <div class="header-subtitle">{{ __('owner_report.reports.pdf.subtitle') }}</div>
        <div class="header-meta">
            {{ __('owner_report.reports.pdf.period') }}: {{ $startDate }} → {{ $endDate }} |
            {{ __('owner_report.reports.pdf.generated') }}: {{ $generatedAt }}
        </div>
    </div>

    {{-- Owner Info --}}
    @if($hallOwner)
        <div class="owner-info">
            <div class="owner-info-title">{{ __('owner_report.reports.pdf.owner_details') }}</div>
            <div class="owner-info-row">
                <span class="owner-info-label">{{ __('owner_report.reports.pdf.owner_name') }}:</span>
                <span class="owner-info-value">{{ $owner->name }}</span>
            </div>
            <div class="owner-info-row">
                <span class="owner-info-label">{{ __('owner_report.reports.pdf.email') }}:</span>
                <span class="owner-info-value">{{ $owner->email }}</span>
            </div>
            @if($hallOwner->business_name)
                <div class="owner-info-row">
                    <span class="owner-info-label">{{ __('owner_report.reports.pdf.business') }}:</span>
                    <span class="owner-info-value">{{ $hallOwner->business_name }}</span>
                </div>
            @endif
            @if($hallOwner->phone)
                <div class="owner-info-row">
                    <span class="owner-info-label">{{ __('owner_report.reports.pdf.phone') }}:</span>
                    <span class="owner-info-value">{{ $hallOwner->phone }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- Net Earnings Highlight --}}
    <div class="earnings-highlight">
        <div class="earnings-amount">{{ number_format($stats['total_earnings'], 3) }} OMR</div>
        <div class="earnings-label">{{ __('owner_report.reports.pdf.net_earnings') }}</div>
    </div>

    {{-- Financial Overview --}}
    <div class="section">
        <div class="section-title">💰 {{ __('owner_report.reports.pdf.financial_overview') }}</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_revenue'], 3) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.gross_revenue') }} (OMR)</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value danger">{{ number_format($stats['platform_commission'], 3) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.platform_fee') }} (OMR)</div>
                </div>
                <div class="stat-box highlight">
                    <div class="stat-value success">{{ number_format($stats['total_earnings'], 3) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.net_earnings') }} (OMR)</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value warning">{{ number_format($stats['pending_payouts'], 3) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.pending_payout') }} (OMR)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Comparison --}}
    @if(!empty($comparison))
        <div class="section">
            <div class="section-title">📈 {{ __('owner_report.reports.pdf.monthly_comparison') }}</div>
            <div class="comparison-grid">
                <div class="comparison-box">
                    <div class="comparison-change {{ $comparison['revenue_change'] >= 0 ? 'positive' : 'negative' }}">
                        {{ $comparison['revenue_change'] >= 0 ? '+' : '' }}{{ $comparison['revenue_change'] }}%
                    </div>
                    <div class="comparison-label">{{ __('owner_report.reports.pdf.earnings_change') }}</div>
                </div>
                <div class="comparison-box">
                    <div class="comparison-change {{ $comparison['bookings_change'] >= 0 ? 'positive' : 'negative' }}">
                        {{ $comparison['bookings_change'] >= 0 ? '+' : '' }}{{ $comparison['bookings_change'] }}%
                    </div>
                    <div class="comparison-label">{{ __('owner_report.reports.pdf.bookings_change') }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Booking Statistics --}}
    <div class="section">
        <div class="section-title">📅 {{ __('owner_report.reports.pdf.booking_stats') }}</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_bookings']) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.total_bookings') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value success">{{ number_format($stats['confirmed_bookings']) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.confirmed') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value primary">{{ number_format($stats['completed_bookings']) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.completed') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value danger">{{ number_format($stats['cancelled_bookings']) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.cancelled') }}</div>
                </div>
            </div>
        </div>

        <div class="summary-box">
            <div class="summary-title">{{ __('owner_report.reports.pdf.additional_stats') }}</div>
            <table>
                <tr>
                    <td>{{ __('owner_report.reports.pdf.total_guests') }}</td>
                    <td class="text-right"><strong>{{ number_format($stats['total_guests']) }}</strong></td>
                </tr>
                <tr>
                    <td>{{ __('owner_report.reports.pdf.avg_booking_value') }}</td>
                    <td class="text-right currency">{{ number_format($stats['average_booking_value'], 3) }} OMR</td>
                </tr>
                <tr>
                    <td>{{ __('owner_report.reports.pdf.total_paid_out') }}</td>
                    <td class="text-right currency success">{{ number_format($stats['completed_payouts'], 3) }} OMR</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Hall Performance --}}
    @if($hallPerformance->isNotEmpty())
        <div class="section page-break">
            <div class="section-title">🏢 {{ __('owner_report.reports.pdf.hall_performance') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('owner_report.reports.pdf.hall_name') }}</th>
                        <th class="text-center">{{ __('owner_report.reports.pdf.bookings') }}</th>
                        <th class="text-right">{{ __('owner_report.reports.pdf.revenue') }}</th>
                        <th class="text-right">{{ __('owner_report.reports.pdf.avg_booking') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hallPerformance as $index => $hall)
                        @php
                            $hallName = is_array($hall->name) ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? '') : $hall->name;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $hallName }}</td>
                            <td class="text-center">{{ $hall->bookings_count }}</td>
                            <td class="text-right currency success">{{ number_format((float) $hall->total_revenue, 3) }} OMR</td>
                            <td class="text-right currency">{{ number_format((float) $hall->avg_booking_value, 3) }} OMR</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f3f4f6; font-weight: bold;">
                        <td colspan="2">{{ __('owner_report.reports.pdf.total') }}</td>
                        <td class="text-center">{{ $hallPerformance->sum('bookings_count') }}</td>
                        <td class="text-right currency success">{{ number_format((float) $hallPerformance->sum('total_revenue'), 3) }} OMR</td>
                        <td class="text-right">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    {{-- Hall Summary --}}
    <div class="section">
        <div class="section-title">📊 {{ __('owner_report.reports.pdf.hall_summary') }}</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value primary">{{ $stats['total_halls'] }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.total_halls') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value success">{{ $stats['active_halls'] }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.active_halls') }}</div>
                </div>
                <div class="stat-box">
                    @php
                        $avgPerHall = $stats['total_halls'] > 0 ? $stats['total_bookings'] / $stats['total_halls'] : 0;
                    @endphp
                    <div class="stat-value">{{ number_format($avgPerHall, 1) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.avg_bookings_per_hall') }}</div>
                </div>
                <div class="stat-box">
                    @php
                        $avgEarningsPerHall = $stats['total_halls'] > 0 ? $stats['total_earnings'] / $stats['total_halls'] : 0;
                    @endphp
                    <div class="stat-value success">{{ number_format($avgEarningsPerHall, 3) }}</div>
                    <div class="stat-label">{{ __('owner_report.reports.pdf.avg_earnings_per_hall') }} (OMR)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ __('owner_report.reports.pdf.footer', ['app' => config('app.name')]) }}</p>
        <p>{{ __('owner_report.reports.pdf.thank_you') }}</p>
    </div>
</body>
</html>
