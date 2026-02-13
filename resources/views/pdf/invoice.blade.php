<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
    <title>Invoice - {{ $booking->booking_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .total { font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MAJALIS</h1>
        <h2>Booking Invoice</h2>
    </div>

    <div class="details">
        <p><strong>Booking Number:</strong> {{ $booking->booking_number }}</p>
        <p><strong>Date:</strong> {{ $booking->booking_date->format('d M Y') }}</p>
        <p><strong>Customer:</strong> {{ $booking->customer_name }}</p>
        <p><strong>Hall:</strong> {{ $hall->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Hall Booking - {{ $booking->time_slot }}</td>
                <td>1</td>
                <td>{{ number_format($booking->hall_price, 3) }} OMR</td>
                <td>{{ number_format($booking->hall_price, 3) }} OMR</td>
            </tr>
            @foreach($extraServices as $service)
            <tr>
                <td>{{ $service->pivot->service_name }}</td>
                <td>{{ $service->pivot->quantity }}</td>
                <td>{{ number_format($service->pivot->unit_price, 3) }} OMR</td>
                <td>{{ number_format($service->pivot->total_price, 3) }} OMR</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right;">Subtotal:</td>
                <td>{{ number_format($booking->subtotal, 3) }} OMR</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Platform Fee:</td>
                <td>{{ number_format($booking->platform_fee, 3) }} OMR</td>
            </tr>
            <tr class="total">
                <td colspan="3" style="text-align: right;">TOTAL:</td>
                <td>{{ number_format($booking->total_amount, 3) }} OMR</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <p>Thank you for booking with Majalis!</p>
        <p>For inquiries: info@majalis.om | +968 24 123456</p>
    </div>
</body>
</html>
