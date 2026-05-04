<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f4f7f8; padding: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; border-top: 4px solid #165166; max-width: 400px; margin: auto; }
        .otp { font-size: 32px; font-weight: bold; color: #2a8b9d; text-align: center; display: block; margin: 20px 0; letter-spacing: 4px; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Admin Login Verification</h3>
        <p>Your one-time password (OTP) is:</p>
        <span class="otp">{{ $otp }}</span>
        <p>This code expires in 10 minutes.</p>
    </div>
</body>
</html>