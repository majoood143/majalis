<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3490dc; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; }
        .button { background: #3490dc; color: white; padding: 12px 24px; text-decoration: none; display: inline-block; margin: 10px 0; }
        .footer { text-align: center; margin-top: 20px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Booking Confirmation</h1>
        </div>
        
        <div class="content">
            <h2>Dear {{ $booking->customer_name }},</h2>
            
            <p>Your booking has been created successfully!</p>
            
            <h3>Booking Details:</h3>
            <ul>
                <li><strong>Booking Number:</strong> {{ $booking->booking_number }}</li>
                <li><strong>Hall:</strong> {{ $booking->hall->name }}</li>
                <li><strong>Date:</strong> {{ $booking->booking_date->format('d M Y') }}</li>
                <li><strong>Time Slot:</strong> {{ ucfirst(str_replace('_', ' ', $booking->time_slot)) }}</li>
                <li><strong>Guests:</strong> {{ $booking->number_of_guests }}</li>
                <li><strong>Total Amount:</strong> {{ number_format($booking->total_amount, 3) }} OMR</li>
            </ul>
            
            <p>Please complete your payment to confirm the booking.</p>
            
            <a href="{{ route('booking.show', $booking->id) }}" class="button">View Booking</a>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Majalis - Hall Booking Platform</p>
            <p>Sultanate of Oman</p>
        </div>
    </div>
</body>
</html>