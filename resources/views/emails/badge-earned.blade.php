<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge Earned</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .badge-showcase {
            text-align: center;
            margin: 30px 0;
            padding: 30px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
        }
        .badge-icon {
            font-size: 64px;
            margin-bottom: 15px;
            display: block;
        }
        .badge-name {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin: 10px 0;
        }
        .badge-description {
            font-size: 16px;
            color: #6b7280;
            margin: 10px 0;
        }
        .badge-tier {
            display: inline-block;
            padding: 6px 16px;
            background-color: #fbbf24;
            color: #78350f;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
        }
        .account-info {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .account-info p {
            margin: 8px 0;
            font-size: 14px;
            color: #4b5563;
        }
        .account-info strong {
            color: #1f2937;
        }
        .cta-button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            opacity: 0.9;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
        .congratulations {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        {{-- Header --}}
        <div class="header">
            <h1>🎉 Congratulations!</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.95;">You've earned a new achievement badge</p>
        </div>

        {{-- Content --}}
        <div class="content">
            <p class="congratulations">
                Hi <strong>{{ $user->name }}</strong>,
            </p>
            
            <p>
                Great news! Your trading account has earned a new verification badge on TheTradeVisor.
            </p>

            {{-- Badge Showcase --}}
            <div class="badge-showcase">
                <span class="badge-icon">{!! $badge->badge_icon !!}</span>
                <div class="badge-name">{{ $badge->badge_name }}</div>
                <div class="badge-description">{{ $badge->badge_description }}</div>
                <span class="badge-tier">Tier {{ $badge->badge_tier }}</span>
            </div>

            {{-- Account Info --}}
            <div class="account-info">
                <p><strong>Account:</strong> {{ $account->broker_name }} - {{ $account->account_number }}</p>
                <p><strong>Earned on:</strong> {{ $badge->earned_at->format('F j, Y \a\t g:i A') }}</p>
            </div>

            <div class="divider"></div>

            <p style="font-size: 15px; color: #4b5563;">
                This badge is now displayed on your public trading profile and helps build trust with other traders viewing your performance.
            </p>

            @if($profileUrl)
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $profileUrl }}?utm_source=email&utm_medium=badge_notification&utm_campaign=badge_earned&utm_content={{ $badge->badge_type }}" class="cta-button">
                        View Your Public Profile
                    </a>
                </div>
            @endif

            <p style="font-size: 14px; color: #6b7280; margin-top: 30px;">
                Keep up the great work! Continue trading to unlock more badges and achievements.
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                <strong>TheTradeVisor</strong><br>
                Professional Trading Analytics Platform
            </p>
            <p style="margin: 10px 0;">
                <a href="{{ route('landing') }}?utm_source=email&utm_medium=badge_notification&utm_campaign=badge_earned">Visit Website</a> • 
                <a href="{{ route('dashboard') }}?utm_source=email&utm_medium=badge_notification&utm_campaign=badge_earned">Dashboard</a> • 
                <a href="{{ route('accounts.index') }}?utm_source=email&utm_medium=badge_notification&utm_campaign=badge_earned">My Accounts</a>
            </p>
            <p style="margin: 15px 0 0 0; font-size: 12px; color: #9ca3af;">
                You're receiving this email because you have an active trading account on TheTradeVisor.<br>
                © {{ date('Y') }} TheTradeVisor. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
