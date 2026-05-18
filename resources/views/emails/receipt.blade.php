<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #444; line-height: 1.6; }
        .invoice-box { max-width: 600px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; }
        .header { background: #1a202c; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; border: 1px solid #eee; border-top: none; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table td { padding: 10px; vertical-align: top; }
        table tr.heading td { background: #f7fafc; border-bottom: 2px solid #edf2f7; font-weight: bold; color: #2d3748; }
        table tr.item td { border-bottom: 1px solid #eee; }
        table tr.total td { font-weight: bold; border-top: 2px solid #eee; font-size: 18px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #718096; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h1 style="margin:0; letter-spacing: 2px;">OFFICIAL RECEIPT</h1>
            <p style="margin:5px 0 0 0;">Island Central Mactan Pickleball</p>
        </div>

        <div class="content">
            <table cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                <tr>
                    <td>
                        <strong>Booking ID:</strong> #{{ str_pad($reservation->id, 5, '0', STR_PAD_LEFT) }}<br>
                        <strong>Date issued:</strong> {{ date('F d, Y') }}
                    </td>
                    <td style="text-align: right;">
                        <strong>Customer:</strong><br>
                        {{ $reservation->player_name }}
                    </td>
                </tr>
            </table>

            <table cellpadding="0" cellspacing="0">
                <tr class="heading">
                    <td>Description</td>
                    <td style="text-align: right;">Price</td>
                </tr>

                {{-- Row 1: The Court Rental --}}
                <tr class="item">
                    <td>
                        <strong>Pickleball Court Reservation</strong><br>
                        <small style="color:#718096;">
                            {{ $reservation->reservation_date }} | {{ $reservation->start_time }} - {{ $reservation->end_time }}
                        </small>
                    </td>
                    <td style="text-align: right;">
                        {{-- Subtracting rental fee from total to show base court price --}}
                        ₱{{ number_format(($reservation->price - ($reservation->rent_equipment ?? 0)), 2) }}
                    </td>
                </tr>

                {{-- Row 2: The Add-ons (Equipment) --}}
                {{-- Ensure rent_equipment is checked as a number --}}
                @if((float)$reservation->rent_equipment > 0)
                <tr class="item">
                    <td>
                        <strong>Add-ons: Equipment Rental</strong><br>
                        <small style="color:#718096;">Paddle & Ball Rental Service</small>
                    </td>
                    <td style="text-align: right;">
                        ₱{{ number_format($reservation->rent_equipment, 2) }}
                    </td>
                </tr>
                @endif

                <tr class="total">
                    <td style="text-align: right;">Total Amount Paid:</td>
                    <td style="text-align: right;">₱{{ number_format($reservation->price, 2) }}</td>
                </tr>
            </table>

            <div style="margin-top: 20px; padding: 10px; background: #f9fafb; border-radius: 5px;">
                <p style="margin:0; font-size: 14px;"><strong>Payment Method:</strong> {{ strtoupper($reservation->payment_method) }}</p>
            </div>

            <div style="margin-top:30px; border-top:1px solid #eee; padding-top:15px; font-size: 13px; color: #4a5568;">
                <p><strong>Reminders:</strong></p>
                <ul>
                    <li>Please arrive 10 minutes before your schedule.</li>
                    <li>Present this email at the counter upon arrival.</li>
                    <li>Please ensure you are wearing proper court shoes.</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            Island Central Mactan Mall, Lapu-Lapu City, Cebu<br>..
            <em>Thank you for booking with us!</em>
        </div>
    </div>
</body>
</html>