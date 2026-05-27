<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
    <style>
        body {
            background-color: #1a535c; 
            color: white;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .container { padding: 2rem; max-width: 600px; }
        
        h1 { 
            font-size: 3.5rem; 
            margin-bottom: 2rem; 
            text-transform: uppercase;
            font-weight: 800;
            line-height: 1;
        }

        /* NEW: Reschedule Badge Style */
        .reschedule-badge {
            background: #f39c12;
            color: #1a535c;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 900;
            font-size: 1.2rem;
            display: inline-block;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .instruction-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Changes border color if it's a reschedule */
        .reschedule-border {
            border: 2px solid #f39c12;
            background: rgba(243, 156, 18, 0.1);
        }
        
        .guard-note {
            color: #ff6b6b; 
            font-weight: bold;
            font-size: 1.6rem;
            display: block;
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
        }
        
        .finish-btn {
            display: inline-block;
            margin-top: 50px;
            padding: 20px 80px;
            background: #4ecdc4;
            color: #1a535c;
            text-decoration: none;
            border-radius: 40px;
            font-weight: bold;
            font-size: 1.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        
        @if(session('isReschedule'))
            <div class="reschedule-badge">Reschedule Confirmed</div>
            <h1 style="color: #f39c12;">SCHEDULE UPDATED!</h1>
        @else
            <h1>BOOKING CONFIRMED!</h1>
        @endif

        <div class="instruction-box {{ session('isReschedule') ? 'reschedule-border' : '' }}">
            <p style="font-size: 1.4rem;">
                {{ session('isReschedule') 
                    ? 'Your new schedule has been reserved.' 
                    : 'Please refer to the receipt sent to your email address.' }}
            </p>
            
            <span class="guard-note">SHOW THIS TO THE GUARD ON DUTY FOR VERIFICATION.</span>
        </div>

        <a href="{{ url('/kiosk') }}" class="finish-btn">FINISH</a>
    </div>

    <script>
        // Resets the kiosk to the start page after 20 seconds
        setTimeout(function() {
            window.location.href = "{{ url('/kiosk') }}";
        }, 20000);
    </script>
</body>
</html>