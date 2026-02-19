<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp"> --}}
    <title>All Hall Owners Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            background-color: #1e40af;
            color: white;
            padding: 25px 20px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            opacity: 0.9;
        }

        .container {
            padding: 0 15px;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #f3f4f6;
            color: #1e40af;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            border-left: 4px solid #1e40af;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
        }

        .stats-row {
            display: table-row;
        }

        .stat-box {
            display: table-cell;
            width: 16.66%;
            padding: 12px;
            text-align: center;
            border-right: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .stat-box:last-child {
            border-right: none;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .stat-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table thead {
            background-color: #1e40af;
            color: white;
        }

        table th {
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
        }

        table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .highlight {
            background-color: #fef3c7;
            padding: 12px;
            border-left: 4px solid #f59e0b;
            margin-bottom: 15px;
        }

        .highlight-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .highlight-value {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }

        .currency {
            font-family: 'DejaVu Sans', monospace;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }

        .info-box {
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            font-size: 9px;
        }

        .info-value {
            display: table-cell;
            font-size: 9px;
        }

        .page-break {
            page-break-after: always;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📊 All Hall Owners Performance Report</h1>
        <p>Comprehensive Overview | Generated on {{ $generatedAt->format('F d, Y') }} by {{ $generatedBy }}</p>
    </div>

    <div class="container">
        <!-- Report Information -->
        <div class="info-box">
            <div class="info-row">
                <div class="info-label">Report Period:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Generated At:</div>
                <div class="info-value">{{ $generatedAt->format('M d, Y h:i A') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Generated By:</div>
                <div class="info-value">{{ $generatedBy }}</div>
            </div>
        </div>

        <!-- Overall Statistics -->
        <div class="section">
            <div class="section-title">📈 Overall Statistics</div>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-value">{{ $overallStats['total_owners'] }}</div>
                        <div class="stat-label">Total Owners</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ $overallStats['verified_owners'] }}</div>
                        <div class="stat-label">Verified</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ $overallStats['active_owners'] }}</div>
                        <div class="stat-label">Active</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ $overallStats['total_halls'] }}</div>
                        <div class="stat-label">Total Halls</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ $overallStats['active_halls'] }}</div>
                        <div class="stat-label">Active Halls</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">{{ $overallStats['total_bookings'] }}</div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="section">
            <div class="section-title">💰 Financial Summary</div>
            <div class="highlight">
                <div class="highlight-title">Total Platform Revenue (Period)</div>
                <div class="highlight-value currency">
                    {{ number_format($overallStats['total_revenue'], 3) }} OMR
                </div>
            </div>

            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-value currency">{{ number_format($overallStats['total_revenue'], 3) }} OMR</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value currency">{{ number_format($overallStats['total_commission'], 3) }} OMR</div>
                        <div class="stat-label">Platform Commission</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value currency">{{ number_format($overallStats['total_payout'], 3) }} OMR</div>
                        <div class="stat-label">Owner Payouts</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        @if($topOwners->count() > 0)
        <div class="section">
            <div class="section-title">🏆 Top 10 Performing Owners (By Revenue)</div>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Business Name</th>
                        <th>Owner</th>
                        <th class="text-center">Halls</th>
                        <th class="text-center">Bookings</th>
                        <th class="text-right">Revenue</th>
                        <th class="text-right">Commission</th>
                        <th class="text-right">Payout</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topOwners as $index => $owner)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $owner['business_name'] }}</td>
                        <td>{{ $owner['owner_name'] }}</td>
                        <td class="text-center">{{ $owner['halls_count'] }}</td>
                        <td class="text-center">{{ $owner['bookings_count'] }}</td>
                        <td class="text-right currency">{{ number_format($owner['revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($owner['commission'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($owner['payout'], 3) }}</td>
                        <td class="text-center">
                            @if($owner['is_verified'] && $owner['is_active'])
                                <span class="badge badge-success">Active</span>
                            @elseif($owner['is_verified'])
                                <span class="badge badge-warning">Verified</span>
                            @else
                                <span class="badge badge-danger">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- All Owners Performance -->
        <div class="section page-break">
            <div class="section-title">📋 All Owners Performance Details</div>
            @if($ownerPerformance->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Business Name</th>
                        <th>Owner Name</th>
                        <th class="text-center">Halls</th>
                        <th class="text-center">Bookings</th>
                        <th class="text-right">Revenue (OMR)</th>
                        <th class="text-right">Commission (OMR)</th>
                        <th class="text-right">Payout (OMR)</th>
                        <th class="text-center">Verified</th>
                        <th class="text-center">Active</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ownerPerformance as $index => $owner)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $owner['business_name'] }}</td>
                        <td>{{ $owner['owner_name'] }}</td>
                        <td class="text-center">{{ $owner['halls_count'] }}</td>
                        <td class="text-center">{{ $owner['bookings_count'] }}</td>
                        <td class="text-right currency">{{ number_format($owner['revenue'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($owner['commission'], 3) }}</td>
                        <td class="text-right currency">{{ number_format($owner['payout'], 3) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $owner['is_verified'] ? 'badge-success' : 'badge-warning' }}">
                                {{ $owner['is_verified'] ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $owner['is_active'] ? 'badge-success' : 'badge-danger' }}">
                                {{ $owner['is_active'] ? 'Yes' : 'No' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach>
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td colspan="4">TOTAL</td>
                        <td class="text-center">{{ $ownerPerformance->sum('bookings_count') }}</td>
                        <td class="text-right currency">{{ number_format($ownerPerformance->sum('revenue'), 3) }}</td>
                        <td class="text-right currency">{{ number_format($ownerPerformance->sum('commission'), 3) }}</td>
                        <td class="text-right currency">{{ number_format($ownerPerformance->sum('payout'), 3) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
            @else
            <div class="no-data">No owner performance data available for the selected period</div>
            @endif
        </div>

        <!-- Summary Insights -->
        <div class="section">
            <div class="section-title">💡 Key Insights</div>
            <div style="padding: 10px; background: #f9fafb; border-radius: 4px;">
                <p style="margin-bottom: 8px; font-size: 10px;"><strong>Verification Rate:</strong>
                    {{ $overallStats['total_owners'] > 0 ? number_format(($overallStats['verified_owners'] / $overallStats['total_owners']) * 100, 1) : 0 }}%
                    of owners are verified
                </p>
                <p style="margin-bottom: 8px; font-size: 10px;"><strong>Active Rate:</strong>
                    {{ $overallStats['total_owners'] > 0 ? number_format(($overallStats['active_owners'] / $overallStats['total_owners']) * 100, 1) : 0 }}%
                    of owners are active
                </p>
                <p style="margin-bottom: 8px; font-size: 10px;"><strong>Average Halls per Owner:</strong>
                    {{ $overallStats['total_owners'] > 0 ? number_format($overallStats['total_halls'] / $overallStats['total_owners'], 1) : 0 }} halls
                </p>
                <p style="margin-bottom: 8px; font-size: 10px;"><strong>Average Bookings per Hall:</strong>
                    {{ $overallStats['total_halls'] > 0 ? number_format($overallStats['total_bookings'] / $overallStats['total_halls'], 1) : 0 }} bookings
                </p>
                <p style="font-size: 10px;"><strong>Commission Rate:</strong>
                    {{ $overallStats['total_revenue'] > 0 ? number_format(($overallStats['total_commission'] / $overallStats['total_revenue']) * 100, 1) : 0 }}%
                    of total revenue
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This report was automatically generated by the Hall Booking Management System</p>
            <p>Report ID: ALL-{{ now()->format('YmdHis') }} | Confidential - For Internal Use Only</p>
        </div>
    </div>
</body>
</html>
