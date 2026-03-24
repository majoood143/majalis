<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Earnings Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #111827;
            line-height: 1.4;
            background: #ffffff;
        }

        .page { padding: 20px 25px; }

        /* Header */
        .header {
            border-bottom: 2px solid #B9916D;
            margin-bottom: 12px;
            padding-bottom: 10px;
        }

        .header-inner {
            display: table;
            width: 100%;
        }

        .header-logo { display: table-cell; width: 30%; vertical-align: middle; }
        .header-title { display: table-cell; width: 40%; vertical-align: middle; text-align: center; }
        .header-meta  { display: table-cell; width: 30%; vertical-align: middle; text-align: right; font-size: 7pt; color: #6b7280; }

        .report-title { font-size: 14pt; font-weight: bold; color: #B9916D; }
        .report-period { font-size: 8pt; color: #374151; margin-top: 3px; }

        /* Owner info bar */
        .owner-bar {
            display: table;
            width: 100%;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            margin-bottom: 12px;
        }

        .owner-bar-cell { display: table-cell; width: 33%; }
        .owner-bar-label { font-size: 6.5pt; color: #6b7280; text-transform: uppercase; }
        .owner-bar-value { font-size: 9pt; font-weight: bold; color: #111827; }

        /* Summary stat boxes */
        .stat-grid { display: table; width: 100%; margin-bottom: 12px; }
        .stat-cell { display: table-cell; padding: 3px; }

        .stat-box {
            border: 1px solid #e5e7eb;
            padding: 8px 6px;
            text-align: center;
        }

        .stat-box.primary { background: #B9916D; border-color: #B9916D; }
        .stat-box.primary .stat-value,
        .stat-box.primary .stat-label { color: #fff; }

        .stat-box.success { border-color: #10b981; border-width: 2px; }
        .stat-box.success .stat-value { color: #059669; }

        .stat-box.danger { border-color: #ef4444; border-width: 2px; }
        .stat-box.danger .stat-value { color: #dc2626; }

        .stat-value { font-size: 12pt; font-weight: bold; color: #111827; }
        .stat-label { font-size: 6.5pt; color: #6b7280; text-transform: uppercase; margin-top: 2px; }

        /* Section */
        .section { margin-bottom: 14px; }

        .section-header {
            font-size: 9pt;
            font-weight: bold;
            color: #B9916D;
            border-bottom: 1.5px solid #B9916D;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }

        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }

        .data-table thead th {
            background: #B9916D;
            color: #fff;
            padding: 5px 6px;
            text-align: left;
            font-size: 7pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .data-table tbody td {
            padding: 4px 6px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }

        .data-table tbody tr:nth-child(even) td { background: #f9fafb; }

        .data-table tfoot td {
            font-weight: bold;
            background: #f3f4f6;
            border-top: 1.5px solid #B9916D;
            padding: 5px 6px;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .text-bold   { font-weight: bold; }
        .color-danger  { color: #dc2626; }
        .color-success { color: #059669; }

        /* Footer */
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
            margin-top: 14px;
            font-size: 6.5pt;
            color: #6b7280;
        }

        .footer-inner { display: table; width: 100%; }
        .footer-left  { display: table-cell; width: 60%; }
        .footer-right { display: table-cell; width: 40%; text-align: right; }
    </style>
</head>
<body>
<div class="page">

    {{-- ================================================================
        Header
    ================================================================ --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-logo">
                @if(file_exists(public_path(config('app.logo_path', 'images/logo.webp'))))
                    <img src="{{ public_path(config('app.logo_path', 'images/logo.webp')) }}"
                         alt="{{ config('app.name') }}" style="height: 36px;">
                @else
                    <span style="font-size: 14pt; font-weight: bold; color: #B9916D;">{{ config('app.name') }}</span>
                @endif
            </div>
            <div class="header-title">
                <div class="report-title">Earnings Report</div>
                <div class="report-period">
                    {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
                    &mdash;
                    {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                </div>
            </div>
            <div class="header-meta">
                Generated: {{ $generatedAt->format('d M Y, H:i') }}<br>
                {{ config('app.name') }}
            </div>
        </div>
    </div>

    {{-- ================================================================
        Owner info bar
    ================================================================ --}}
    <div class="owner-bar">
        <div class="owner-bar-cell">
            <div class="owner-bar-label">Owner</div>
            <div class="owner-bar-value">{{ $owner->name }}</div>
        </div>
        <div class="owner-bar-cell">
            <div class="owner-bar-label">Email</div>
            <div class="owner-bar-value" style="font-size: 8pt;">{{ $owner->email }}</div>
        </div>
        <div class="owner-bar-cell">
            @if($hallOwner)
                <div class="owner-bar-label">Business</div>
                <div class="owner-bar-value">{{ $hallOwner->business_name ?? '-' }}</div>
            @endif
        </div>
    </div>

    {{-- ================================================================
        Summary stats (6 boxes)
    ================================================================ --}}
    <div class="stat-grid">
        <div class="stat-cell">
            <div class="stat-box">
                <div class="stat-value">{{ $stats['total_bookings'] }}</div>
                <div class="stat-label">Bookings</div>
            </div>
        </div>
        <div class="stat-cell">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['hall_revenue'], 3) }}</div>
                <div class="stat-label">Hall Revenue (OMR)</div>
            </div>
        </div>
        <div class="stat-cell">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['services_revenue'], 3) }}</div>
                <div class="stat-label">Services (OMR)</div>
            </div>
        </div>
        <div class="stat-cell">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['gross_revenue'], 3) }}</div>
                <div class="stat-label">Gross Revenue (OMR)</div>
            </div>
        </div>
        <div class="stat-cell">
            <div class="stat-box danger">
                <div class="stat-value">{{ number_format($stats['total_commission'], 3) }}</div>
                <div class="stat-label">Commission (OMR)</div>
            </div>
        </div>
        <div class="stat-cell">
            <div class="stat-box primary">
                <div class="stat-value">{{ number_format($stats['net_earnings'], 3) }}</div>
                <div class="stat-label">Net Earnings (OMR)</div>
            </div>
        </div>
    </div>

    {{-- ================================================================
        Hall Breakdown (if included and multiple halls)
    ================================================================ --}}
    @if($hallBreakdown->isNotEmpty())
    <div class="section">
        <div class="section-header">Hall Breakdown</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Hall</th>
                    <th class="text-center" width="12%">Bookings</th>
                    <th class="text-right" width="18%">Gross (OMR)</th>
                    <th class="text-right" width="18%">Commission (OMR)</th>
                    <th class="text-right" width="18%">Net Earnings (OMR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hallBreakdown as $row)
                <tr>
                    <td class="text-bold">{{ $row['hall_name'] }}</td>
                    <td class="text-center">{{ $row['bookings_count'] }}</td>
                    <td class="text-right">{{ number_format($row['gross_revenue'], 3) }}</td>
                    <td class="text-right color-danger">{{ number_format($row['commission'], 3) }}</td>
                    <td class="text-right color-success text-bold">{{ number_format($row['net_earnings'], 3) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="text-center">{{ $stats['total_bookings'] }}</td>
                    <td class="text-right">{{ number_format($stats['gross_revenue'], 3) }}</td>
                    <td class="text-right color-danger">{{ number_format($stats['total_commission'], 3) }}</td>
                    <td class="text-right color-success">{{ number_format($stats['net_earnings'], 3) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- ================================================================
        Monthly Breakdown
    ================================================================ --}}
    @if($monthlyBreakdown->isNotEmpty())
    <div class="section">
        <div class="section-header">Monthly Breakdown</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-center" width="12%">Bookings</th>
                    <th class="text-right" width="18%">Gross (OMR)</th>
                    <th class="text-right" width="18%">Commission (OMR)</th>
                    <th class="text-right" width="18%">Net (OMR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyBreakdown as $row)
                <tr>
                    <td class="text-bold">{{ $row['month'] }}</td>
                    <td class="text-center">{{ $row['bookings'] }}</td>
                    <td class="text-right">{{ number_format($row['gross'], 3) }}</td>
                    <td class="text-right color-danger">{{ number_format($row['commission'], 3) }}</td>
                    <td class="text-right color-success text-bold">{{ number_format($row['net'], 3) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="text-center">{{ $monthlyBreakdown->sum('bookings') }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown->sum('gross'), 3) }}</td>
                    <td class="text-right color-danger">{{ number_format($monthlyBreakdown->sum('commission'), 3) }}</td>
                    <td class="text-right color-success">{{ number_format($monthlyBreakdown->sum('net'), 3) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- ================================================================
        Booking Details (if included)
    ================================================================ --}}
    @if($bookings->isNotEmpty())
    <div class="section">
        <div class="section-header">
            Booking Details ({{ $bookings->count() }} records)
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="14%">Booking #</th>
                    <th>Hall</th>
                    <th width="13%">Date</th>
                    <th>Customer</th>
                    <th class="text-center" width="10%">Slot</th>
                    <th class="text-right" width="14%">Gross (OMR)</th>
                    <th class="text-right" width="14%">Net (OMR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                @php
                    $hallName = $booking->hall->name ?? 'N/A';
                    if (is_array($hallName)) {
                        $hallName = $hallName[app()->getLocale()] ?? $hallName['en'] ?? $hallName['ar'] ?? 'N/A';
                    }
                @endphp
                <tr>
                    <td class="text-bold">{{ $booking->booking_number }}</td>
                    <td>{{ $hallName }}</td>
                    <td>{{ $booking->booking_date->format('d M Y') }}</td>
                    <td>{{ $booking->customer_name }}</td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $booking->time_slot)) }}</td>
                    <td class="text-right">{{ number_format($booking->total_amount, 3) }}</td>
                    <td class="text-right color-success text-bold">{{ number_format($booking->owner_payout, 3) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">TOTAL ({{ $bookings->count() }} bookings)</td>
                    <td class="text-right">{{ number_format($stats['gross_revenue'], 3) }}</td>
                    <td class="text-right color-success">{{ number_format($stats['net_earnings'], 3) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- ================================================================
        Footer
    ================================================================ --}}
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-left">
                <strong>{{ config('app.name') }}</strong> &mdash; Hall Booking Management System
            </div>
            <div class="footer-right">
                This report is confidential. &copy; {{ date('Y') }} All Rights Reserved.
            </div>
        </div>
    </div>

</div>
</body>
</html>
