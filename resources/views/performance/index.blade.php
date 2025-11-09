@section('title', 'Performance Metrics - TheTradeVisor | Trading Performance Analytics')
@section('description', 'Analyze your trading performance with detailed metrics including win rate, profit factor, average hold time, and trade analysis across all your MT5 accounts.')
@section('og_title', 'Trading Performance Metrics - TheTradeVisor')
@section('og_description', 'Comprehensive trading performance analytics and metrics for MT5 traders')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Performance Metrics') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">Comprehensive analysis of your trading performance</p>
            </div>

            {{-- Time Period Filter --}}
            <div class="flex gap-2">
                <a href="{{ route('performance', ['days' => 7]) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 7 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm' }}">
                    7 Days
                </a>
                <a href="{{ route('performance', ['days' => 30]) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 30 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm' }}">
                    30 Days
                </a>
                <a href="{{ route('performance', ['days' => 90]) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 90 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm' }}">
                    90 Days
                </a>
                <a href="{{ route('performance', ['days' => 365]) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 365 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm' }}">
                    1 Year
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(!$hasAccounts)
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card rounded-xl">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Trading Accounts</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by connecting your first trading account.</p>
                        <div class="mt-6">
                            <a href="{{ route('accounts.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Connect Account
                            </a>
                        </div>
                    </div>
                </div>
            @else
                {{-- Trade Analysis Section --}}
                @if($metrics['trade_analysis'])
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">📊 Trade Analysis (Last {{ $days }} Days)</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            {{-- Total Trades --}}
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Total Trades</div>
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($metrics['trade_analysis']['total_trades']) }}</div>
                            </div>

                            {{-- Win Rate --}}
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Win Rate</div>
                                <div class="text-2xl font-bold text-green-600">{{ $metrics['trade_analysis']['win_rate'] }}%</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $metrics['trade_analysis']['winning_trades'] }}W / {{ $metrics['trade_analysis']['losing_trades'] }}L
                                </div>
                            </div>

                            {{-- Profit Factor --}}
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Profit Factor</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $metrics['trade_analysis']['profit_factor'] }}</div>
                            </div>

                            {{-- Avg Hold Time --}}
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Avg Hold Time</div>
                                <div class="text-2xl font-bold text-purple-600">{{ $metrics['trade_analysis']['avg_hold_time'] }}</div>
                            </div>
                        </div>

                        {{-- Best and Worst Trades --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50">
                                <h4 class="font-semibold text-green-800 mb-2">🏆 Most Profitable Trade</h4>
                                <div class="text-sm space-y-1">
                                    <div><span class="font-medium">Symbol:</span> {{ $metrics['trade_analysis']['most_profitable_trade']['symbol'] }}</div>
                                    <div><span class="font-medium">Profit:</span> <span class="text-green-600 font-bold">{{ $displayCurrency }} {{ number_format($metrics['trade_analysis']['most_profitable_trade']['profit'], 2) }}</span></div>
                                    <div><span class="font-medium">ROI:</span> {{ $metrics['trade_analysis']['most_profitable_trade']['roi'] }}%</div>
                                    <div><span class="font-medium">Date:</span> {{ $metrics['trade_analysis']['most_profitable_trade']['date'] }}</div>
                                </div>
                            </div>

                            <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
                                <h4 class="font-semibold text-red-800 mb-2">📉 Worst Trade</h4>
                                <div class="text-sm space-y-1">
                                    <div><span class="font-medium">Symbol:</span> {{ $metrics['trade_analysis']['worst_trade']['symbol'] }}</div>
                                    <div><span class="font-medium">Loss:</span> <span class="text-red-600 font-bold">{{ $displayCurrency }} {{ number_format($metrics['trade_analysis']['worst_trade']['profit'], 2) }}</span></div>
                                    <div><span class="font-medium">Date:</span> {{ $metrics['trade_analysis']['worst_trade']['date'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Equity Curve Chart --}}
                @if(!empty($metrics['equity_curve']))
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">📈 Equity Curve</h2>
                        <div style="height: 400px;">
                            <canvas id="equityCurveChart"></canvas>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Symbol Performance --}}
                @if($metrics['symbol_performance'] && $metrics['symbol_performance']->isNotEmpty())
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">💰 Symbol Performance</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trades</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Win Rate</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Profit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Profit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($metrics['symbol_performance'] as $symbol)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $symbol['symbol'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $symbol['total_trades'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $symbol['win_rate'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $symbol['win_rate'] }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $symbol['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $displayCurrency }} {{ number_format($symbol['total_profit'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $symbol['avg_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $displayCurrency }} {{ number_format($symbol['avg_profit'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($symbol['total_volume'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Risk Metrics --}}
                @if($metrics['risk_metrics'])
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">⚖️ Risk Metrics</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Risk/Reward Ratio</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $metrics['risk_metrics']['risk_reward_ratio'] }}</div>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Avg Win</div>
                                <div class="text-2xl font-bold text-green-600">{{ $displayCurrency }} {{ number_format($metrics['risk_metrics']['avg_win'], 2) }}</div>
                            </div>

                            <div class="bg-red-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Avg Loss</div>
                                <div class="text-2xl font-bold text-red-600">{{ $displayCurrency }} {{ number_format($metrics['risk_metrics']['avg_loss'], 2) }}</div>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Largest Win</div>
                                <div class="text-2xl font-bold text-purple-600">{{ $displayCurrency }} {{ number_format($metrics['risk_metrics']['largest_win'], 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Streaks --}}
                @if($metrics['streaks'])
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">🔥 Winning & Losing Streaks</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <div class="text-sm text-gray-600">Max Win Streak</div>
                                <div class="text-4xl font-bold text-green-600">{{ $metrics['streaks']['max_win_streak'] }}</div>
                            </div>

                            <div class="bg-red-50 rounded-lg p-4 text-center">
                                <div class="text-sm text-gray-600">Max Loss Streak</div>
                                <div class="text-4xl font-bold text-red-600">{{ $metrics['streaks']['max_loss_streak'] }}</div>
                            </div>

                            <div class="bg-indigo-50 rounded-lg p-4 text-center">
                                <div class="text-sm text-gray-600">Current Streak</div>
                                <div class="text-4xl font-bold {{ $metrics['streaks']['current_streak'] > 0 ? 'text-green-600' : ($metrics['streaks']['current_streak'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                    {{ abs($metrics['streaks']['current_streak']) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ ucfirst($metrics['streaks']['current_streak_type']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Timing Analysis - Hourly Heatmap --}}
                @if($metrics['timing_analysis'])
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">⏰ Trading Hours Performance</h2>
                        <div style="height: 300px;">
                            <canvas id="hourlyPerformanceChart"></canvas>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Best Hour</div>
                                <div class="text-2xl font-bold text-green-600">{{ $metrics['timing_analysis']['best_hour']['hour'] }}</div>
                                <div class="text-sm text-gray-600">{{ $displayCurrency }} {{ number_format($metrics['timing_analysis']['best_hour']['profit'], 2) }} profit</div>
                            </div>

                            <div class="bg-red-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Worst Hour</div>
                                <div class="text-2xl font-bold text-red-600">{{ $metrics['timing_analysis']['worst_hour']['hour'] }}</div>
                                <div class="text-sm text-gray-600">{{ $displayCurrency }} {{ number_format($metrics['timing_analysis']['worst_hour']['profit'], 2) }} profit</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Day of Week Performance --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">📅 Day of Week Performance</h2>
                        <div style="height: 300px;">
                            <canvas id="dailyPerformanceChart"></canvas>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Best Day</div>
                                <div class="text-2xl font-bold text-green-600">{{ $metrics['timing_analysis']['best_day']['day'] }}</div>
                                <div class="text-sm text-gray-600">{{ $displayCurrency }} {{ number_format($metrics['timing_analysis']['best_day']['profit'], 2) }} profit</div>
                            </div>

                            <div class="bg-red-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Worst Day</div>
                                <div class="text-2xl font-bold text-red-600">{{ $metrics['timing_analysis']['worst_day']['day'] }}</div>
                                <div class="text-sm text-gray-600">{{ $displayCurrency }} {{ number_format($metrics['timing_analysis']['worst_day']['profit'], 2) }} profit</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Drawdown Chart --}}
                @if($metrics['drawdown'])
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">📉 Drawdown Analysis</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-red-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Maximum Drawdown</div>
                                <div class="text-3xl font-bold text-red-600">{{ $metrics['drawdown']['max_drawdown'] }}%</div>
                            </div>

                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Current Drawdown</div>
                                <div class="text-3xl font-bold text-orange-600">{{ $metrics['drawdown']['current_drawdown'] }}%</div>
                            </div>
                        </div>

                        <div style="height: 300px;">
                            <canvas id="drawdownChart"></canvas>
                        </div>
                    </div>
                </div>
                @endif

            @endif
        </div>
    </div>

    @if($hasAccounts)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Equity Curve Chart
        @if(!empty($metrics['equity_curve']))
        const equityCurveCtx = document.getElementById('equityCurveChart');
        if (equityCurveCtx) {
            new Chart(equityCurveCtx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'Balance',
                        data: @json(collect($metrics['equity_curve'])->map(function($point) {
                            return ['x' => $point['date'], 'y' => $point['balance']];
                        })),
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time',
                            time: { unit: 'day' }
                        },
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: value => '{{ $displayCurrency }}' + value.toLocaleString()
                            }
                        }
                    }
                }
            });
        }
        @endif

        // Hourly Performance Chart
        @if($metrics['timing_analysis'])
        const hourlyCtx = document.getElementById('hourlyPerformanceChart');
        if (hourlyCtx) {
            new Chart(hourlyCtx, {
                type: 'bar',
                data: {
                    labels: @json(collect($metrics['timing_analysis']['hourly_performance'])->pluck('hour')),
                    datasets: [{
                        label: 'Profit',
                        data: @json(collect($metrics['timing_analysis']['hourly_performance'])->pluck('profit')),
                        backgroundColor: function(context) {
                            const value = context.parsed.y;
                            return value >= 0 ? 'rgba(34, 197, 94, 0.8)' : 'rgba(239, 68, 68, 0.8)';
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: value => '$' + value.toLocaleString()
                            }
                        }
                    }
                }
            });
        }

        // Daily Performance Chart
        const dailyCtx = document.getElementById('dailyPerformanceChart');
        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: @json(collect($metrics['timing_analysis']['daily_performance'])->pluck('day')),
                    datasets: [{
                        label: 'Profit',
                        data: @json(collect($metrics['timing_analysis']['daily_performance'])->pluck('profit')),
                        backgroundColor: function(context) {
                            const value = context.parsed.y;
                            return value >= 0 ? 'rgba(34, 197, 94, 0.8)' : 'rgba(239, 68, 68, 0.8)';
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: value => '$' + value.toLocaleString()
                            }
                        }
                    }
                }
            });
        }
        @endif

        // Drawdown Chart
        @if($metrics['drawdown'])
        const drawdownCtx = document.getElementById('drawdownChart');
        if (drawdownCtx) {
            new Chart(drawdownCtx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'Drawdown %',
                        data: @json(collect($metrics['drawdown']['drawdown_periods'])->map(function($point) {
                            return ['x' => $point['date'], 'y' => -$point['drawdown']];
                        })),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time',
                            time: { unit: 'day' }
                        },
                        y: {
                            ticks: {
                                callback: value => value + '%'
                            }
                        }
                    }
                }
            });
        }
        @endif
    });
    </script>
    @endpush
    @endif
</x-app-layout>
