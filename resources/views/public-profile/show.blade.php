<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Meta Tags --}}
    <title>{{ $user->public_display_mode === 'anonymous' ? 'Anonymous Trader' : '@' . $user->public_username }} - Trading Performance | TheTradeVisor</title>
    <meta name="description" content="View {{ $user->public_display_mode === 'anonymous' ? 'anonymous trader' : '@' . $user->public_username }}'s trading performance: {{ $stats['win_rate'] }}% win rate, {{ $stats['total_trades'] }} trades, {{ number_format($stats['total_profit'], 2) }} {{ $stats['currency'] }} profit.">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $user->public_display_mode === 'anonymous' ? 'Anonymous Trader' : '@' . $user->public_username }} - {{ $stats['win_rate'] }}% Win Rate">
    <meta property="og:description" content="{{ number_format($stats['total_profit'], 2) }} {{ $stats['currency'] }} profit • {{ $stats['total_trades'] }} trades • {{ $stats['win_rate'] }}% win rate">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ url()->current() }}">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $user->public_display_mode === 'anonymous' ? 'Anonymous Trader' : '@' . $user->public_username }}'s Trading Performance">
    <meta name="twitter:description" content="{{ $stats['win_rate'] }}% win rate • {{ number_format($stats['total_profit'], 2) }} {{ $stats['currency'] }} profit • {{ $stats['total_trades'] }} trades">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="bg-gray-50">
    
    {{-- Header --}}
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                        {{ substr($account->broker_name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            @if($user->public_display_mode === 'username')
                                @{{ $user->public_username }}
                            @elseif($user->public_display_mode === 'anonymous')
                                Anonymous Trader
                            @else
                                {{ $user->public_display_name }}
                            @endif
                        </h1>
                        @if($profile->custom_title)
                            <p class="text-lg text-gray-600">{{ $profile->custom_title }}</p>
                        @endif
                        <p class="text-sm text-gray-500">
                            <x-broker-name :broker="$account->broker_name" /> • 
                            <x-platform-badge :account="$account" /> •
                            Trading since {{ $milestones['first_trade_date'] ? $milestones['first_trade_date']->format('M Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('landing') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Track Your Performance Free
                </a>
            </div>
            
            {{-- Badges --}}
            @if(count($badges) > 0)
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($badges as $badge)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $badge->badge_color }}-100 text-{{ $badge->badge_color }}-800">
                            {!! $badge->badge_icon !!} {{ $badge->badge_name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </header>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        {{-- Performance Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="text-sm text-gray-500 mb-1">Total Trades</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_trades']) }}</div>
                <div class="text-sm text-gray-600 mt-1">{{ $stats['winning_trades'] }}W / {{ $stats['losing_trades'] }}L</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="text-sm text-gray-500 mb-1">Win Rate</div>
                <div class="text-3xl font-bold text-green-600">{{ $stats['win_rate'] }}%</div>
                <div class="text-sm text-gray-600 mt-1">Last 30 days</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="text-sm text-gray-500 mb-1">Total Profit</div>
                <div class="text-3xl font-bold {{ $stats['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($stats['total_profit'], 2) }} {{ $stats['currency'] }}
                </div>
                <div class="text-sm text-gray-600 mt-1">Last 30 days</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="text-sm text-gray-500 mb-1">Profit Factor</div>
                <div class="text-3xl font-bold text-indigo-600">{{ $stats['profit_factor'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Gross profit / loss</div>
            </div>
        </div>

        {{-- Equity Curve --}}
        @if(count($equity_curve) > 0)
            <div class="bg-white rounded-xl shadow-card p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Equity Curve (30 Days)</h2>
                <canvas id="equityChart" height="80"></canvas>
            </div>
        @endif

        {{-- Symbol Performance --}}
        @if($profile->show_symbols && count($symbol_performance) > 0)
            <div class="bg-white rounded-xl shadow-card p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Top Symbols</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trades</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Win Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($symbol_performance as $symbol)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $symbol['symbol'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $symbol['trades'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $symbol['win_rate'] }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap {{ $symbol['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($symbol['profit'], 2) }} {{ $stats['currency'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- CTA Section --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-card p-8 text-center text-white">
            <h2 class="text-2xl font-bold mb-2">Track Your Trading Performance</h2>
            <p class="text-indigo-100 mb-6">Join thousands of traders using TheTradeVisor to analyze their performance</p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition">
                Get Started Free
            </a>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-gray-600">
            <p>Powered by <a href="{{ route('landing') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">TheTradeVisor</a></p>
            <p class="text-sm mt-2">Professional Trading Analytics Platform</p>
        </div>
    </footer>

    <script>
        // Equity Curve Chart
        @if(count($equity_curve) > 0)
        const ctx = document.getElementById('equityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($equity_curve, 'date')) !!},
                datasets: [{
                    label: 'Equity',
                    data: {!! json_encode(array_column($equity_curve, 'equity')) !!},
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
        @endif
    </script>
</body>
</html>
