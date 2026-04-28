<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background: #6366f1; color: white; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 32px; }
        .content p { margin-bottom: 16px; font-size: 16px; }
        .otp-box { background: #f3f4f6; border: 1px dashed #cbd5e1; padding: 16px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 4px; color: #4f46e5; border-radius: 8px; margin: 24px 0; }
        .button-wrapper { text-align: center; margin: 32px 0; }
        .button { display: inline-block; padding: 12px 24px; background-color: #6366f1; color: #ffffff !important; text-decoration: none; font-weight: 600; border-radius: 6px; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 14px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistem OBE - Password Reset</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset your password. Here is your One-Time Password (OTP) and a link to create a new password.</p>
            
            <div class="otp-box">
                {{ $otp }}
            </div>

            <p>Please click the button below to proceed. You will need to enter the 6-digit OTP above on the reset page.</p>
            
            <div class="button-wrapper">
                <a href="{{ $resetLink }}" class="button">Reset My Password</a>
            </div>
            
            <p style="font-size: 14px; color: #64748b;">If you did not request a password reset, no further action is required and you can safely ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Sistem OBE. All rights reserved.
        </div>
    </div>
</body>
</html>
