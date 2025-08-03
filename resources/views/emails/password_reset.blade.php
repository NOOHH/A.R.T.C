<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #8e2de2 0%, #c84f8f 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.8;
            color: #555;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #8e2de2 0%, #c84f8f 100%);
            color: white;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(142, 45, 226, 0.3);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .fallback-url {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #6c757d;
        }
        .security-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            color: #856404;
        }
        .security-note h3 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .logo {
            width: 50px;
            height: 50px;
            margin-bottom: 15px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .reset-button {
                padding: 12px 25px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
            <p>Ascendo Review and Training Center</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello {{ $userName }},
            </div>
            
            <div class="message">
                You recently requested to reset your password for your A.R.T.C account ({{ $userEmail }}). 
                Click the button below to reset your password.
            </div>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">Reset My Password</a>
            </div>
            
            <div class="message">
                If the button above doesn't work, you can copy and paste the following link into your browser:
            </div>
            
            <div class="fallback-url">
                {{ $resetUrl }}
            </div>
            
            <div class="security-note">
                <h3>🔒 Security Notice</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This password reset link will expire in <strong>1 hour</strong> for security reasons</li>
                    <li>If you didn't request this password reset, please ignore this email</li>
                    <li>Your current password will remain unchanged until you create a new one</li>
                    <li>For security, never share this link with anyone</li>
                </ul>
            </div>
            
            <div class="message">
                If you have any questions or need assistance, please contact our support team.
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Ascendo Review and Training Center</strong></p>
            <p>Review Smarter. Learn Better. Succeed Faster.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #adb5bd;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
