<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trading Digest</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background-color:#f3f4f6; padding:24px;">
    <table width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:8px; padding:24px;">
        <tr>
            <td>
                <h1 style="font-size:20px; margin-bottom:4px;">{{ ucfirst($frequency) }} Trading Digest</h1>
                <p style="font-size:13px; color:#6b7280; margin-top:0;">Period: {{ $digest['period_start']->format('Y-m-d') }} → {{ $digest['period_end']->format('Y-m-d') }}</p>

                <hr style="border:none; border-top:1px solid #e5e7eb; margin:16px 0;" />

                <h2 style="font-size:16px; margin-bottom:8px;">Overview</h2>
                <ul style="font-size:14px; color:#111827; padding-left:18px;">
                    <li><strong>Total trades:</strong> {{ $digest['total_trades'] }}</li>
                    <li><strong>Winning trades:</strong> {{ $digest['winning_trades'] }} | <strong>Losing trades:</strong> {{ $digest['losing_trades'] }}</li>
                    <li><strong>Win rate:</strong> {{ $digest['win_rate'] }}%</li>
                    <li><strong>Total PnL:</strong> {{ number_format($digest['total_profit'], 2) }}</li>
                    <li><strong>Avg PnL per trade:</strong> {{ number_format($digest['avg_profit'], 2) }}</li>
                </ul>

                @if($digest['top_symbol'])
                    <h2 style="font-size:16px; margin-bottom:8px;">Best &amp; Worst Symbols</h2>
                    <ul style="font-size:14px; color:#111827; padding-left:18px;">
                        <li><strong>Best:</strong> {{ $digest['top_symbol']['symbol'] }} ({{ number_format($digest['top_symbol']['profit'], 2) }} across {{ $digest['top_symbol']['trades'] }} trades)</li>
                        <li><strong>Worst:</strong> {{ $digest['worst_symbol']['symbol'] }} ({{ number_format($digest['worst_symbol']['profit'], 2) }} across {{ $digest['worst_symbol']['trades'] }} trades)</li>
                    </ul>
                @endif

                <p style="font-size:12px; color:#9ca3af; margin-top:24px;">You can change your digest preferences from your profile page.</p>
            </td>
        </tr>
    </table>
</body>
</html>
