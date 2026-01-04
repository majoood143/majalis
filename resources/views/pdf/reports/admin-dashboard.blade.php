<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.reports.pdf.title') }} - {{ config('app.name') }}</title>
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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            margin-bottom: 30px;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .header-meta {
            margin-top: 15px;
            font-size: 11px;
            opacity: 0.8;
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
            border-bottom: 2px solid #4f46e5;
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

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #6b7280;
        }

        .summary-value {
            font-weight: 600;
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

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 500;
        }

        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-primary { background: #e0e7ff; color: #3730a3; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-title">{{ __('admin.reports.pdf.title') }}</div>
        <div class="header-subtitle">{{ config('app.name') }} - {{ __('admin.reports.pdf.platform_report') }}</div>
        <div class="header-meta">
            {{ __('admin.reports.pdf.period') }}: {{ $startDate }} → {{ $endDate }} |
            {{ __('admin.reports.pdf.generated') }}: {{ $generatedAt }} |
            {{ __('admin.reports.pdf.by') }}: {{ $generatedBy }}
        </div>
    </div>

    {{-- Overview Stats --}}
    <div class="section">
        <div class="section-title">📊 {{ __('admin.reports.pdf.overview') }}</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value success">{{ number_format($stats['total_revenue'], 3) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.total_revenue') }} (OMR)</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value primary">{{ number_format($stats['total_commission'], 3) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.platform_commission') }} (OMR)</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_owner_payout'], 3) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.owner_payouts') }} (OMR)</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value warning">{{ number_format($stats['pending_payout_amount'], 3) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.pending_payouts') }} (OMR)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Stats --}}
    <div class="section">
        <div class="section-title">📅 {{ __('admin.reports.pdf.booking_stats') }}</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_bookings']) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.total_bookings') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value success">{{ number_format($stats['confirmed_bookings']) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.confirmed') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value primary">{{ number_format($stats['completed_bookings']) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.completed') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value danger">{{ number_format($stats['cancelled_bookings']) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.cancelled') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Commission Report --}}
    <div class="section">
        <div class="section-title">💰 {{ __('admin.reports.pdf.commission_summary') }}</div>
        <div class="summary-box">
            <table>
                <tr>
                    <td>{{ __('admin.reports.pdf.gross_revenue') }}</td>
                    <td class="text-right currency">{{ number_format($commissionReport['total_revenue'], 3) }} OMR</td>
                </tr>
                <tr>
                    <td>{{ __('admin.reports.pdf.total_commission') }}</td>
                    <td class="text-right currency primary">{{ number_format($commissionReport['total_commission'], 3) }} OMR</td>
                </tr>
                <tr>
                    <td>{{ __('admin.reports.pdf.avg_commission_rate') }}</td>
                    <td class="text-right"><strong>{{ $commissionReport['commission_rate'] }}%</strong></td>
                </tr>
                <tr>
                    <td>{{ __('admin.reports.pdf.bookings_processed') }}</td>
                    <td class="text-right"><strong>{{ $commissionReport['bookings_count'] }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Top Performing Halls --}}
    @if($topHalls->isNotEmpty())
        <div class="section page-break">
            <div class="section-title">🏆 {{ __('admin.reports.pdf.top_halls') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin.reports.pdf.hall_name') }}</th>
                        <th class="text-center">{{ __('admin.reports.pdf.bookings') }}</th>
                        <th class="text-right">{{ __('admin.reports.pdf.revenue') }}</th>
                        <th class="text-right">{{ __('admin.reports.pdf.avg_booking') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topHalls as $index => $hall)
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
            </table>
        </div>
    @endif

    {{-- Top Performing Owners --}}
    @if($topOwners->isNotEmpty())
        <div class="section">
            <div class="section-title">👥 {{ __('admin.reports.pdf.top_owners') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('admin.reports.pdf.owner_name') }}</th>
                        <th>{{ __('admin.reports.pdf.business') }}</th>
                        <th class="text-center">{{ __('admin.reports.pdf.halls') }}</th>
                        <th class="text-center">{{ __('admin.reports.pdf.bookings') }}</th>
                        <th class="text-right">{{ __('admin.reports.pdf.revenue') }}</th>
                        <th class="text-right">{{ __('admin.reports.pdf.commission') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topOwners as $index => $owner)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $owner->name }}</td>
                            <td>{{ $owner->business_name ?? '—' }}</td>
                            <td class="text-center">{{ $owner->halls_count }}</td>
                            <td class="text-center">{{ $owner->bookings_count }}</td>
                            <td class="text-right currency success">{{ number_format((float) $owner->total_revenue, 3) }} OMR</td>
                            <td class="text-right currency primary">{{ number_format((float) $owner->total_commission, 3) }} OMR</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Platform Stats --}}
    <div class="section">
        <div class="section-title">🏢 {{ __('admin.reports.pdf.platform_stats') }}</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-value primary">{{ number_format($stats['total_halls']) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.active_halls') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_owners']) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.verified_owners') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.total_customers') }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['pending_payouts'] }}</div>
                    <div class="stat-label">{{ __('admin.reports.pdf.pending_payout_count') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ __('admin.reports.pdf.footer', ['app' => config('app.name')]) }}</p>
        <p>{{ __('admin.reports.pdf.confidential') }}</p>
    </div>
</body>
</html>
