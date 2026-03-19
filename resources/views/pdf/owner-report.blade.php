<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hall Owner Performance Report</title>
    <style>
        * { margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #000000;
            line-height: 1.4;
            background: #ffffff;
        }

        /* Section titles */
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #000000;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #000000;
            margin-bottom: 6px;
        }

        /* Stat boxes */
        .stat-box {
            padding: 6px 8px;
            text-align: center;
            border: 1px solid #cccccc;
            background: #f9f9f9;
        }

        .stat-value {
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
        }

        .stat-label {
            font-size: 6.5pt;
            color: #444444;
            margin-top: 2px;
            text-transform: uppercase;
        }

        /* Data tables */
        .data-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }

        .data-table th {
            background: #f0f0f0;
            font-weight: bold;
            color: #000000;
            font-size: 7pt;
            text-transform: uppercase;
            padding: 5px 6px;
            border-bottom: 1px solid #999999;
            text-align: left;
        }

        .data-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #e5e5e5;
            color: #000000;
            text-align: left;
        }

        .data-table tfoot td {
            font-weight: bold;
            background: #f0f0f0;
            border-top: 1px solid #999999;
            border-bottom: none;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #fafafa;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        /* Badges — borders only, no background color */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000000;
            color: #000000;
        }

        .summary-label { color: #444444; }
        .summary-value { font-weight: bold; color: #000000; }

        .footer-text { font-size: 6.5pt; color: #555555; }
    </style>
</head>
<body>

    {{-- ========================================================================
        Header — Logo centered + title + period
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 2px solid #000000; margin-bottom: 10px;">
        <tr>
            <td style="text-align: center; padding-bottom: 8px;">
                <img src="{{ public_path(config('app.logo_path')) }}"
                     alt="{{ config('app.name') }}"
                     style="height: 40px; display: block; margin: 0 auto 4px auto;">
                <div style="font-size: 13pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    Hall Owner Performance Report
                </div>
                <div style="font-size: 7.5pt; color: #444444;">
                    {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                    &nbsp;|&nbsp; Generated: {{ $generatedAt->format('d M Y, H:i') }}
                    &nbsp;|&nbsp; By: {{ $generatedBy }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 1: Owner Information + Performance Summary (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;">
        <tr>
            {{-- Owner Information --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Owner Information</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="summary-label" width="45%">Business Name</td>
                        <td class="summary-value">{{ $owner->business_name }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Owner Name</td>
                        <td class="summary-value">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Email</td>
                        <td class="summary-value" style="font-size: 7pt;">{{ $owner->business_email ?? $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Phone</td>
                        <td class="summary-value">{{ $owner->business_phone }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Commercial Reg.</td>
                        <td class="summary-value">{{ $owner->commercial_registration }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Total Halls</td>
                        <td class="summary-value">{{ $halls->count() }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Verification</td>
                        <td class="summary-value">
                            <span class="badge">{{ $owner->is_verified ? 'Verified' : 'Unverified' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="summary-label">Account Status</td>
                        <td class="summary-value">
                            <span class="badge">{{ $owner->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Performance Summary --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Performance Summary</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_revenue'], 3) }}</div>
                                <div class="stat-label">Total Revenue (OMR)</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_commission'], 3) }}</div>
                                <div class="stat-label">Platform Commission (OMR)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['owner_payout'], 3) }}</div>
                                <div class="stat-label">Owner Payout (OMR)</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ $stats['total_bookings'] }}</div>
                                <div class="stat-label">Total Bookings</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 2: Booking Status Stats + Additional Stats (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;">
        <tr>
            {{-- Booking Status --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Booking Status</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ $stats['confirmed_bookings'] }}</div>
                                <div class="stat-label">Confirmed</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ $stats['completed_bookings'] }}</div>
                                <div class="stat-label">Completed</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ $stats['pending_bookings'] }}</div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ $stats['cancelled_bookings'] }}</div>
                                <div class="stat-label">Cancelled</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Additional Stats --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Additional Statistics</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_guests']) }}</div>
                                <div class="stat-label">Total Guests</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['average_booking_value'], 3) }}</div>
                                <div class="stat-label">Avg. Booking (OMR)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ $halls->where('is_active', true)->count() }}</div>
                                <div class="stat-label">Active Halls</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">
                                    @if($stats['total_revenue'] > 0)
                                        {{ number_format(($stats['total_commission'] / $stats['total_revenue']) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </div>
                                <div class="stat-label">Commission Rate</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Hall Performance Breakdown
        ======================================================================== --}}
    @if($hallPerformance->count() > 0)
        <div class="section-title" style="margin-bottom: 6px;">Hall Performance Breakdown</div>
        <table class="data-table" width="100%" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th>Hall Name</th>
                    <th class="text-center" width="18%">Total Bookings</th>
                    <th class="text-right" width="22%">Revenue (OMR)</th>
                    <th class="text-right" width="22%">Owner Payout (OMR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hallPerformance as $hall)
                    <tr>
                        <td><strong>{{ $hall['hall_name'] }}</strong></td>
                        <td class="text-center">{{ $hall['bookings_count'] }}</td>
                        <td class="text-right">{{ number_format($hall['revenue'], 3) }}</td>
                        <td class="text-right">{{ number_format($hall['payout'], 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ========================================================================
        Monthly Performance Breakdown
        ======================================================================== --}}
    @if($monthlyBreakdown->count() > 0)
        <div class="section-title" style="margin-bottom: 6px;">Monthly Performance Breakdown</div>
        <table class="data-table" width="100%" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-center" width="14%">Bookings</th>
                    <th class="text-right" width="19%">Revenue (OMR)</th>
                    <th class="text-right" width="19%">Commission (OMR)</th>
                    <th class="text-right" width="19%">Payout (OMR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyBreakdown as $month)
                    <tr>
                        <td><strong>{{ $month['month'] }}</strong></td>
                        <td class="text-center">{{ $month['bookings'] }}</td>
                        <td class="text-right">{{ number_format($month['revenue'], 3) }}</td>
                        <td class="text-right">{{ number_format($month['commission'], 3) }}</td>
                        <td class="text-right">{{ number_format($month['payout'], 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="text-center">{{ $monthlyBreakdown->sum('bookings') }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown->sum('revenue'), 3) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown->sum('commission'), 3) }}</td>
                    <td class="text-right">{{ number_format($monthlyBreakdown->sum('payout'), 3) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ========================================================================
        Recent Bookings
        ======================================================================== --}}
    @if($bookings->count() > 0)
        <div class="section-title" style="margin-bottom: 6px;">
            Recent Bookings (Latest 50)
        </div>
        <table class="data-table" width="100%" style="margin-bottom: 6px;">
            <thead>
                <tr>
                    <th width="14%">Booking #</th>
                    <th>Hall</th>
                    <th width="13%">Date</th>
                    <th>Customer</th>
                    <th class="text-center" width="13%">Status</th>
                    <th class="text-right" width="14%">Amount (OMR)</th>
                    <th class="text-right" width="14%">Payout (OMR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings->take(50) as $booking)
                    @php
                        $hallName = $booking->hall->name ?? 'N/A';
                        if (is_array($hallName)) {
                            $hallName = $hallName['en'] ?? $hallName['ar'] ?? 'N/A';
                        }
                        $status = is_object($booking->status) ? $booking->status->value : $booking->status;
                    @endphp
                    <tr>
                        <td>{{ $booking->booking_number }}</td>
                        <td>{{ $hallName }}</td>
                        <td>{{ $booking->booking_date->format('d M Y') }}</td>
                        <td>{{ $booking->customer_name ?? $booking->user->name }}</td>
                        <td class="text-center">
                            <span class="badge">{{ ucfirst($status) }}</span>
                        </td>
                        <td class="text-right">{{ number_format($booking->total_amount, 3) }}</td>
                        <td class="text-right">{{ number_format($booking->owner_payout, 3) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($bookings->count() > 50)
            <p style="text-align: center; font-size: 6.5pt; color: #555555; font-style: italic; margin-bottom: 10px;">
                Showing 50 of {{ $bookings->count() }} total bookings
            </p>
        @endif
    @endif

    {{-- ========================================================================
        Footer
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 8px; border-top: 1px solid #cccccc; padding-top: 6px;">
        <tr>
            <td width="60%" style="vertical-align: middle;" class="footer-text">
                <strong>{{ config('app.name') }}</strong> &mdash; Hall Booking Management System
            </td>
            <td width="40%" style="vertical-align: middle; text-align: right;" class="footer-text">
                This report is confidential. &copy; {{ date('Y') }} All Rights Reserved.
            </td>
        </tr>
    </table>

</body>
</html>
