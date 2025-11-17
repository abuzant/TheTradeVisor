<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Digest</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            padding: 24px;
            line-height: 1.6;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            border-radius: 8px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .logo-placeholder {
            width: 48px;
            height: 48px;
            background: #e5e7eb;
            border-radius: 4px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #9ca3af;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #111827;
        }
        .header p {
            margin: 4px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        .metric {
            background: #fafafa;
            border-radius: 6px;
            padding: 16px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .metric-value {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .metric-value.profit { color: #059669; }
        .metric-value.loss { color: #dc2626; }
        .metric-label {
            font-size: 13px;
            color: #6b7280;
        }
        .accounts-table {
            width: 100%;
            border-collapse: collapse;
        }
        .accounts-table th,
        .accounts-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .accounts-table th {
            font-weight: 600;
            background: #f9fafb;
            color: #374151;
        }
        .accounts-table tr:hover {
            background: #f9fafb;
        }
        .footer {
            margin-top: 32px;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
        .footer a {
            color: #6b7280;
            text-decoration: none;
            margin: 0 8px;
        }
        .footer a:hover {
            color: #374151;
            text-decoration: underline;
        }
        .symbol-link {
            color: #374151;
            text-decoration: none;
        }
        .symbol-link:hover {
            color: #111827;
            text-decoration: underline;
        }
        .symbol-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .symbol-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        .symbol-list strong {
            color: #111827;
        }
        .profit { color: #059669; }
        .loss { color: #dc2626; }
        .open-trades-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .open-trades-table th,
        .open-trades-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .open-trades-table th {
            font-weight: 600;
            background: #f9fafb;
            color: #374151;
        }
        .open-trades-table tr.win {
            background: #d1fae5;
        }
        .open-trades-table tr.loss {
            background: #fee2e2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-placeholder">LOGO</div>
            <h1>Performance Digest</h1>
            <p>Generated on {{ $generatedAt }}</p>
        </div>

        @foreach ($metrics as $days => $data)
            <div class="card">
                <h2 class="card-title">
                    @if ($days == 7) 📅 @elseif ($days == 30) 📊 @else 📈 @endif
                    Last {{ $days }} Days
                </h2>
                <div class="metrics-grid">
                    <div class="metric">
                        <div class="metric-value">{{ $data['totalTrades'] }}</div>
                        <div class="metric-label">Total Trades</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value">{{ $data['winningTrades'] }}</div>
                        <div class="metric-label">Winning Trades</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value {{ $data['totalProfit'] >= 0 ? 'profit' : 'loss' }}">
                            ${{ number_format(abs($data['totalProfit']), 2) }}
                        </div>
                        <div class="metric-label">Total Profit (USD)</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value">{{ $data['winRate'] }}%</div>
                        <div class="metric-label">Win Rate</div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="card">
            <h2 class="card-title">🏆 Top & Bottom Symbols</h2>
            <table class="accounts-table">
                <thead>
                    <tr>
                        <th>Top Winning Symbol</th>
                        <th>Top Losing Symbol</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @if($topSymbol)
                                <a href="https://thetradevisor.com/symbol/{{ $topSymbol['symbol'] }}" class="symbol-link" target="_blank">
                                    <strong>{{ $topSymbol['symbol'] }}</strong>
                                </a><br>
                                <span class="profit">${{ number_format($topSymbol['profit'], 2) }}</span>
                            @else
                                <strong>N/A</strong><br><span class="profit">$0.00</span>
                            @endif
                        </td>
                        <td>
                            @if($worstSymbol)
                                <a href="https://thetradevisor.com/symbol/{{ $worstSymbol['symbol'] }}" class="symbol-link" target="_blank">
                                    <strong>{{ $worstSymbol['symbol'] }}</strong>
                                </a><br>
                                <span class="loss">${{ number_format(abs($worstSymbol['profit']), 2) }}</span>
                            @else
                                <strong>N/A</strong><br><span class="loss">$0.00</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2 class="card-title">💼 Accounts</h2>
            <table class="accounts-table">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Broker</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($accounts as $account)
                        <tr>
                            <td>{{ $account->account_number }}</td>
                            <td>
                                @if($account->broker_name)
                                    <a href="https://thetradevisor.com/brokers/{{ Str::slug($account->broker_name) }}" class="symbol-link" target="_blank">
                                        {{ $account->broker_name }}
                                    </a>
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td>{{ $account->is_paused ? '⏸️ Paused' : '✅ Active' }}</td>
                            <td>{{ $account->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2 class="card-title">📌 Open Trades</h2>
            @if($openTrades->isNotEmpty())
                <table class="open-trades-table">
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>Type</th>
                            <th>Volume</th>
                            <th>Open Price</th>
                            <th>Current Price</th>
                            <th>Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openTrades as $trade)
                            <tr class="{{ $trade['profit'] >= 0 ? 'win' : 'loss' }}">
                                <td>
                                    <a href="https://thetradevisor.com/symbol/{{ $trade['symbol'] }}" class="symbol-link" target="_blank">
                                        <strong>{{ $trade['symbol'] }}</strong>
                                    </a>
                                </td>
                                <td>{{ ucfirst($trade['type']) }}</td>
                                <td>{{ $trade['volume'] }}</td>
                                <td>{{ $trade['open_price'] }}</td>
                                <td>{{ $trade['current_price'] }}</td>
                                <td class="{{ $trade['profit'] >= 0 ? 'profit' : 'loss' }}">
                                    ${{ number_format($trade['profit'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                        @php
                            $totalOpenPnL = $openTrades->sum('profit');
                        @endphp
                        <tr class="totals-row">
                            <td colspan="5" style="text-align: right; font-weight: 600;">Total Open P&L</td>
                            <td class="{{ $totalOpenPnL >= 0 ? 'profit' : 'loss' }}" style="font-weight: 700;">
                                ${{ number_format($totalOpenPnL, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p style="color: #6b7280; font-size: 14px;">No open trades at this time.</p>
            @endif
        </div>

        <div class="footer">
            <p>This is an automated digest from TheTradeVisor.</p>
            <p>
                <a href="https://thetradevisor.com">Dashboard</a> |
                <a href="https://thetradevisor.com/profile">Profile</a> |
                <a href="https://thetradevisor.com/my-digest">My Digest</a> |
                <a href="https://thetradevisor.com/terms">Terms of Service</a> |
                <a href="https://thetradevisor.com/privacy">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
