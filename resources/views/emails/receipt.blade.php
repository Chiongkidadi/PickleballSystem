<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #444; line-height: 1.6; }
        .invoice-box { max-width: 600px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; }
        
        .header { background: {{ isset($isReschedule) && $isReschedule ? '#d69e2e' : '#1a202c' }}; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        
        .content { padding: 20px; border: 1px solid #eee; border-top: none; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table td { padding: 10px; vertical-align: top; }
        table tr.heading td { background: #f7fafc; border-bottom: 2px solid #edf2f7; font-weight: bold; color: #2d3748; }
        table tr.item td { border-bottom: 1px solid #eee; }
        table tr.total td { font-weight: bold; border-top: 2px solid #eee; font-size: 18px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #718096; }
        
        .reschedule-status {
            background: #fefcbf;
            color: #b7791f;
            border: 1px solid #f6e05e;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .btn-reschedule {
            display: block;
            text-align: center;
            background: #2a8b9d;
            color: white !important;
            padding: 12px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            @if(isset($isReschedule) && $isReschedule)
                <h1 style="margin:0; letter-spacing: 2px;">RESCHEDULE RECEIPT</h1>
            @else
                <h1 style="margin:0; letter-spacing: 2px;">OFFICIAL RECEIPT</h1>
            @endif
            <p style="margin:5px 0 0 0;">Island Central Mactan Pickleball</p>
        </div>

        <div class="content">
            
            @php
                // 1. Get the actual equipment price saved in the database
                $totalRentalCost = (float)($reservation->rent_equipment ?? 0);

                // 2. Calculate the Court Cost (Total Paid - Rental Cost)
                $courtCost = max(0, (float)($reservation->price ?? 0) - $totalRentalCost);
                
                // 3. Get the description text (e.g., "1 Set (2 Paddles, 1 Ball)")
                $rentalDescription = $reservation->paddle_rental && $reservation->paddle_rental !== 'None' 
                                    ? $reservation->paddle_rental 
                                    : 'Equipment Rental';
            @endphp

            @if(isset($isReschedule) && $isReschedule)
                <div style="text-align: center;">
                    <div class="reschedule-status">
                        Confirmed Schedule Update
                    </div>
                </div>
            @endif

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

                <tr class="item">
                    <td>
                        <strong>Pickleball Court Reservation</strong><br>
                        <small style="color:#718096;">
                            {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M d, Y') }} | {{ $reservation->start_time }} - {{ $reservation->end_time ?? 'Finish' }}
                        </small>
                    </td>
                    <td style="text-align: right;">
                        ₱{{ number_format($courtCost, 2) }}
                    </td>
                </tr>

                {{-- Only show this row if equipment was actually rented --}}
                @if($totalRentalCost > 0)
                <tr class="item">
                    <td>
                        <strong>Add-ons: Equipment Rental</strong><br>
                        <small style="color:#718096;">
                            {{ $rentalDescription }}
                        </small>
                    </td>
                    <td style="text-align: right;">
                        ₱{{ number_format($totalRentalCost, 2) }}
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

            @if(!isset($isReschedule) || !$isReschedule)
            <div style="margin-top: 20px; text-align: center;">
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #4a5568;">Need to change your time slot?</p>
                <a href="{{ $rescheduleUrl ?? '#' }}" class="btn-reschedule">RESCHEDULE BOOKING</a>
                <p style="margin-top: 8px; font-size: 11px; color: #a0aec0;">* Link valid for 3 days from the time of booking.</p>
            </div>
            @endif

            <div style="margin-top:30px; border-top:1px solid #eee; padding-top:15px; font-size: 13px; color: #4a5568;">
                <p><strong>Reminders:</strong></p>
                <ul>
                    @if(isset($isReschedule) && $isReschedule)
                        <li style="color: #b7791f; font-weight: bold;">NOTICE: This is an updated schedule. Previous bookings are void.</li>
                    @endif
                    <li>Please arrive 10 minutes before your schedule.</li>
                    <li>Present this email at the counter upon arrival.</li>
                    <li>Please ensure you are wearing proper court shoes.</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            Island Central Mactan Mall, Lapu-Lapu City, Cebu<br>
            <em>Thank you for booking with us!</em>
        </div>
    </div>
</body>
</html>