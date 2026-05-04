<!DOCTYPE html>
<html>
<head>
    <title>Admin Login Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; max-width: 500px; margin: 0 auto; text-align: center; border: 1px solid #ddd;">
        <h2 style="color: #2a8b9d; margin-top: 0;">Admin Panel Login</h2>
        <p style="color: #555; font-size: 16px;">Your one-time secure login code is:</p>
        
        <div style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1c4e63; background-color: #e0f2f7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            {{ $otpCode }}
        </div>
        
        <p style="color: #777; font-size: 14px;">This code will expire in 10 minutes. Do not share it with anyone.</p>
    </div>
</body>
</html>