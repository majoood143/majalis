<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payout Receipt - {{ $payout_number }}</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            width: 210mm; /* A4 width */
            height: 297mm; /* A4 height */
            margin: 0 auto;
        }

        /* Page Container */
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 25px 30px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Section */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 4px solid #2563eb;
            padding-bottom: 25px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .company-logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .company-tagline {
            font-size: 10px;
            color: #4b5563;
            font-style: italic;
            margin-bottom: 10px;
        }

        .company-info {
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }

        .receipt-title {
            font-size: 26px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .receipt-subtitle {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 12px;
        }

        .receipt-number {
            font-size: 14px;
            color: #2563eb;
            font-weight: bold;
            background: #dbeafe;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .receipt-date {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 22px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 12px;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Section Styles */
        .section {
            margin-bottom: 28px;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 18px;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 60px;
            height: 2px;
            background: #2563eb;
        }

        /* Two Column Layout */
        .two-columns {
            display: table;
            width: 100%;
        }

        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        .column:last-child {
            padding-right: 0;
            padding-left: 15px;
        }

        /* Info Row */
        .info-row {
            margin-bottom: 10px;
        }

        .info-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .info-value {
            font-size: 12px;
            color: #333;
            font-weight: 500;
        }

        /* Financial Table */
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .financial-table th,
        .financial-table td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .financial-table th {
            background-color: #f8fafc;
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .financial-table td {
            font-size: 12px;
        }

        .financial-table .amount {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
            font-weight: 500;
        }

        .financial-table .total-row {
            background-color: #eff6ff;
            font-weight: bold;
        }

        .financial-table .total-row td {
            border-bottom: 2px solid #2563eb;
            font-size: 14px;
        }

        .financial-table .sub-total {
            background-color: #f1f5f9;
        }

        .financial-table .deduction {
            color: #dc2626;
        }

        .financial-table .addition {
            color: #16a34a;
        }

        /* Net Payout Highlight */
        .net-payout-box {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .net-payout-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.1) 0%, transparent 40%);
        }

        .net-payout-label {
            font-size: 13px;
            text-transform: uppercase;
            opacity: 0.9;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .net-payout-amount {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .net-payout-currency {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Payment Details Box */
        .payment-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        }

        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .payment-row:last-child {
            margin-bottom: 0;
        }

        .payment-label {
            display: table-cell;
            width: 40%;
            font-size: 11px;
            color: #666;
            font-weight: 600;
        }

        .payment-value {
            display: table-cell;
            width: 60%;
            font-size: 12px;
            color: #333;
            font-weight: 500;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
        }

        .footer-text {
            font-size: 11px;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .footer-note {
            font-size: 10px;
            color: #999;
            font-style: italic;
            margin-bottom: 4px;
        }

        .footer-links {
            font-size: 10px;
            color: #4b5563;
            margin-top: 10px;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(37, 99, 235, 0.06);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
            letter-spacing: 5px;
        }

        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .container {
                box-shadow: none;
                padding: 0;
            }
        }

        /* Responsive adjustments */
        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">PAID</div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-logo">MAJALIS</div>
                <div class="company-name">Majalis Hall Booking Platform</div>
                <div class="company-tagline">Connecting Halls & Celebrations Across Oman</div>
                <div class="company-info">
                    {{ $company_address }}<br>
                    Phone: {{ $company_phone }}<br>
                    Email: {{ $company_email }}<br>
                    VAT ID: {{ $vat_id ?? 'N/A' }}
                </div>
            </div>
            <div class="header-right">
                <div class="receipt-title">Payout Receipt</div>
                <div class="receipt-subtitle">Financial Settlement Statement</div>
                <div class="receipt-number">REF: {{ $payout_number }}</div>
                <div class="receipt-date">Generated: {{ $generated_at }}</div>
                <div class="status-badge status-completed">{{ $status }}</div>
            </div>
        </div>

        <!-- Owner Information -->
        <div class="section">
            <div class="section-title">Recipient Information</div>
            <div class="two-columns">
                <div class="column">
                    <div class="info-row">
                        <div class="info-label">Owner Name</div>
                        <div class="info-value">{{ $owner_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Business Name</div>
                        <div class="info-value">{{ $business_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $owner_email }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $owner_phone ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="column">
                    <div class="info-row">
                        <div class="info-label">Bank</div>
                        <div class="info-value">{{ $bank_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Account Number</div>
                        <div class="info-value">{{ $bank_account }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">IBAN</div>
                        <div class="info-value">{{ $iban ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Swift Code</div>
                        <div class="info-value">{{ $swift_code ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Information -->
        <div class="section">
            <div class="section-title">Payout Period</div>
            <div class="two-columns">
                <div class="column">
                    <div class="info-row">
                        <div class="info-label">Period</div>
                        <div class="info-value">{{ $period_string }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Start Date</div>
                        {{-- <div class="info-value">{{ $start_date }}</div> --}}
                    </div>
                    <div class="info-row">
                        <div class="info-label">End Date</div>
                        {{-- <div class="info-value">{{ $end_date }}</div> --}}
                    </div>
                </div>
                <div class="column">
                    <div class="info-row">
                        <div class="info-label">Total Bookings</div>
                        <div class="info-value">{{ $bookings_count }} booking(s)</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Gross Revenue</div>
                        <div class="info-value">{{ $currency }} {{ number_format($gross_revenue, 3) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Commission Rate</div>
                        <div class="info-value">{{ $commission_rate }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Breakdown -->
        <div class="section">
            <div class="section-title">Financial Breakdown</div>
            <table class="financial-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Amount ({{ $currency }})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gross Revenue from Bookings</td>
                        <td class="amount">{{ number_format($gross_revenue, 3) }}</td>
                    </tr>
                    <tr>
                        <td class="deduction">Platform Commission ({{ $commission_rate }}%)</td>
                        <td class="amount deduction">- {{ number_format($commission_amount, 3) }}</td>
                    </tr>
                    @if((float) str_replace(',', '', $adjustments) != 0)
                    <tr>
                        <td class="{{ (float) str_replace(',', '', $adjustments) > 0 ? 'addition' : 'deduction' }}">
                            Adjustments
                        </td>
                        <td class="amount {{ (float) str_replace(',', '', $adjustments) > 0 ? 'addition' : 'deduction' }}">
                            {{ (float) str_replace(',', '', $adjustments) > 0 ? '+ ' : '- ' }}{{ number_format(ltrim(str_replace('-', '', $adjustments), '-'), 3) }}
                        </td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>Net Payout</strong></td>
                        <td class="amount"><strong>{{ number_format($net_payout, 3) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Net Payout Highlight Box -->
        <div class="net-payout-box">
            <div class="net-payout-label">Total Amount Paid</div>
            <div class="net-payout-amount">{{ number_format($net_payout, 3) }}</div>
            <div class="net-payout-currency">{{ $currency }} (Omani Rial)</div>
        </div>

        <!-- Payment Details -->
        <div class="section">
            <div class="section-title">Payment Details</div>
            <div class="payment-box">
                <div class="payment-row">
                    <div class="payment-label">Payment Method:</div>
                    <div class="payment-value">{{ $payment_method }}</div>
                </div>
                <div class="payment-row">
                    <div class="payment-label">Transaction Reference:</div>
                    <div class="payment-value">{{ $transaction_reference }}</div>
                </div>
                <div class="payment-row">
                    <div class="payment-label">Completed Date:</div>
                    <div class="payment-value">{{ $completed_at }}</div>
                </div>
                <div class="payment-row">
                    <div class="payment-label">Processed By:</div>
                    <div class="payment-value">{{ $processed_by }}</div>
                </div>
                {{-- @if($notes)
                <div class="payment-row">
                    <div class="payment-label">Notes:</div>
                    <div class="payment-value">{{ $notes }}</div>
                </div>
                @endif --}}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">
                Thank you for being a valued partner of {{ $company_name }}!
            </div>
            <div class="footer-note">
                This is a computer-generated document. No signature is required.
            </div>
            <div class="footer-note">
                For any queries, please contact us at {{ $company_email }}
            </div>
            <div class="footer-links">
                Website: majalis.om | Customer Support: support@majalis.om
            </div>
        </div>
    </div>
</body>
</html>
