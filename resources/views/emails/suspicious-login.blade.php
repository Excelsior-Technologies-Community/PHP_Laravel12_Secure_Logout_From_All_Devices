<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; border-radius: 10px; overflow: hidden; }
        .header { background: #dc2626; color: #fff; padding: 25px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 30px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .label { color: #6b7280; font-size: 14px; }
        .value { font-weight: bold; color: #111827; font-size: 14px; }
        .alert-box { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 15px; margin: 20px 0; }
        .footer { background: #f9fafb; padding: 15px; text-align: center; color: #9ca3af; font-size: 12px; }
        .btn { display: inline-block; background: #dc2626; color: #fff; padding: 12px 25px; border-radius: 8px; text-decoration: none; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ New Device Login Detected</h1>
        </div>
        <div class="body">
            <p>Someone logged into your account from a <strong>new device</strong>. If this was you, no action needed. If not, secure your account immediately.</p>

            <div class="alert-box">
                <div class="info-row">
                    <span class="label">🌐 IP Address</span>
                    <span class="value">{{ $ip }}</span>
                </div>
                <div class="info-row">
                    <span class="label">🖥️ Browser</span>
                    <span class="value">{{ $browser }}</span>
                </div>
                <div class="info-row">
                    <span class="label">💻 Platform</span>
                    <span class="value">{{ $platform }}</span>
                </div>
                <div class="info-row">
                    <span class="label">📍 Location</span>
                    <span class="value">{{ $location }}</span>
                </div>
                <div class="info-row" style="border:none">
                    <span class="label">🕒 Login Time</span>
                    <span class="value">{{ $loginTime }}</span>
                </div>
            </div>

            <p>If you did not login, please change your password immediately and logout from all devices.</p>
            <a href="{{ url('/security') }}" class="btn">Review Security Settings</a>
        </div>
        <div class="footer">
            This is an automated security alert from {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
