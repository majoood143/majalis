<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
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
        }

        /* Page Container */
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Section */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
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

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .receipt-number {
            font-size: 14px;
            color: #2563eb;
            font-weight: bold;
        }

        .receipt-date {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
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
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 15px;
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
            margin-bottom: 8px;
        }

        .info-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
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
            margin-top: 10px;
        }

        .financial-table th,
        .financial-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .financial-table th {
            background-color: #f9fafb;
            font-size: 11px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
        }

        .financial-table td {
            font-size: 12px;
        }

        .financial-table .amount {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .financial-table .total-row {
            background-color: #eff6ff;
            font-weight: bold;
        }

        .financial-table .total-row td {
            border-bottom: 2px solid #2563eb;
            font-size: 14px;
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
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 25px 0;
        }

        .net-payout-label {
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .net-payout-amount {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .net-payout-currency {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Payment Details Box */
        .payment-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
        }

        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .payment-row:last-child {
            margin-bottom: 0;
        }

        .payment-label {
            display: table-cell;
            width: 40%;
            font-size: 11px;
            color: #666;
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
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer-text {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }

        .footer-note {
            font-size: 9px;
            color: #999;
            font-style: italic;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(37, 99, 235, 0.05);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
        }

        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
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
                <div class="company-name">{{ $company_name }}</div>
                <div class="company-info">
                    {{ $company_address }}<br>
                    {{ $company_phone }}<br>
                    {{ $company_email }}
                </div>
            </div>
            <div class="header-right">
                <div class="receipt-title">PAYOUT RECEIPT</div>
                <div class="receipt-number">{{ $payout_number }}</div>
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
                        <div class="info-value">{{ $business_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $owner_email }}</div>
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
                </div>
                <div class="column">
                    <div class="info-row">
                        <div class="info-label">Total Bookings</div>
                        <div class="info-value">{{ $bookings_count }} booking(s)</div>
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
                        <td class="amount">{{ $gross_revenue }}</td>
                    </tr>
                    <tr>
                        <td class="deduction">Platform Commission ({{ $commission_rate }}%)</td>
                        <td class="amount deduction">- {{ $commission_amount }}</td>
                    </tr>
                    @if((float) str_replace(',', '', $adjustments) != 0)
                    <tr>
                        <td class="{{ (float) str_replace(',', '', $adjustments) > 0 ? 'addition' : 'deduction' }}">
                            Adjustments
                        </td>
                        <td class="amount {{ (float) str_replace(',', '', $adjustments) > 0 ? 'addition' : 'deduction' }}">
                            {{ (float) str_replace(',', '', $adjustments) > 0 ? '+ ' : '- ' }}{{ ltrim($adjustments, '-') }}
                        </td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Net Payout</td>
                        <td class="amount">{{ $net_payout }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Net Payout Highlight Box -->
        <div class="net-payout-box">
            <div class="net-payout-label">Total Amount Paid</div>
            <div class="net-payout-amount">{{ $net_payout }}</div>
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
        </div>
    </div>
</body>
</html>
