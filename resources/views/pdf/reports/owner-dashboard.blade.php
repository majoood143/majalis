<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('owner_report.reports.pdf.title') }} - {{ ($owner ?? null)?->name ?? __('owner_report.reports.pdf.owner') }}</title>
    <style>
        * { margin: 0; padding: 0; }

        body {
            font-family: tajawal, 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #000000;
            line-height: 1.4;
            background: #ffffff;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
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
            font-size: 13pt;
            font-weight: bold;
            color: #000000;
        }

        .stat-label {
            font-size: 6.5pt;
            color: #444444;
            margin-top: 2px;
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
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .data-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #e5e5e5;
            color: #000000;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .data-table tfoot td {
            font-weight: bold;
            background: #f0f0f0;
            border-top: 1px solid #999999;
            border-bottom: none;
        }

        .text-right  { text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; }
        .text-center { text-align: center; }

        .summary-label { color: #444444; }
        .summary-value { font-weight: bold; color: #000000; }

        .footer-text { font-size: 6.5pt; color: #555555; }
    </style>
</head>
<body>

    {{-- ========================================================================
        Header — Logo centered + owner info + period
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 2px solid #000000; margin-bottom: 10px;">
        <tr>
            <td style="text-align: center; padding-bottom: 8px;">
                <img src="{{ public_path(config('app.logo_path')) }}"
                     alt="{{ config('app.name') }}"
                     style="height: 40px; display: block; margin: 0 auto 4px auto;">
                <div style="font-size: 13pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    {{ __('owner_report.reports.pdf.earnings_report') }}
                </div>
                @if($hallOwner)
                    <div style="font-size: 9pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                        {{ $hallOwner->business_name ?? ($owner ?? null)?->name ?? '' }}
                    </div>
                @endif
                <div style="font-size: 7.5pt; color: #444444;">
                    {{ __('owner_report.reports.pdf.period') }}: {{ $startDate }} &mdash; {{ $endDate }}
                    &nbsp;|&nbsp; {{ __('owner_report.reports.pdf.generated') }}: {{ $generatedAt }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 1: Owner Details + Net Earnings highlight (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;">
        <tr>
            {{-- Owner Details --}}
            @if($hallOwner)
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('owner_report.reports.pdf.owner_details') }}</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="summary-label" width="40%">{{ __('owner_report.reports.pdf.owner_name') }}</td>
                        <td class="summary-value">{{ ($owner ?? null)?->name ?? __('owner_report.general.na') }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">{{ __('owner_report.reports.pdf.email') }}</td>
                        <td class="summary-value" style="font-size: 7pt;">{{ ($owner ?? null)?->email ?? __('owner_report.general.na') }}</td>
                    </tr>
                    @if($hallOwner->business_name)
                    <tr>
                        <td class="summary-label">{{ __('owner_report.reports.pdf.business') }}</td>
                        <td class="summary-value">{{ $hallOwner->business_name }}</td>
                    </tr>
                    @endif
                    @if($hallOwner->phone)
                    <tr>
                        <td class="summary-label">{{ __('owner_report.reports.pdf.phone') }}</td>
                        <td class="summary-value">{{ $hallOwner->phone }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td width="2%"></td>
            @endif

            {{-- Net Earnings Box --}}
            <td width="{{ $hallOwner ? '49%' : '100%' }}" style="vertical-align: top;">
                <div class="section-title">{{ __('owner_report.reports.pdf.net_earnings') }}</div>
                <table width="100%" cellpadding="0" cellspacing="0"
                       style="border: 1.5px solid #000000; background: #f9f9f9; text-align: center;">
                    <tr>
                        <td style="padding: 14px; text-align: center;">
                            <div style="font-size: 22pt; font-weight: bold; color: #000000;">
                                {{ number_format($stats['total_earnings'], 3) }} OMR
                            </div>
                            <div style="font-size: 7.5pt; color: #444444; margin-top: 4px;">
                                {{ __('owner_report.reports.pdf.net_earnings') }}
                                &nbsp;&mdash;&nbsp;
                                {{ $startDate }} &mdash; {{ $endDate }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 2: Financial Overview + Booking Stats (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;">
        <tr>
            {{-- Financial Overview --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('owner_report.reports.pdf.financial_overview') }}</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_revenue'], 3) }}</div>
                                <div class="stat-label">{{ __('owner_report.reports.pdf.gross_revenue') }} (OMR)</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['platform_commission'], 3) }}</div>
                                <div class="stat-label">{{ __('owner_report.reports.pdf.platform_fee') }} (OMR)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['total_earnings'], 3) }}</div>
                                <div class="stat-label">{{ __('owner_report.reports.pdf.net_earnings') }} (OMR)</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['pending_payouts'], 3) }}</div>
                                <div class="stat-label">{{ __('owner_report.reports.pdf.pending_payout') }} (OMR)</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Booking Stats --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('owner_report.reports.pdf.booking_stats') }}</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_bookings']) }}</div>
                                <div class="stat-label">{{ __('owner_report.reports.pdf.total_bookings') }}</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['confirmed_bookings']) }}</div>
                                <div class="stat-label">{{ __('owner_report.reports.pdf.confirmed') }}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['completed_bookings']) }}</div>
                                <div class="stat-label">{{ __('owner_report.reports.pdf.completed') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['cancelled_bookings']) }}</div>
                                <div class="stat-label">{{ __('owner_report.reports.pdf.cancelled') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 3: Additional Stats + Monthly Comparison (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;">
        <tr>
            {{-- Additional Stats --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('owner_report.reports.pdf.additional_stats') }}</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="summary-label">{{ __('owner_report.reports.pdf.total_guests') }}</td>
                        <td class="text-right summary-value">{{ number_format($stats['total_guests']) }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">{{ __('owner_report.reports.pdf.avg_booking_value') }}</td>
                        <td class="text-right summary-value">{{ number_format($stats['average_booking_value'], 3) }} OMR</td>
                    </tr>
                    <tr>
                        <td class="summary-label">{{ __('owner_report.reports.pdf.total_paid_out') }}</td>
                        <td class="text-right summary-value">{{ number_format($stats['completed_payouts'], 3) }} OMR</td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Monthly Comparison --}}
            <td width="49%" style="vertical-align: top;">
                @if(!empty($comparison))
                    <div class="section-title">{{ __('owner_report.reports.pdf.monthly_comparison') }}</div>
                    <table width="100%" cellpadding="0" cellspacing="2">
                        <tr>
                            <td width="50%">
                                <div class="stat-box">
                                    <div class="stat-value">
                                        {{ $comparison['revenue_change'] >= 0 ? '+' : '' }}{{ $comparison['revenue_change'] }}%
                                    </div>
                                    <div class="stat-label">{{ __('owner_report.reports.pdf.earnings_change') }}</div>
                                </div>
                            </td>
                            <td width="50%">
                                <div class="stat-box">
                                    <div class="stat-value">
                                        {{ $comparison['bookings_change'] >= 0 ? '+' : '' }}{{ $comparison['bookings_change'] }}%
                                    </div>
                                    <div class="stat-label">{{ __('owner_report.reports.pdf.bookings_change') }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                @else
                    <div class="section-title">{{ __('owner_report.reports.pdf.hall_summary') }}</div>
                    <table width="100%" cellpadding="0" cellspacing="2">
                        <tr>
                            <td width="50%">
                                <div class="stat-box">
                                    <div class="stat-value">{{ $stats['total_halls'] }}</div>
                                    <div class="stat-label">{{ __('owner_report.reports.pdf.total_halls') }}</div>
                                </div>
                            </td>
                            <td width="50%">
                                <div class="stat-box">
                                    <div class="stat-value">{{ $stats['active_halls'] }}</div>
                                    <div class="stat-label">{{ __('owner_report.reports.pdf.active_halls') }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="stat-box" style="margin-top: 2px;">
                                    @php $avgPerHall = $stats['total_halls'] > 0 ? $stats['total_bookings'] / $stats['total_halls'] : 0; @endphp
                                    <div class="stat-value">{{ number_format($avgPerHall, 1) }}</div>
                                    <div class="stat-label">{{ __('owner_report.reports.pdf.avg_bookings_per_hall') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="stat-box" style="margin-top: 2px;">
                                    @php $avgEarningsPerHall = $stats['total_halls'] > 0 ? $stats['total_earnings'] / $stats['total_halls'] : 0; @endphp
                                    <div class="stat-value">{{ number_format($avgEarningsPerHall, 3) }}</div>
                                    <div class="stat-label">{{ __('owner_report.reports.pdf.avg_earnings_per_hall') }} (OMR)</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Hall Performance Table (page 2 if needed)
        ======================================================================== --}}
    @if($hallPerformance->isNotEmpty())
        <div class="section-title" style="margin-bottom: 6px;">{{ __('owner_report.reports.pdf.hall_performance') }}</div>
        <table class="data-table" width="100%" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>{{ __('owner_report.reports.pdf.hall_name') }}</th>
                    <th class="text-center" width="15%">{{ __('owner_report.reports.pdf.bookings') }}</th>
                    <th class="text-right" width="22%">{{ __('owner_report.reports.pdf.revenue') }}</th>
                    <th class="text-right" width="22%">{{ __('owner_report.reports.pdf.avg_booking') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hallPerformance as $index => $hall)
                    @php
                        $hallName = is_array($hall->name)
                            ? ($hall->name[app()->getLocale()] ?? $hall->name['en'] ?? '')
                            : $hall->name;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $hallName }}</td>
                        <td class="text-center">{{ $hall->bookings_count }}</td>
                        <td class="text-right">{{ number_format((float) $hall->total_revenue, 3) }} OMR</td>
                        <td class="text-right">{{ number_format((float) ($hall->avg_booking_value ?? 0), 3) }} OMR</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">{{ __('owner_report.reports.pdf.total') }}</td>
                    <td class="text-center">{{ $hallPerformance->sum('bookings_count') }}</td>
                    <td class="text-right">{{ number_format((float) $hallPerformance->sum('total_revenue'), 3) }} OMR</td>
                    <td class="text-right">—</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Hall Summary (only when comparison was shown above, to avoid duplication) --}}
    @if(!empty($comparison))
        <div class="section-title" style="margin-bottom: 6px;">{{ __('owner_report.reports.pdf.hall_summary') }}</div>
        <table width="100%" cellpadding="0" cellspacing="2" style="margin-bottom: 10px;">
            <tr>
                <td width="25%">
                    <div class="stat-box">
                        <div class="stat-value">{{ $stats['total_halls'] }}</div>
                        <div class="stat-label">{{ __('owner_report.reports.pdf.total_halls') }}</div>
                    </div>
                </td>
                <td width="25%">
                    <div class="stat-box">
                        <div class="stat-value">{{ $stats['active_halls'] }}</div>
                        <div class="stat-label">{{ __('owner_report.reports.pdf.active_halls') }}</div>
                    </div>
                </td>
                <td width="25%">
                    <div class="stat-box">
                        @php $avgPerHall = $stats['total_halls'] > 0 ? $stats['total_bookings'] / $stats['total_halls'] : 0; @endphp
                        <div class="stat-value">{{ number_format($avgPerHall, 1) }}</div>
                        <div class="stat-label">{{ __('owner_report.reports.pdf.avg_bookings_per_hall') }}</div>
                    </div>
                </td>
                <td width="25%">
                    <div class="stat-box">
                        @php $avgEarningsPerHall = $stats['total_halls'] > 0 ? $stats['total_earnings'] / $stats['total_halls'] : 0; @endphp
                        <div class="stat-value">{{ number_format($avgEarningsPerHall, 3) }}</div>
                        <div class="stat-label">{{ __('owner_report.reports.pdf.avg_earnings_per_hall') }} (OMR)</div>
                    </div>
                </td>
            </tr>
        </table>
    @endif

    {{-- ========================================================================
        Footer
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 8px; border-top: 1px solid #cccccc; padding-top: 6px;">
        <tr>
            <td width="60%" style="vertical-align: middle;" class="footer-text">
                {{ __('owner_report.reports.pdf.footer', ['app' => config('app.name')]) }}
            </td>
            <td width="40%" style="vertical-align: middle; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};" class="footer-text">
                {{ __('owner_report.reports.pdf.thank_you') }}
            </td>
        </tr>
    </table>

</body>
</html>
