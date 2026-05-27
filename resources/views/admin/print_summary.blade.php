<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2a8b9d;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1c4e63;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .card {
            border: 1px solid #ddd;
            padding: 15px;
            width: 48%;
            text-align: center;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .card h3 {
            margin: 0 0 5px;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }
        .card p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #2a8b9d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        th {
            background-color: #2a8b9d;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .total-row {
            background-color: #f0f9fb;
        }
        .total-label {
            font-size: 16px;
            color: #1c4e63;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .total-amount {
            font-size: 18px;
            color: #1c4e63;
            border-bottom: 4px double #1c4e63;
        }
        .print-btn {
            display: block;
            width: 200px;
            margin: 0 0 30px auto;
            padding: 10px 20px;
            background-color: #1c4e63;
            color: white;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .status-paid { color: #2a8b9d; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }

        .proof-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .card { border: 1px solid #ccc; }
        }
    </style>
</head>
<body>

    @php
        $calculatedTotalVolume = count($bookings ?? []);
        $calculatedGrandTotal = collect($bookings ?? [])->sum('price');
        $calculatedCollectedRevenue = collect($bookings ?? [])
            ->whereIn('status', ['paid', 'PAID', 'approved', 'APPROVED'])
            ->sum('price');
    @endphp

    <button onclick="window.print()" class="print-btn no-print">
        🖨️ Print Output
    </button>

    <div class="header">
        <h1>Island Central Mactan Pickleball</h1>
        <p>Official Booking Summary Report</p>
        
        <p style="font-weight: bold; color: #1c4e63; margin-top: 10px;">
            Period: {{ $period ?? 'All Time' }}
        </p>
        
        <p style="font-size: 11px;">Report Generated: {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="summary-cards">
        <div class="card">
            <h3>Total Volume</h3>
            <p>{{ $calculatedTotalVolume }} Bookings</p>
        </div>
        <div class="card">
            <h3>Collected Revenue</h3>
            <p>₱{{ number_format($calculatedCollectedRevenue, 2) }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Player Name</th>
                <th>Method</th>
                <th>Status</th> 
                <th>Ref #</th> 
                <th>Receipt</th> 
                <th class="text-right">Equip.</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings ?? [] as $booking)
            <tr>
                <td>{{ \Carbon\Carbon::parse($booking->reservation_date)->format('M d, Y') }}</td>
                <td style="color: #666;">
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}
                </td>
                <td class="font-bold">{{ $booking->player_name }}</td>
                <td>{{ strtoupper($booking->payment_method) }}</td>
                
                <td>
                    @if(in_array(strtolower($booking->status), ['paid', 'approved']))
                        <span class="status-paid">{{ strtoupper($booking->status) }}</span>
                    @else
                        <span class="status-pending">{{ strtoupper($booking->status) }}</span>
                    @endif
                </td>

                <td style="font-family: monospace;">{{ $booking->reference_number ?? 'N/A' }}</td>

                <td>
                    @if($booking->proof_of_payment)
                        <img src="{{ asset('storage/' . $booking->proof_of_payment) }}" class="proof-img">
                    @else
                        <span style="color: #ccc; font-size: 10px;">No Image</span>
                    @endif
                </td>

                <td class="text-right">₱{{ number_format($booking->rent_equipment ?? 0, 2) }}</td>
                <td class="text-right font-bold">₱{{ number_format($booking->price, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 40px; color: #888;">
                    No booking records found for the selected period.
                </td>
            </tr>
            @endforelse
        </tbody>
        
        @if(count($bookings ?? []) > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="text-right font-bold total-label" style="padding: 20px;">
                    Grand Total (Filtered):
                </td>
                <td class="text-right font-bold total-amount" style="padding: 20px;">
                    ₱{{ number_format($calculatedGrandTotal, 2) }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div style="text-align: center; font-size: 11px; color: #aaa; margin-top: 60px; border-top: 1px solid #eee; padding-top: 20px;">
        <p>END OF SUMMARY REPORT &bull; SYSTEM GENERATED BY ICM ADMIN</p>
    </div>

</body>
</html>