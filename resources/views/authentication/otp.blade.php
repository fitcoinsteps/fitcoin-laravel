<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h1 style="color: #1a202c; text-align: center;">🔐 Fitcoin</h1>
        <p style="color: #4a5568; font-size: 16px; text-align: center;">
            @if($type === 'password_reset')
                Password Reset Verification Code
            @else
                Your OTP Verification Code
            @endif
        </p>

        @if($firstName)
            <p style="color: #4a5568;">Hello <strong>{{ $firstName }}</strong>,</p>
        @endif

        <p style="color: #4a5568;">
            @if($type === 'password_reset')
                Use the verification code below to reset your password.
            @else
                Use the verification code below to complete your registration.
            @endif
            This code will expire in <strong>15 minutes</strong>.
        </p>

        <div style="background: linear-gradient(135deg, #fdf2f8 0%, #f3e8ff 100%); border: 2px dashed #ec4899; border-radius: 12px; padding: 20px; text-align: center; margin: 30px 0;">
            <span style="font-size: 36px; font-weight: 700; letter-spacing: 8px;">{{ $code }}</span>
        </div>

        <p style="color: #718096; font-size: 14px;">If you didn't request this code, please ignore this email.</p>

        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; color: #718096; font-size: 14px;">
            <p>&copy; {{ date('Y') }} Fitcoin. All rights reserved.</p>
        </div>
    </div>
</body>
</html>