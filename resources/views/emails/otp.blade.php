<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a202c;
            font-size: 24px;
            font-weight: 700;
        }
        .header .brand {
            color: #ec4899;
            font-weight: 800;
        }
        .code-box {
            background: linear-gradient(135deg, #fdf2f8 0%, #f3e8ff 100%);
            border: 2px dashed #ec4899;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: 700;
            color: #1a202c;
            letter-spacing: 8px;
            background: #ffffff;
            padding: 15px 30px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .info {
            color: #4a5568;
            line-height: 1.6;
            text-align: center;
        }
        .info strong {
            color: #1a202c;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 <span class="brand">Fitcoin</span></h1>
            <p style="color: #4a5568; font-size: 16px;">
                @if($type === 'password_reset')
                    Password Reset Verification Code
                @else
                    Your OTP Verification Code
                @endif
            </p>
        </div>

        @if($firstName)
            <p class="info" style="font-size: 16px;">Hello <strong>{{ $firstName }}</strong>,</p>
        @endif

        <p class="info" style="margin-top: 10px;">
            @if($type === 'password_reset')
                Use the verification code below to reset your password.
            @else
                Use the verification code below to complete your registration.
            @endif
            This code will expire in <strong>15 minutes</strong>.
        </p>

        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>

        <p class="info" style="font-size: 14px; color: #718096;">
            If you didn't request this code, please ignore this email.
        </p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Fitcoin. All rights reserved.</p>
            <p style="font-size: 12px; color: #a0aec0;">
                This is an automated message, please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>