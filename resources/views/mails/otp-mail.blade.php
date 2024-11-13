<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            text-align: center;
            margin: 20px 0;
            letter-spacing: 5px;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #999999;
            padding: 20px;
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            Your {{ $type === 'verification' ? 'Verification' : 'Password Reset' }} OTP Code
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Hello,</p>

            @if ($type == 'verification')
                <p>Thank you for registering with RMS. To complete your account setup, please use the following One-Time Password (OTP) to verify your email within the next 5 minutes:</p>
            @else
                <p>Forgot your password? No problem. To reset your password, please use the following One-Time Password (OTP) within the next 5 minutes:</p>
            @endif

            <div class="otp-code">{{ $otp }}</div>
            <p>If you didn't request this, you can safely ignore this email.</p>
            <p>Thank you, <br> RMS Team</p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            © {{ date('Y') }} RMS Team. All rights reserved.
        </div>
    </div>
</body>
</html>
