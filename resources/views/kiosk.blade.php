<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickleball Reservation</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #1a4f63; 
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .island-logo {
            height: 60px; 
            margin-bottom: 30px;
            border-radius: 10px; 
        }

        .logo-circle {
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background-color: #153c4d;
            border: 4px solid #23657d;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h1 {
            font-size: 5rem;
            font-weight: 900;
            letter-spacing: 10px;
            margin: 0;
            text-transform: uppercase;
        }

        h2 {
            font-size: 1.5rem;
            letter-spacing: 12px;
            color: #6bb4cc;
            margin-bottom: 50px;
            text-transform: uppercase;
        }

        .start-btn {
            background: transparent;
            border: 2px solid #55a8c2;
            color: white;
            padding: 18px 45px;
            font-size: 1.3rem;
            font-weight: bold;
            border-radius: 50px;
            text-decoration: none;
            letter-spacing: 2px;
            box-shadow: 0 0 20px rgba(85, 168, 194, 0.5);
            transition: 0.3s;
        }

        .start-btn:hover {
            background: rgba(85, 168, 194, 0.2);
        }

        .footer {
            margin-top: 60px;
            font-size: 0.8rem;
            letter-spacing: 4px;
            color: #55879a;
        }
    </style>
</head>
<body>

    <img src="{{ asset('storage/island-central-logo.png.png') }}" alt="Island Centralllll" class="island-logo">

    <div class="logo-circle">
        <img src="{{ asset('storage/pickleball_logo.jpeg') }}" alt="Pickleball Logoooo">
    </div>

    <h1>Pickleball</h1>
    <h2>Reservation Kiosk</h2>

    <a href="{{ route('reserve.store') }}" class="start-btn">↖ TAP SCREEN TO BEGINNNN</a>

    <div class="footer">
        ISLAND CENTRAL MACTAN • MEPZ ECOZONE
    </div>

</body>
</html>