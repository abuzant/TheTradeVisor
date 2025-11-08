<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Global Trading Analytics') }}
            </h2>

            {{-- Time Period Filter --}}
            <div class="flex gap-2">
                <a href="{{ route('analytics', ['days' => 7]) }}"
                   class="px-4 py-2 rounded-md text-sm {{ $days == 7 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    7 Days
                </a>
                <a href="{{ route('analytics', ['days' => 30]) }}"
                   class="px-4 py-2 rounded-md text-sm {{ $days == 30 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    30 Days
                </a>
                <a href="{{ route('analytics', ['days' => 90]) }}"
                   class="px-4 py-2 rounded-md text-sm {{ $days == 90 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    90 Days
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info Banner --}}
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Global Analytics:</strong> Real-time insights from thousands of traders worldwide. All data is anonymized and aggregated.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Overview Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #6366f1, #4f46e5);">
                    <div class="text-indigo-100 text-sm font-medium">Active Traders</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($analytics['overview']['total_traders']) }}</div>
                    <div class="text-indigo-100 text-xs mt-1">Platform-wide</div>
                </div>

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #10b981, #059669);">
                    <div class="text-green-100 text-sm font-medium">Total Trades</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($analytics['overview']['total_trades']) }}</div>
                    <div class="text-green-100 text-xs mt-1">Last {{ $days }} days</div>
                </div>

                @if($analytics['overview']['total_profit'] >= 0)
                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #10b981, #059669);">
                    <div class="text-white text-opacity-90 text-sm font-medium">Total Profit/Loss</div>
                    <div class="text-3xl font-bold mt-2">{{ $displayCurrency }} {{ number_format($analytics['overview']['total_profit'], 0) }}</div>
                    <div class="text-white text-opacity-90 text-xs mt-1">Avg: {{ $displayCurrency }} {{ number_format($analytics['overview']['avg_trade_profit'], 2) }}/trade</div>
                </div>
                @else
                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #ef4444, #dc2626);">
                    <div class="text-white text-opacity-90 text-sm font-medium">Total Profit/Loss</div>
                    <div class="text-3xl font-bold mt-2">{{ $displayCurrency }} {{ number_format($analytics['overview']['total_profit'], 0) }}</div>
                    <div class="text-white text-opacity-90 text-xs mt-1">Avg: {{ $displayCurrency }} {{ number_format($analytics['overview']['avg_trade_profit'], 2) }}/trade</div>
                </div>
                @endif

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #a855f7, #9333ea);">
                    <div class="text-purple-100 text-sm font-medium">Total Volume</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($analytics['overview']['total_volume'], 1) }}</div>
                    <div class="text-purple-100 text-xs mt-1">Lots traded</div>
                </div>

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #f97316, #ea580c);">
                    <div class="text-orange-100 text-sm font-medium">Active Accounts</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($analytics['overview']['active_accounts']) }}</div>
                    <div class="text-orange-100 text-xs mt-1">{{ $analytics['overview']['total_brokers'] }} brokers</div>
                </div>

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #3b82f6, #2563eb);">
                    <div class="text-blue-100 text-sm font-medium">Open Positions</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($analytics['overview']['open_positions']) }}</div>
                    <div class="text-blue-100 text-xs mt-1">Currently active</div>
                </div>

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #ec4899, #db2777);">
                    <div class="text-pink-100 text-sm font-medium">Trading Costs</div>
                    <div class="text-3xl font-bold mt-2">{{ $displayCurrency }} {{ number_format($analytics['trading_costs']['total_costs'], 0) }}</div>
                    <div class="text-pink-100 text-xs mt-1">Avg: {{ $displayCurrency }} {{ number_format($analytics['trading_costs']['avg_cost_per_trade'], 2) }}/trade</div>
                </div>

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #eab308, #ca8a04);">
                    <div class="text-yellow-100 text-sm font-medium">Countries</div>
                    <div class="text-3xl font-bold mt-2">{{ $analytics['overview']['countries'] }}</div>
                    <div class="text-yellow-100 text-xs mt-1">Global reach</div>
                </div>

            </div>

            {{-- Daily Volume Trend Chart --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Daily Trading Volume Trend</h3>
                    <div class="h-64">
                        <canvas id="volumeTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Win Rate by Symbol Table --}}
            @if(!empty($analytics['win_rate_by_symbol']) && count($analytics['win_rate_by_symbol']) > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Win Rate by Symbol</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Trades</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Win Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Profit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($analytics['win_rate_by_symbol'] as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['symbol'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item['total_trades']) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="h-2 rounded-full" style="width: {{ $item['win_rate'] }}%; background-color: {{ $item['win_rate'] >= 50 ? '#059669' : '#dc2626' }}"></div>
                                            </div>
                                            <span class="text-sm font-semibold {{ $item['win_rate'] >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $item['win_rate'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $item['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $displayCurrency }} {{ number_format($item['total_profit'], 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Position Size Distribution --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📏 Position Size Distribution</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Average</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $analytics['position_sizes']['avg'] }}</div>
                            <div class="text-xs text-gray-500">lots</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Median</div>
                            <div class="text-2xl font-bold text-green-600">{{ $analytics['position_sizes']['median'] }}</div>
                            <div class="text-xs text-gray-500">lots</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Minimum</div>
                            <div class="text-2xl font-bold text-purple-600">{{ $analytics['position_sizes']['min'] }}</div>
                            <div class="text-xs text-gray-500">lots</div>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Maximum</div>
                            <div class="text-2xl font-bold text-orange-600">{{ $analytics['position_sizes']['max'] }}</div>
                            <div class="text-xs text-gray-500">lots</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Popular Pairs --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Traded Pairs (7 Days)</h3>
                        <div class="space-y-3">
                            @foreach($analytics['popular_pairs'] as $pair)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-semibold text-gray-900">{{ $pair['symbol'] }}</span>
                                    <span class="text-xs text-gray-500">{{ number_format($pair['trades']) }} trades</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">Vol: {{ number_format($pair['volume'], 1) }}</span>
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, ($pair['trades'] / $analytics['popular_pairs']->first()['trades']) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Market Sentiment --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Market Sentiment (Open Positions)</h3>
                        @if(!empty($analytics['sentiment']) && count($analytics['sentiment']) > 0)
                        <div class="space-y-3">
                            @foreach($analytics['sentiment'] as $item)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-semibold text-gray-900">{{ $item['symbol'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $item['total'] }} positions</span>
                                </div>
                                <div class="flex h-4 rounded-full overflow-hidden">
                                    <div class="bg-green-500 flex items-center justify-center text-xs text-white font-semibold" style="width: {{ $item['buy_percent'] }}%">
                                        @if($item['buy_percent'] > 15)
                                            {{ $item['buy_percent'] }}%
                                        @endif
                                    </div>
                                    <div class="bg-red-500 flex items-center justify-center text-xs text-white font-semibold" style="width: {{ $item['sell_percent'] }}%">
                                        @if($item['sell_percent'] > 15)
                                            {{ $item['sell_percent'] }}%
                                        @endif
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>Buy {{ $item['buy_percent'] }}%</span>
                                    <span>Sell {{ $item['sell_percent'] }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="mt-2 text-sm">No open positions with significant volume to display sentiment</p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Trading Activity by Hour --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Trading Activity by Hour (UTC, Last 7 Days)</h3>
                    <div class="h-64">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Charts Row 2 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Regional Activity --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Top Trading Countries</h3>
                            @if(count($analytics['regional_activity']) > 0)
                            <a href="{{ route('analytics.countries') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                View Detailed Analytics →
                            </a>
                            @endif
                        </div>
                        @if(count($analytics['regional_activity']) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Accounts</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($analytics['regional_activity'] as $region)
                                    <tr>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                @if($region['country_code'])
                                                <span class="mr-2 text-2xl">{{ \App\Helpers\CountryHelper::getFlag($region['country_code']) }}</span>
                                                @endif
                                                {{ $region['country'] }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ number_format($region['accounts']) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $displayCurrency }} {{ number_format($region['balance'], 0) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2 text-sm">No country data available yet</p>
                            <p class="mt-1 text-xs text-gray-400">Country tracking will begin with new API requests</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Top Performing Pairs --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Pairs (7 Days)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total P&L</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Trades</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Avg</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($analytics['top_performers'] as $performer)
                                    <tr>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $performer['symbol'] }}</td>
                                        <td class="px-4 py-2 text-sm font-semibold {{ $performer['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $displayCurrency }} {{ number_format($performer['profit'], 2) }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ number_format($performer['trades']) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $displayCurrency }} {{ number_format($performer['avg_profit'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Broker Distribution --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Broker Distribution</h3>
                    <div class="h-64">
                        <canvas id="brokerChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Daily Volume Trend Chart
        const volumeTrendCtx = document.getElementById('volumeTrendChart').getContext('2d');
        const volumeData = {!! json_encode($analytics['daily_volume_trend']) !!};

        new Chart(volumeTrendCtx, {
            type: 'line',
            data: {
                labels: volumeData.map(d => d.date),
                datasets: [{
                    label: 'Daily Volume (Lots)',
                    data: volumeData.map(d => parseFloat(d.volume)),
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                }, {
                    label: 'Number of Trades',
                    data: volumeData.map(d => parseInt(d.trades)),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Volume (Lots)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Number of Trades'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // Hourly Activity Chart
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($analytics['trading_by_hour'], 'hour')) !!},
                datasets: [{
                    label: 'Trades',
                    data: {!! json_encode(array_column($analytics['trading_by_hour'], 'trades')) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Broker Distribution Chart
        const brokerCtx = document.getElementById('brokerChart').getContext('2d');
        new Chart(brokerCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($analytics['broker_distribution']->pluck('broker_name')) !!},
                datasets: [{
                    data: {!! json_encode($analytics['broker_distribution']->pluck('accounts')) !!},
                    backgroundColor: [
                        'rgb(79, 70, 229)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
