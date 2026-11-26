<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your B-READY Account Credentials</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .intro {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .credentials-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .credential-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #dcfce7;
            font-size: 13px;
        }
        .credential-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .credential-label {
            color: #166534;
            font-weight: 600;
        }
        .credential-value {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            color: #0f172a;
            background-color: #ffffff;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #d1fae5;
        }
        .cta-wrapper {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            background-color: #059669;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 14px 32px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.3);
        }
        .notice-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 12px;
            color: #92400e;
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 28px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>B-READY Platform</h1>
            <p>Disaster Preparedness &amp; School Safety Learning</p>
        </div>

        <div class="content">
            <div class="greeting">Hello, {{ $user->name }}!</div>
            <p class="intro">
                A <strong>{{ $user->role === 'trainer' ? 'DRR Trainer' : ($user->role === 'admin' ? 'Super Administrator' : 'Teacher') }}</strong> account has been created for you on the <strong>B-READY</strong> Disaster Preparedness Platform by the Super Administrator.
            </p>

            <div class="credentials-box">
                <div class="credential-row">
                    <span class="credential-label">Role:</span>
                    <span class="credential-value" style="font-family: inherit; font-size: 12px; text-transform: uppercase;">{{ $user->role === 'trainer' ? 'DRR Trainer' : ($user->role === 'admin' ? 'Super Admin' : 'Teacher') }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Login Email:</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Temporary Password:</span>
                    <span class="credential-value">{{ $plainPassword }}</span>
                </div>
            </div>

            <div class="notice-box">
                <strong>🔒 Mandatory Password Change:</strong> For your security, this temporary password will allow you to sign in for the first time, and you will be immediately prompted to set your own permanent password.
            </div>

            <div class="cta-wrapper">
                <a href="{{ $loginUrl }}" class="btn">Log In to B-READY &rarr;</a>
            </div>

            <p style="font-size: 12px; color: #64748b; text-align: center; margin-top: 20px;">
                Direct link: <a href="{{ $loginUrl }}" style="color: #059669; word-break: break-all;">{{ $loginUrl }}</a>
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} B-READY Disaster Preparedness &bull; Ramon Aboitiz Foundation Inc. (RAFI)<br>
            This is an automated notification. Please do not reply directly to this email.
        </div>
    </div>
</body>
</html>
