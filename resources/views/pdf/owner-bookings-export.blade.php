<!DOCTYPE html>
<html dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}" lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('owner_booking.export.pdf_title') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'tajawal', sans-serif;
            font-size: 10px;
            line-height: 1.6;
            color: #333;
            direction: {{ $locale === 'ar' ? 'rtl' : 'ltr' }};
        }

        .header {
            background: #4f46e5;
            color: #ffffff;
            padding: 25px 30px;
            margin-bottom: 20px;
        }

        .header-inner {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: {{ $locale === 'ar' ? 'left' : 'right' }};
            font-size: 9px;
            opacity: 0.85;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 10px;
            opacity: 0.9;
        }

        /* Filter summary bar */
        .filter-bar {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            margin-bottom: 18px;
            font-size: 9px;
            color: #475569;
        }

        .filter-bar span {
            font-weight: bold;
            color: #1e293b;
        }

        .filter-sep {
            margin: 0 8px;
            color: #cbd5e1;
        }

        /* Stats grid */
        .stats-table {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
        }

        .stats-row {
            display: table-row;
        }

        .stat-cell {
            display: table-cell;
            width: 16.66%;
            padding: 4px;
        }

        .stat-box {
            border: 1px solid #e2e8f0;
            padding: 12px 8px;
            text-align: center;
            background: #ffffff;
        }

        .stat-box.earnings {
            border-top: 3px solid #059669;
        }

        .stat-box.total {
            border-top: 3px solid #4f46e5;
        }

        .stat-box.confirmed {
            border-top: 3px solid #2563eb;
        }

        .stat-box.completed {
            border-top: 3px solid #059669;
        }

        .stat-box.pending {
            border-top: 3px solid #d97706;
        }

        .stat-box.cancelled {
            border-top: 3px solid #dc2626;
        }

        .stat-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
        }

        .stat-value.small {
            font-size: 12px;
        }

        /* Section heading */
        .section-heading {
            font-size: 13px;
            font-weight: bold;
            color: #4f46e5;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }

        /* Bookings table */
        table.bookings {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        table.bookings thead tr {
            background: #4f46e5;
            color: #ffffff;
        }

        table.bookings th {
            padding: 9px 8px;
            text-align: {{ $locale === 'ar' ? 'right' : 'left' }};
            border: 1px solid #6366f1;
            font-weight: bold;
        }

        table.bookings td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            text-align: {{ $locale === 'ar' ? 'right' : 'left' }};
            vertical-align: middle;
        }

        table.bookings tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .th-num, .td-num {
            text-align: center !important;
        }

        .th-money, .td-money {
            text-align: {{ $locale === 'ar' ? 'left' : 'right' }} !important;
            white-space: nowrap;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-confirmed  { background: #dbeafe; color: #1d4ed8; }
        .badge-completed  { background: #d1fae5; color: #065f46; }
        .badge-pending    { background: #fef3c7; color: #92400e; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }
        .badge-paid       { background: #d1fae5; color: #065f46; }
        .badge-partial    { background: #fef3c7; color: #92400e; }
        .badge-refunded   { background: #fce7f3; color: #9d174d; }
        .badge-default    { background: #f1f5f9; color: #475569; }

        .no-bookings {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
            font-size: 11px;
            font-style: italic;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    {{-- ─── Header ─── --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <h1>{{ __('owner_booking.export.pdf_title') }}</h1>
                <p>{{ __('owner_booking.export.owner_label') }}: {{ $ownerName }}</p>
            </div>
            <div class="header-right">
                <p>{{ __('owner_booking.export.generated_at') }}: <span dir="ltr">{{ $generatedAt->format('d/m/Y H:i') }}</span></p>
            </div>
        </div>
    </div>

    {{-- ─── Filter Summary ─── --}}
    <div class="filter-bar">
        {{-- Hall --}}
        <span>{{ __('owner_booking.export.hall_filter_label') }}:</span>
        @if ($hall)
            @php
                $hallName = $hall->name;
                if (is_array($hallName)) {
                    $hallName = $hallName[$locale] ?? $hallName['en'] ?? $hallName['ar'] ?? 'N/A';
                }
            @endphp
            {{ $hallName }}
        @else
            {{ __('owner_booking.export.all_halls_filter') }}
        @endif

        <span class="filter-sep">|</span>

        {{-- Period --}}
        <span>{{ __('owner_booking.export.period_label') }}:</span>
        @if ($dateFrom || $dateTo)
            <span dir="ltr">
                {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : '...' }}
                &nbsp;&mdash;&nbsp;
                {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : '...' }}
            </span>
        @else
            {{ __('owner_booking.export.all_period') }}
        @endif
    </div>

    {{-- ─── Stats ─── --}}
    <div class="stats-table">
        <div class="stats-row">
            <div class="stat-cell">
                <div class="stat-box total">
                    <div class="stat-label">{{ __('owner_booking.export.stats.total_bookings') }}</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-box confirmed">
                    <div class="stat-label">{{ __('owner_booking.export.stats.confirmed') }}</div>
                    <div class="stat-value">{{ $stats['confirmed'] }}</div>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-box completed">
                    <div class="stat-label">{{ __('owner_booking.export.stats.completed') }}</div>
                    <div class="stat-value">{{ $stats['completed'] }}</div>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-box pending">
                    <div class="stat-label">{{ __('owner_booking.export.stats.pending') }}</div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-box cancelled">
                    <div class="stat-label">{{ __('owner_booking.export.stats.cancelled') }}</div>
                    <div class="stat-value">{{ $stats['cancelled'] }}</div>
                </div>
            </div>
            <div class="stat-cell">
                <div class="stat-box earnings">
                    <div class="stat-label">{{ __('owner_booking.export.stats.total_earnings') }}</div>
                    <div class="stat-value small">
                        <span dir="ltr">{{ number_format($stats['total_earnings'], 3) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Bookings Table ─── --}}
    <div class="section-heading">{{ __('owner_booking.export.pdf_title') }}</div>

    @if ($bookings->isEmpty())
        <div class="no-bookings">{{ __('owner_booking.export.no_bookings') }}</div>
    @else
        <table class="bookings">
            <thead>
                <tr>
                    <th class="th-num">#</th>
                    <th>{{ __('owner_booking.export.table.booking_number') }}</th>
                    @if (!$hall)
                        <th>{{ __('owner_booking.export.table.hall') }}</th>
                    @endif
                    <th>{{ __('owner_booking.export.table.customer') }}</th>
                    <th>{{ __('owner_booking.export.table.date') }}</th>
                    <th>{{ __('owner_booking.export.table.time_slot') }}</th>
                    <th class="th-num">{{ __('owner_booking.export.table.status') }}</th>
                    <th class="th-num">{{ __('owner_booking.export.table.payment') }}</th>
                    <th class="th-money">{{ __('owner_booking.export.table.earnings') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $index => $booking)
                    @php
                        $status = is_object($booking->status) ? $booking->status->value : $booking->status;
                        $paymentStatus = is_object($booking->payment_status) ? $booking->payment_status->value : $booking->payment_status;
                        $timeSlot = $booking->time_slot;
                        $timeSlotLabel = __('owner_booking.export.time_slots.' . $timeSlot, [], $locale) ?: ucfirst($timeSlot);

                        $hallName = optional($booking->hall)->name;
                        if (is_array($hallName)) {
                            $hallName = $hallName[$locale] ?? $hallName['en'] ?? $hallName['ar'] ?? 'N/A';
                        }
                        $hallName = $hallName ?? 'N/A';

                        $customerName = $booking->customer_name ?? optional($booking->user)->name ?? 'N/A';
                    @endphp
                    <tr>
                        <td class="td-num">{{ $index + 1 }}</td>
                        <td><span dir="ltr">{{ $booking->booking_number }}</span></td>
                        @if (!$hall)
                            <td>{{ $hallName }}</td>
                        @endif
                        <td>{{ $customerName }}</td>
                        <td><span dir="ltr">{{ $booking->booking_date->format('d/m/Y') }}</span></td>
                        <td>{{ $timeSlotLabel }}</td>
                        <td class="td-num">
                            <span class="badge badge-{{ $status }}">
                                {{ __('owner_booking.export.status.' . $status, [], $locale) ?: ucfirst($status) }}
                            </span>
                        </td>
                        <td class="td-num">
                            <span class="badge badge-{{ $paymentStatus ?? 'default' }}">
                                {{ __('owner_booking.payment.' . ($paymentStatus ?? ''), [], $locale) ?: ucfirst($paymentStatus ?? '-') }}
                            </span>
                        </td>
                        <td class="td-money">
                            <span dir="ltr">{{ number_format($booking->owner_payout, 3) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ─── Footer ─── --}}
    <div class="footer">
        <p>{{ __('owner_booking.export.footer') }}</p>
        <p>&copy; {{ date('Y') }} Majalis</p>
    </div>

</body>
</html>
