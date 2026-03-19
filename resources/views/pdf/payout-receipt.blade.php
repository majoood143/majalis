<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payout Receipt - {{ $payout_number }}</title>
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
            margin-bottom: 8px;
        }

        /* Data tables */
        .data-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }

        .data-table th {
            background: #f0f0f0;
            font-weight: bold;
            color: #000000;
            font-size: 7pt;
            text-transform: uppercase;
            padding: 5px 8px;
            border-bottom: 1px solid #999999;
            text-align: left;
        }

        .data-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e5e5;
            color: #000000;
            text-align: left;
        }

        .data-table .total-row td {
            font-weight: bold;
            font-size: 9pt;
            border-top: 1.5px solid #000000;
            border-bottom: none;
            background: #f0f0f0;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        .info-label { color: #444444; font-size: 7pt; text-transform: uppercase; margin-bottom: 2px; }
        .info-value { font-weight: 500; color: #000000; font-size: 8pt; margin-bottom: 8px; }

        /* Status badge — border only */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1.5px solid #000000;
            color: #000000;
        }

        .footer-text { font-size: 6.5pt; color: #555555; }
    </style>
</head>
<body>

    {{-- ========================================================================
        Header — Logo (left) + Receipt title/ref (right)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 2px solid #000000; margin-bottom: 12px; padding-bottom: 8px;">
        <tr>
            {{-- Left: Logo + company info --}}
            <td width="50%" style="vertical-align: top;">
                <img src="{{ public_path(config('app.logo_path')) }}"
                     alt="{{ $company_name }}"
                     style="height: 40px; display: block; margin-bottom: 6px;">
                <div style="font-size: 7pt; color: #444444; line-height: 1.6;">
                    {{ $company_address }}<br>
                    Phone: {{ $company_phone }}<br>
                    Email: {{ $company_email }}<br>
                    VAT ID: {{ $vat_id ?? 'N/A' }}
                </div>
            </td>

            {{-- Right: Receipt title + ref + status --}}
            <td width="50%" style="vertical-align: top; text-align: right;">
                <div style="font-size: 16pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    Payout Receipt
                </div>
                <div style="font-size: 7.5pt; color: #444444; margin-bottom: 4px;">
                    Financial Settlement Statement
                </div>
                <div style="font-size: 8pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    REF: {{ $payout_number }}
                </div>
                <div style="font-size: 7pt; color: #444444; margin-bottom: 6px;">
                    Generated: {{ $generated_at }}
                </div>
                <span class="status-badge">{{ $status }}</span>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 1: Recipient Information + Payout Period (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            {{-- Recipient Information --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Recipient Information</div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="50%" style="vertical-align: top;">
                            <div class="info-label">Owner Name</div>
                            <div class="info-value">{{ $owner_name }}</div>
                            <div class="info-label">Business Name</div>
                            <div class="info-value">{{ $business_name ?? 'N/A' }}</div>
                            <div class="info-label">Email</div>
                            <div class="info-value" style="font-size: 7pt;">{{ $owner_email }}</div>
                            <div class="info-label">Phone</div>
                            <div class="info-value">{{ $owner_phone ?? 'N/A' }}</div>
                        </td>
                        <td width="50%" style="vertical-align: top; padding-left: 8px;">
                            <div class="info-label">Bank</div>
                            <div class="info-value">{{ $bank_name }}</div>
                            <div class="info-label">Account Number</div>
                            <div class="info-value">{{ $bank_account }}</div>
                            <div class="info-label">IBAN</div>
                            <div class="info-value">{{ $iban ?? 'N/A' }}</div>
                            <div class="info-label">Swift Code</div>
                            <div class="info-value">{{ $swift_code ?? 'N/A' }}</div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Payout Period --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Payout Period</div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="50%" style="vertical-align: top;">
                            <div class="info-label">Period</div>
                            <div class="info-value">{{ $period_string }}</div>
                            <div class="info-label">Total Bookings</div>
                            <div class="info-value">{{ $bookings_count }} booking(s)</div>
                        </td>
                        <td width="50%" style="vertical-align: top; padding-left: 8px;">
                            <div class="info-label">Gross Revenue</div>
                            <div class="info-value">{{ $currency }} {{ number_format($gross_revenue, 3) }}</div>
                            <div class="info-label">Commission Rate</div>
                            <div class="info-value">{{ $commission_rate }}%</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Row 2: Financial Breakdown + Payment Details (side by side)
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            {{-- Financial Breakdown --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Financial Breakdown</div>
                <table class="data-table" width="100%">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-right" width="35%">Amount ({{ $currency }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Gross Revenue from Bookings</td>
                            <td class="text-right">{{ number_format($gross_revenue, 3) }}</td>
                        </tr>
                        <tr>
                            <td>Platform Commission ({{ $commission_rate }}%)</td>
                            <td class="text-right">- {{ number_format($commission_amount, 3) }}</td>
                        </tr>
                        @if ((float) str_replace(',', '', $adjustments) != 0)
                            @php $adj = (float) str_replace(',', '', $adjustments); @endphp
                            <tr>
                                <td>Adjustments</td>
                                <td class="text-right">{{ $adj > 0 ? '+ ' : '- ' }}{{ number_format(abs($adj), 3) }}</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>Net Payout</td>
                            <td class="text-right">{{ number_format($net_payout, 3) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td width="2%"></td>

            {{-- Payment Details --}}
            <td width="49%" style="vertical-align: top;">
                <div class="section-title">Payment Details</div>
                <table class="data-table" width="100%">
                    <tr>
                        <td class="info-label" width="45%" style="padding: 5px 8px;">Payment Method</td>
                        <td style="padding: 5px 8px; font-weight: 500;">{{ $payment_method }}</td>
                    </tr>
                    <tr>
                        <td class="info-label" style="padding: 5px 8px;">Transaction Ref.</td>
                        <td style="padding: 5px 8px; font-size: 7pt; font-weight: 500;">{{ $transaction_reference }}</td>
                    </tr>
                    <tr>
                        <td class="info-label" style="padding: 5px 8px;">Completed Date</td>
                        <td style="padding: 5px 8px; font-weight: 500;">{{ $completed_at }}</td>
                    </tr>
                    <tr>
                        <td class="info-label" style="padding: 5px 8px;">Processed By</td>
                        <td style="padding: 5px 8px; font-weight: 500;">{{ $processed_by }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Net Payout Highlight Box
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            <td style="border: 1.5px solid #000000; background: #f0f0f0; text-align: center; padding: 14px;">
                <div style="font-size: 7.5pt; color: #444444; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">
                    Total Amount Paid
                </div>
                <div style="font-size: 22pt; font-weight: bold; color: #000000; margin-bottom: 2px;">
                    {{ number_format($net_payout, 3) }}
                </div>
                <div style="font-size: 8pt; color: #444444;">
                    {{ $currency }} (Omani Rial)
                </div>
            </td>
        </tr>
    </table>

    {{-- ========================================================================
        Footer
        ======================================================================== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid #cccccc; padding-top: 6px;">
        <tr>
            <td width="60%" style="vertical-align: middle;" class="footer-text">
                Thank you for being a valued partner of {{ $company_name }}.<br>
                This is a computer-generated document. No signature is required.
            </td>
            <td width="40%" style="vertical-align: middle; text-align: right;" class="footer-text">
                {{ $company_email }} &nbsp;|&nbsp; majalis.om
            </td>
        </tr>
    </table>

</body>
</html>
