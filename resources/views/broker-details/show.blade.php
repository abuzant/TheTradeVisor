<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $broker }} trading analytics - View real trading statistics, performance metrics, and insights from traders using {{ $broker }}. Last 180 days of aggregated data.">
    <meta name="keywords" content="{{ $broker }}, forex broker, trading statistics, broker analytics, trading performance, {{ $broker }} review">
    <meta property="og:title" content="{{ $broker }} - Trading Analytics & Statistics">
    <meta property="og:description" content="Real trading data and performance metrics from {{ $broker }} traders. {{ $overview['total_trades'] }} trades analyzed over 180 days.">
    <meta property="og:type" content="website">
    <title>{{ $broker }} - Trading Analytics & Performance Statistics | TheTradeVisor</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FinancialService",
        "name": "{{ $broker }}",
        "description": "Trading analytics and statistics for {{ $broker }}",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $overview['win_rate'] / 20 }}",
            "bestRating": "5",
            "worstRating": "1",
            "ratingCount": "{{ $overview['active_traders'] }}"
        }
    }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        TheTradeVisor
                    </a>
                </div>
                <nav class="flex gap-6">
                    <a href="/" class="text-gray-600 hover:text-indigo-600">Home</a>
                    <a href="/features" class="text-gray-600 hover:text-indigo-600">Features</a>
                    <a href="/pricing" class="text-gray-600 hover:text-indigo-600">Pricing</a>
                    @auth
                        <a href="/dashboard" class="text-indigo-600 font-medium">Dashboard</a>
                    @else
                        <a href="/login" class="text-indigo-600 font-medium">Login</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Broker Header -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $broker }}</h1>
            <p class="text-gray-600 text-lg">Real trading analytics from {{ $overview['active_traders'] }} active traders over the last {{ $days }} days</p>
            <div class="mt-4 flex gap-4 text-sm text-gray-500">
                <span>📊 {{ number_format($overview['total_trades']) }} trades analyzed</span>
                <span>🌍 {{ $top_countries->count() }} countries</span>
                <span>📈 {{ $top_symbols->count() }} symbols traded</span>
            </div>
        </div>

        <!-- Overview Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Total Trades</div>
                <div class="text-3xl font-bold text-indigo-600">{{ number_format($overview['total_trades']) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $overview['active_traders'] }} traders</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Win Rate</div>
                <div class="text-3xl font-bold {{ $overview['win_rate'] >= 50 ? 'text-green-600' : 'text-orange-600' }}">
                    {{ $overview['win_rate'] }}%
                </div>
                <div class="text-xs text-gray-500 mt-1">Success ratio</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Avg Trade Size</div>
                <div class="text-3xl font-bold text-purple-600">{{ number_format($overview['avg_trade_size'], 2) }}</div>
                <div class="text-xs text-gray-500 mt-1">Lots</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Avg Hold Time</div>
                <div class="text-3xl font-bold text-blue-600">{{ $avg_hold_time }}</div>
                <div class="text-xs text-gray-500 mt-1">Per position</div>
            </div>
        </div>

        <!-- Daily Profit Trend Chart -->
        @if($daily_profit_trend->isNotEmpty())
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">📈 Daily Trading Activity</h2>
            <canvas id="dailyTrendChart" height="80"></canvas>
        </div>
        @endif

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Countries -->
            @if($top_countries->isNotEmpty())
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">🌍 Top Trading Countries</h2>
                <div class="space-y-3">
                    @foreach($top_countries as $country)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $country->country }}</div>
                            <div class="text-sm text-gray-600">{{ number_format($country->trades) }} trades</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold {{ $country->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $country->profit >= 0 ? '+' : '' }}{{ number_format($country->profit, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">{{ number_format($country->volume, 2) }} lots</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Most Profitable Pairs -->
            @if($most_profitable_pairs->isNotEmpty())
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">💰 Most Profitable Pairs</h2>
                <div class="space-y-3">
                    @foreach($most_profitable_pairs as $pair)
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $pair->normalized_symbol }}</div>
                            <div class="text-sm text-gray-600">{{ number_format($pair->trades) }} trades</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-green-600">+${{ number_format($pair->total_profit, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ number_format($pair->avg_volume, 2) }} avg lots</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Biggest Loss Pairs & Top Symbols -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Biggest Loss Pairs -->
            @if($biggest_loss_pairs->isNotEmpty())
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">📉 Biggest Loss Pairs</h2>
                <div class="space-y-3">
                    @foreach($biggest_loss_pairs as $pair)
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $pair->normalized_symbol }}</div>
                            <div class="text-sm text-gray-600">{{ number_format($pair->trades) }} trades</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-red-600">${{ number_format($pair->total_profit, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ number_format($pair->avg_volume, 2) }} avg lots</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Top Traded Symbols -->
            @if($top_symbols->isNotEmpty())
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">🔥 Most Traded Symbols</h2>
                <div class="space-y-3">
                    @foreach($top_symbols->take(10) as $symbol)
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $symbol->normalized_symbol }}</div>
                            <div class="text-sm text-gray-600">{{ number_format($symbol->trades) }} trades</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-blue-600">{{ number_format($symbol->total_volume, 2) }} lots</div>
                            <div class="text-xs text-gray-500">{{ number_format($symbol->avg_lot_size, 2) }} avg</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Symbol Performance Table -->
        @if($symbol_performance->isNotEmpty())
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">📊 Symbol Performance (Top 25)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Trades</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Win Rate</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Profit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg Lot Size</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($symbol_performance as $perf)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $perf->normalized_symbol }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-600">{{ number_format($perf->total_trades) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $perf->win_rate >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $perf->win_rate }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-semibold {{ $perf->total_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $perf->total_profit >= 0 ? '+' : '' }}${{ number_format($perf->total_profit, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-600">{{ number_format($perf->avg_lot_size, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg p-8 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">Want to Track Your Own Trading Performance?</h2>
            <p class="text-lg mb-6 opacity-90">Join TheTradeVisor and get detailed analytics for your trading accounts</p>
            <div class="flex gap-4 justify-center">
                @guest
                    <a href="/register" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Get Started Free
                    </a>
                    <a href="/features" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition">
                        Learn More
                    </a>
                @else
                    <a href="/dashboard" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Go to Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} TheTradeVisor. All rights reserved.</p>
                <div class="mt-4 flex justify-center gap-6">
                    <a href="/about" class="hover:text-white">About</a>
                    <a href="/contact" class="hover:text-white">Contact</a>
                    <a href="/faq" class="hover:text-white">FAQ</a>
                    <a href="/pricing" class="hover:text-white">Pricing</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Chart Script -->
    @if($daily_profit_trend->isNotEmpty())
    <script>
        const ctx = document.getElementById('dailyTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($daily_profit_trend->pluck('date')) !!},
                datasets: [{
                    label: 'Daily Profit',
                    data: {!! json_encode($daily_profit_trend->pluck('profit')) !!},
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
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
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    @endif
</body>
</html>
