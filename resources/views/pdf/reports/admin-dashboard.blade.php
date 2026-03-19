<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('admin.reports.pdf.title') }} - {{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
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

        /* Tables */
        table { border-collapse: collapse; }

        .data-table { width: 100%; font-size: 7.5pt; }

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

        .text-right { text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; }
        .text-center { text-align: center; }

        /* Commission table rows */
        .summary-label { color: #444444; }
        .summary-value { font-weight: bold; color: #000000; }

        /* Footer */
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
                    {{ __('admin.reports.pdf.title') }}
                </div>
                <div style="font-size: 7.5pt; color: #444444;">
                    {{ __('admin.reports.pdf.period') }}: {{ $startDate }} &mdash; {{ $endDate }}
                    &nbsp;|&nbsp; {{ __('admin.reports.pdf.generated') }}: {{ $generatedAt }}
                    &nbsp;|&nbsp; {{ __('admin.reports.pdf.by') }}: {{ $generatedBy }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 1: Overview Stats + Booking Stats (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;">
        <tr>
            {{-- Overview Stats --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('admin.reports.pdf.overview') }}</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_revenue'], 3) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.total_revenue') }} (OMR)</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_commission'], 3) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.platform_commission') }} (OMR)</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['total_owner_payout'], 3) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.owner_payouts') }} (OMR)</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['pending_payout_amount'], 3) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.pending_payouts') }} (OMR)</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Booking Stats --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('admin.reports.pdf.booking_stats') }}</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_bookings']) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.total_bookings') }}</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['confirmed_bookings']) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.confirmed') }}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['completed_bookings']) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.completed') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['cancelled_bookings']) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.cancelled') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 2: Commission Summary + Platform Stats (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;">
        <tr>
            {{-- Commission Summary --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('admin.reports.pdf.commission_summary') }}</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="summary-label">{{ __('admin.reports.pdf.gross_revenue') }}</td>
                        <td class="text-right summary-value">{{ number_format($commissionReport['total_revenue'], 3) }} OMR</td>
                    </tr>
                    <tr>
                        <td class="summary-label">{{ __('admin.reports.pdf.total_commission') }}</td>
                        <td class="text-right summary-value">{{ number_format($commissionReport['total_commission'], 3) }} OMR</td>
                    </tr>
                    <tr>
                        <td class="summary-label">{{ __('admin.reports.pdf.avg_commission_rate') }}</td>
                        <td class="text-right summary-value">{{ $commissionReport['commission_rate'] }}%</td>
                    </tr>
                    <tr>
                        <td class="summary-label">{{ __('admin.reports.pdf.bookings_processed') }}</td>
                        <td class="text-right summary-value">{{ $commissionReport['bookings_count'] }}</td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Platform Stats --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">{{ __('admin.reports.pdf.platform_stats') }}</div>
                <table width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_halls']) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.active_halls') }}</div>
                            </div>
                        </td>
                        <td width="50%">
                            <div class="stat-box">
                                <div class="stat-value">{{ number_format($stats['total_owners']) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.verified_owners') }}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.total_customers') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="margin-top: 2px;">
                                <div class="stat-value">{{ $stats['pending_payouts'] }}</div>
                                <div class="stat-label">{{ __('admin.reports.pdf.pending_payout_count') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Top Performing Halls
        ======================================================================== --}}
    @if($topHalls->isNotEmpty())
        <div class="section-title" style="margin-bottom: 6px;">{{ __('admin.reports.pdf.top_halls') }}</div>
        <table class="data-table" width="100%" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>{{ __('admin.reports.pdf.hall_name') }}</th>
                    <th class="text-center" width="15%">{{ __('admin.reports.pdf.bookings') }}</th>
                    <th class="text-right" width="22%">{{ __('admin.reports.pdf.revenue') }}</th>
                    <th class="text-right" width="22%">{{ __('admin.reports.pdf.avg_booking') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topHalls as $index => $hall)
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
                        <td class="text-right">{{ number_format((float) $hall->avg_booking_value, 3) }} OMR</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ========================================================================
        Top Performing Owners
        ======================================================================== --}}
    @if($topOwners->isNotEmpty())
        <div class="section-title" style="margin-bottom: 6px;">{{ __('admin.reports.pdf.top_owners') }}</div>
        <table class="data-table" width="100%" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>{{ __('admin.reports.pdf.owner_name') }}</th>
                    <th>{{ __('admin.reports.pdf.business') }}</th>
                    <th class="text-center" width="10%">{{ __('admin.reports.pdf.halls') }}</th>
                    <th class="text-center" width="12%">{{ __('admin.reports.pdf.bookings') }}</th>
                    <th class="text-right" width="18%">{{ __('admin.reports.pdf.revenue') }}</th>
                    <th class="text-right" width="18%">{{ __('admin.reports.pdf.commission') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topOwners as $index => $owner)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $owner->name }}</td>
                        <td>{{ $owner->business_name ?? '—' }}</td>
                        <td class="text-center">{{ $owner->halls_count }}</td>
                        <td class="text-center">{{ $owner->bookings_count }}</td>
                        <td class="text-right">{{ number_format((float) $owner->total_revenue, 3) }} OMR</td>
                        <td class="text-right">{{ number_format((float) $owner->total_commission, 3) }} OMR</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ========================================================================
        Footer
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 8px; border-top: 1px solid #cccccc; padding-top: 6px;">
        <tr>
            <td width="60%" style="vertical-align: middle;" class="footer-text">
                {{ __('admin.reports.pdf.footer', ['app' => config('app.name')]) }}
            </td>
            <td width="40%" style="vertical-align: middle; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};" class="footer-text">
                {{ __('admin.reports.pdf.confidential') }}
            </td>
        </tr>
    </table>

</body>
</html>
