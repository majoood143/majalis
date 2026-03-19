<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>All Hall Owners Report</title>
    <style>
        * { margin: 5; padding: 0; }

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

        .summary-label { color: #444444; }
        .summary-value { font-weight: bold; color: #000000; }

        /* Badge — border only */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000000;
            color: #000000;
        }

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
                    All Hall Owners Performance Report
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
        Row 1: Overall Statistics + Financial Summary (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;">
        <tr>
            {{-- Overall Statistics --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Overall Statistics</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="33%">
                            <div class="stat-box">
                                <div class="stat-value">{{ $overallStats['total_owners'] }}</div>
                                <div class="stat-label">Total Owners</div>
                            </div>
                        </td>
                        <td width="33%">
                            <div class="stat-box">
                                <div class="stat-value">{{ $overallStats['verified_owners'] }}</div>
                                <div class="stat-label">Verified</div>
                            </div>
                        </td>
                        <td width="33%">
                            <div class="stat-box">
                                <div class="stat-value">{{ $overallStats['active_owners'] }}</div>
                                <div class="stat-label">Active</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ $overallStats['total_halls'] }}</div>
                                <div class="stat-label">Total Halls</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ $overallStats['active_halls'] }}</div>
                                <div class="stat-label">Active Halls</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ $overallStats['total_bookings'] }}</div>
                                <div class="stat-label">Total Bookings</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Financial Summary --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Financial Summary</div>
                <table width="100%" cellpadding="0" cellspacing="2" style="margin-bottom: 4px;">
                    <tr>
                        <td width="33%">
                            <div class="stat-box">
                                <div class="stat-value" style="font-size: 10pt;">{{ number_format($overallStats['total_revenue'], 3) }}</div>
                                <div class="stat-label">Total Revenue (OMR)</div>
                            </div>
                        </td>
                        <td width="33%">
                            <div class="stat-box">
                                <div class="stat-value" style="font-size: 10pt;">{{ number_format($overallStats['total_commission'], 3) }}</div>
                                <div class="stat-label">Commission (OMR)</div>
                            </div>
                        </td>
                        <td width="33%">
                            <div class="stat-box">
                                <div class="stat-value" style="font-size: 10pt;">{{ number_format($overallStats['total_payout'], 3) }}</div>
                                <div class="stat-label">Owner Payouts (OMR)</div>
                            </div>
                        </td>
                    </tr>
                </table>
                {{-- Key Insights inline --}}
                <table class="data-table" width="100%" style="margin-top: 4px;">
                    <tr>
                        <td class="summary-label">Verification Rate</td>
                        <td class="text-right summary-value">{{ $overallStats['total_owners'] > 0 ? number_format(($overallStats['verified_owners'] / $overallStats['total_owners']) * 100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Active Rate</td>
                        <td class="text-right summary-value">{{ $overallStats['total_owners'] > 0 ? number_format(($overallStats['active_owners'] / $overallStats['total_owners']) * 100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Avg. Halls per Owner</td>
                        <td class="text-right summary-value">{{ $overallStats['total_owners'] > 0 ? number_format($overallStats['total_halls'] / $overallStats['total_owners'], 1) : 0 }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Avg. Bookings per Hall</td>
                        <td class="text-right summary-value">{{ $overallStats['total_halls'] > 0 ? number_format($overallStats['total_bookings'] / $overallStats['total_halls'], 1) : 0 }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Commission Rate</td>
                        <td class="text-right summary-value">{{ $overallStats['total_revenue'] > 0 ? number_format(($overallStats['total_commission'] / $overallStats['total_revenue']) * 100, 1) : 0 }}%</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Top 10 Performing Owners
        ======================================================================== --}}
    @if($topOwners->count() > 0)
        <div class="section-title" style="margin-bottom: 6px;">Top 10 Performing Owners (By Revenue)</div>
        <table class="data-table" width="100%" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Business Name</th>
                    <th>Owner</th>
                    <th class="text-center" width="8%">Halls</th>
                    <th class="text-center" width="10%">Bookings</th>
                    <th class="text-right" width="14%">Revenue (OMR)</th>
                    <th class="text-right" width="14%">Commission (OMR)</th>
                    <th class="text-right" width="14%">Payout (OMR)</th>
                    <th class="text-center" width="10%">Status</th>
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
                        <td class="text-right">{{ number_format($owner['revenue'], 3) }}</td>
                        <td class="text-right">{{ number_format($owner['commission'], 3) }}</td>
                        <td class="text-right">{{ number_format($owner['payout'], 3) }}</td>
                        <td class="text-center">
                            <span class="badge">
                                @if($owner['is_verified'] && $owner['is_active']) Active
                                @elseif($owner['is_verified']) Verified
                                @else Pending
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ========================================================================
        All Owners Performance Details
        ======================================================================== --}}
    <div class="section-title" style="margin-bottom: 6px;">All Owners Performance Details</div>
    @if($ownerPerformance->count() > 0)
        <table class="data-table" width="100%" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th>Business Name</th>
                    <th>Owner Name</th>
                    <th class="text-center" width="7%">Halls</th>
                    <th class="text-center" width="9%">Bookings</th>
                    <th class="text-right" width="14%">Revenue (OMR)</th>
                    <th class="text-right" width="14%">Commission (OMR)</th>
                    <th class="text-right" width="14%">Payout (OMR)</th>
                    <th class="text-center" width="9%">Verified</th>
                    <th class="text-center" width="8%">Active</th>
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
                        <td class="text-right">{{ number_format($owner['revenue'], 3) }}</td>
                        <td class="text-right">{{ number_format($owner['commission'], 3) }}</td>
                        <td class="text-right">{{ number_format($owner['payout'], 3) }}</td>
                        <td class="text-center"><span class="badge">{{ $owner['is_verified'] ? 'Yes' : 'No' }}</span></td>
                        <td class="text-center"><span class="badge">{{ $owner['is_active'] ? 'Yes' : 'No' }}</span></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">TOTAL</td>
                    <td class="text-center">{{ $ownerPerformance->sum('bookings_count') }}</td>
                    <td class="text-right">{{ number_format($ownerPerformance->sum('revenue'), 3) }}</td>
                    <td class="text-right">{{ number_format($ownerPerformance->sum('commission'), 3) }}</td>
                    <td class="text-right">{{ number_format($ownerPerformance->sum('payout'), 3) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    @else
        <p style="text-align: center; font-size: 7.5pt; color: #555555; font-style: italic; margin-bottom: 10px;">
            No owner performance data available for the selected period.
        </p>
    @endif

    {{-- ========================================================================
        Footer
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 8px; border-top: 1px solid #cccccc; padding-top: 6px;">
        <tr>
            <td width="60%" style="vertical-align: middle;" class="footer-text">
                This report was automatically generated by {{ config('app.name') }}.
                Confidential &mdash; For Internal Use Only.
            </td>
            <td width="40%" style="vertical-align: middle; text-align: right;" class="footer-text">
                Report ID: ALL-{{ now()->format('YmdHis') }} &nbsp;|&nbsp; &copy; {{ date('Y') }} All Rights Reserved.
            </td>
        </tr>
    </table>

</body>
</html>
