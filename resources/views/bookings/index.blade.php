<!DOCTYPE html>
<html>
<head>
    <title>Pickleball Court Booking</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        .booked { background-color: #ffeaea; color: #a00; }
        .available { background-color: #f4fff4; color: #0a0; }
        .btn { padding: 6px 12px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; }
        .btn:hover { background: #0056b3; }
        .input-name { padding: 6px; width: 150px; }
    </style>
</head>
<body>
    <h1>Pickleball Court Schedule</h1>

    @if(session('success'))
        <div style="color: green; font-weight: bold; margin-bottom: 15px;">{{ session('success') }}</div>
    @endif

    @if($errors->has('conflict'))
        <div style="color: red; font-weight: bold; margin-bottom: 15px;">{{ $errors->first('conflict') }}</div>
    @endif

    <form action="{{ route('bookings.index') }}" method="GET" style="margin-bottom: 20px;">
        <label style="font-weight: bold;">Select Date: </label>
        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" style="padding: 5px;">
    </form>

    <h2>Schedule for {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}</h2>
    
    <table>
        <thead>
            <tr>
                <th>Time Slot</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedule as $slot)
                <tr class="{{ $slot['is_booked'] ? 'booked' : 'available' }}">
                    <td style="font-weight: bold;">{{ $slot['time_label'] }}</td>
                    
                    <td>
                        @if($slot['is_booked'])
                            Booked by {{ $slot['player'] }}
                        @else
                            Available
                        @endif
                    </td>
                    
                    <td>
                        @if(!$slot['is_booked'])
                            <form action="{{ route('bookings.store') }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="start_time" value="{{ $slot['start_time'] }}">
                                <input type="hidden" name="end_time" value="{{ $slot['end_time'] }}">
                                <input type="text" name="player_name" class="input-name" placeholder="Your Name" required>
                                <button type="submit" class="btn">Book</button>
                            </form>
                        @else
                            --
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>