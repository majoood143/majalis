<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Owner Performance Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .owner-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .owner-info h2 {
            font-size: 16px;
            color: #667eea;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px 15px 8px 0;
            width: 40%;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 0;
        }
        
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
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 20px;
            text-align: center;
            width: 25%;
        }
        
        .stat-box.success {
            border-top: 3px solid #28a745;
        }
        
        .stat-box.primary {
            border-top: 3px solid #667eea;
        }
        
        .stat-box.warning {
            border-top: 3px solid #ffc107;
        }
        
        .stat-box.danger {
            border-top: 3px solid #dc3545;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 16px;
            color: #667eea;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        
        table thead {
            background: #667eea;
            color: white;
        }
        
        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        table tbody tr:hover {
            background: #e9ecef;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .summary-box {
            background: #fff;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Hall Owner Performance Report</h1>
        <p>{{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}</p>
        <p style="margin-top: 10px;">Generated on {{ $generatedAt->format('F d, Y H:i') }} by {{ $generatedBy }}</p>
    </div>

    <!-- Owner Information -->
    <div class="owner-info">
        <h2>Owner Information</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Business Name:</div>
                <div class="info-value">{{ $owner->business_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Owner Name:</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $owner->business_email ?? $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $owner->business_phone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Commercial Registration:</div>
                <div class="info-value">{{ $owner->commercial_registration }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Halls:</div>
                <div class="info-value">{{ $halls->count() }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Verification Status:</div>
                <div class="info-value">
                    @if($owner->is_verified)
                        <span class="badge badge-success">Verified</span>
                    @else
                        <span class="badge badge-warning">Unverified</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Status:</div>
                <div class="info-value">
                    @if($owner->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="summary-box">
        <div class="summary-title">📊 Performance Summary</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box success">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">{{ number_format($stats['total_revenue'], 3) }} OMR</div>
                </div>
                <div class="stat-box warning">
                    <div class="stat-label">Platform Commission</div>
                    <div class="stat-value">{{ number_format($stats['total_commission'], 3) }} OMR</div>
                </div>
                <div class="stat-box primary">
                    <div class="stat-label">Owner Payout</div>
                    <div class="stat-value">{{ number_format($stats['owner_payout'], 3) }} OMR</div>
                </div>
                <div class="stat-box success">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-value">{{ $stats['total_bookings'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="stats-grid" style="margin-bottom: 30px;">
        <div class="stats-row">
            <div class="stat-box success">
                <div class="stat-label">Confirmed</div>
                <div class="stat-value">{{ $stats['confirmed_bookings'] }}</div>
            </div>
            <div class="stat-box primary">
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $stats['completed_bookings'] }}</div>
            </div>
            <div class="stat-box warning">
                <div class="stat-label">Pending</div>
                <div class="stat-value">{{ $stats['pending_bookings'] }}</div>
            </div>
            <div class="stat-box danger">
                <div class="stat-label">Cancelled</div>
                <div class="stat-value">{{ $stats['cancelled_bookings'] }}</div>
            </div>
        </div>
    </div>

    <div class="stats-grid" style="margin-bottom: 30px;">
        <div class="stats-row">
            <div class="stat-box primary">
                <div class="stat-label">Total Guests</div>
                <div class="stat-value">{{ number_format($stats['total_guests']) }}</div>
            </div>
            <div class="stat-box success">
                <div class="stat-label">Avg. Booking Value</div>
                <div class="stat-value">{{ number_format($stats['average_booking_value'], 3) }} OMR</div>
            </div>
            <div class="stat-box primary">
                <div class="stat-label">Active Halls</div>
                <div class="stat-value">{{ $halls->where('is_active', true)->count() }}</div>
            </div>
            <div class="stat-box warning">
                <div class="stat-label">Commission Rate</div>
                <div class="stat-value">
                    @if($stats['total_revenue'] > 0)
                        {{ number_format(($stats['total_commission'] / $stats['total_revenue']) * 100, 1) }}%
                    @else
                        0%
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hall Performance -->
    @if($hallPerformance->count() > 0)
    <div class="section">
        <h2 class="section-title">🏛️ Hall Performance Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Hall Name</th>
                    <th class="text-center">Total Bookings</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Owner Payout</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hallPerformance as $hall)
                <tr>
                    <td><strong>{{ $hall['hall_name'] }}</strong></td>
                    <td class="text-center">{{ $hall['bookings_count'] }}</td>
                    <td class="text-right">{{ number_format($hall['revenue'], 3) }} OMR</td>
                    <td class="text-right">{{ number_format($hall['payout'], 3) }} OMR</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Monthly Breakdown -->
    @if($monthlyBreakdown->count() > 0)
    <div class="section page-break">
        <h2 class="section-title">📅 Monthly Performance Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-center">Bookings</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Commission</th>
                    <th class="text-right">Payout</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyBreakdown as $month)
                <tr>
                    <td><strong>{{ $month['month'] }}</strong></td>
                    <td class="text-center">{{ $month['bookings'] }}</td>
                    <td class="text-right">{{ number_format($month['revenue'], 3) }} OMR</td>
                    <td class="text-right">{{ number_format($month['commission'], 3) }} OMR</td>
                    <td class="text-right">{{ number_format($month['payout'], 3) }} OMR</td>
                </tr>
                @endforeach>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td>TOTAL</td>
                    <td class="text-center">{{ $monthlyBreakdown->sum('bookings') }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown->sum('revenue'), 3) }} OMR</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown->sum('commission'), 3) }} OMR</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown->sum('payout'), 3) }} OMR</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Recent Bookings -->
    @if($bookings->count() > 0)
    <div class="section page-break">
        <h2 class="section-title">📋 Recent Bookings (Latest 50)</h2>
        <table>
            <thead>
                <tr>
                    <th>Booking #</th>
                    <th>Hall</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">Payout</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings->take(50) as $booking)
                <tr>
                    <td>{{ $booking->booking_number }}</td>
                    <td>
                        @php
                            $hallName = $booking->hall->name ?? 'N/A';
                            if (is_array($hallName)) {
                                $hallName = $hallName['en'] ?? $hallName['ar'] ?? 'N/A';
                            }
                        @endphp
                        {{ $hallName }}
                    </td>
                    <td>{{ $booking->booking_date->format('M d, Y') }}</td>
                    <td>{{ $booking->customer_name ?? $booking->user->name }}</td>
                    <td class="text-center">
                        @php
                            $status = is_object($booking->status) ? $booking->status->value : $booking->status;
                        @endphp
                        @if($status === 'completed')
                            <span class="badge badge-success">Completed</span>
                        @elseif($status === 'confirmed')
                            <span class="badge badge-info">Confirmed</span>
                        @elseif($status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($status === 'cancelled')
                            <span class="badge badge-danger">Cancelled</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($status) }}</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($booking->total_amount, 3) }} OMR</td>
                    <td class="text-right">{{ number_format($booking->owner_payout, 3) }} OMR</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($bookings->count() > 50)
        <p style="text-align: center; color: #6c757d; font-style: italic;">
            Showing 50 of {{ $bookings->count() }} total bookings
        </p>
        @endif
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Hall Booking Management System</strong></p>
        <p>This report is automatically generated and contains confidential information.</p>
        <p>© {{ date('Y') }} All Rights Reserved</p>
    </div>
</body>
</html>