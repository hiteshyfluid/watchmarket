<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111; line-height: 1.5;">
    <p>Hello,</p>
    <p>Your OTP for password reset is:</p>
    <p style="font-size: 24px; font-weight: 700; letter-spacing: 2px;">{{ $otp }}</p>
    <p>This OTP is valid for {{ $expiryMinutes }} minutes.</p>
    <p>If you did not request this, please ignore this email.</p>
</body>
</html>

