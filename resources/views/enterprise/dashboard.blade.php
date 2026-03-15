@section('title', 'Enterprise Dashboard - TheTradeVisor')

<x-enterprise-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            {{ __('Enterprise Dashboard') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Time Period Selector --}}
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card rounded-xl">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $broker->company_name }}</h2>
                            <p class="text-sm text-gray-600">Enterprise Analytics - View metrics for different time ranges</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('enterprise.dashboard', ['days' => 7]) }}"
                               class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 border {{ $days == 7 ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 shadow-sm' }}">
                                7 Days
                            </a>
                            <a href="{{ route('enterprise.dashboard', ['days' => 30]) }}"
                               class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 border {{ $days == 30 ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 shadow-sm' }}">
                                30 Days
                            </a>
                            <a href="{{ route('enterprise.dashboard', ['days' => 90]) }}"
                               class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 border {{ $days == 90 ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 shadow-sm' }}">
                                90 Days
                            </a>
                            <a href="{{ route('enterprise.dashboard', ['days' => 180]) }}"
                               class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 border {{ $days == 180 ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 shadow-sm' }}">
                                180 Days
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($broker->is_active)
                <div class="bg-green-50 border-l-4 border-green-500 p-4">
                    <p class="text-sm text-green-700">
                        <strong>Enterprise Plan Active</strong> - All users with <strong>{{ $broker->official_broker_name }}</strong> get unlimited free accounts
                    </p>
                </div>
            @elseif($broker->grace_period_ends_at && $broker->grace_period_ends_at > now())
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                    <p class="text-sm text-yellow-700">
                        <strong>Grace Period</strong> - Expires {{ $broker->grace_period_ends_at->format('M d, Y') }}
                    </p>
                </div>
            @else
                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                    <p class="text-sm text-red-700">
                        <strong>Subscription Inactive</strong> - Contact support to reactivate
                    </p>
                </div>
            @endif

            @if($stats['total_users'] == 0)
                <div class="bg-white p-12 text-center rounded-xl shadow">
                    <h3 class="text-xl font-semibold mb-4">No Users Yet</h3>
                    <p class="text-gray-600 mb-6">
                        Users will appear once they connect accounts with broker: <strong>{{ $broker->official_broker_name }}</strong>
                    </p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-left max-w-2xl mx-auto">
                        <h4 class="font-semibold mb-3">How It Works:</h4>
                        <ol class="space-y-2 text-sm">
                            <li>1. Users create free accounts on TheTradeVisor</li>
                            <li>2. They download MT4/MT5 EA and enter API key</li>
                            <li>3. EA sends data with broker name</li>
                            <li>4. System detects match with {{ $broker->official_broker_name }}</li>
                            <li>5. User gets unlimited free accounts automatically</li>
                        </ol>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Total Users</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_users']) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Total Accounts</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_accounts']) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Active (7 days)</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['active_last_7_days']) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Total Balance (USD)</p>
                        <p class="text-3xl font-bold">${{ number_format($stats['total_balance'], 2) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Total Equity (USD)</p>
                        <p class="text-3xl font-bold">${{ number_format($stats['total_equity'], 2) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Total Profit/Loss (USD)</p>
                        <p class="text-3xl font-bold {{ $stats['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($stats['total_profit'], 2) }}
                        </p>
                    </div>
                </div>

                @if(!empty($performance) && $performance['total_trades'] > 0)
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-4">Trading Performance (Last {{ $days }} Days)</h3>
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Total Trades</p>
                            <p class="text-2xl font-bold">{{ number_format($performance['total_trades']) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Winning</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($performance['winning_trades']) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Losing</p>
                            <p class="text-2xl font-bold text-red-600">{{ number_format($performance['losing_trades']) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Win Rate</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $performance['win_rate'] }}%</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Volume</p>
                            <p class="text-2xl font-bold">{{ number_format($performance['total_volume'], 2) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Profit (USD)</p>
                            <p class="text-2xl font-bold {{ $performance['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($performance['total_profit'], 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Profit Factor & Best/Worst Trades --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Profit Factor --}}
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-blue-500 hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Profit Factor</h4>
                                <span class="text-3xl">📊</span>
                            </div>
                            <div class="text-center py-4">
                                <div class="text-5xl font-bold text-blue-600 mb-2">
                                    {{ $performance['profit_factor'] }}
                                </div>
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                    @if($performance['profit_factor'] >= 2) bg-green-100 text-green-800
                                    @elseif($performance['profit_factor'] >= 1.5) bg-blue-100 text-blue-800
                                    @elseif($performance['profit_factor'] >= 1) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($performance['profit_factor'] >= 2)
                                        ✅ Excellent
                                    @elseif($performance['profit_factor'] >= 1.5)
                                        🟢 Good
                                    @elseif($performance['profit_factor'] >= 1)
                                        🟡 Fair
                                    @else
                                        🔴 Poor
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-xs text-gray-500 text-center">Ratio of gross profit to gross loss</p>
                            </div>
                        </div>
                    </div>

                    {{-- Best Trade --}}
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-green-500 hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Best Trade</h4>
                                <span class="text-3xl">🏆</span>
                            </div>
                            @if($performance['best_trade'])
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Symbol</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $performance['best_trade']['symbol'] }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Profit</span>
                                        <span class="text-lg font-bold text-green-600">${{ number_format($performance['best_trade']['profit'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Volume</span>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($performance['best_trade']['volume'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                        <span class="text-xs text-gray-500">Date</span>
                                        <span class="text-xs font-medium text-gray-700">{{ $performance['best_trade']['date'] }}</span>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100">
                                        <span class="text-xs text-gray-500 block mb-2">Account</span>
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $performance['best_trade']['account_number'] }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $performance['best_trade']['account_currency'] }}
                                            </span>
                                            @if($performance['best_trade']['platform_type'])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ strtoupper($performance['best_trade']['platform_type']) === 'MT5' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ strtoupper($performance['best_trade']['platform_type']) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <p class="text-sm italic">No trades yet</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Worst Trade --}}
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-red-500 hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-900">Worst Trade</h4>
                                <span class="text-3xl">📉</span>
                            </div>
                            @if($performance['worst_trade'])
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Symbol</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $performance['worst_trade']['symbol'] }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Loss</span>
                                        <span class="text-lg font-bold text-red-600">${{ number_format($performance['worst_trade']['profit'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Volume</span>
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($performance['worst_trade']['volume'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                        <span class="text-xs text-gray-500">Date</span>
                                        <span class="text-xs font-medium text-gray-700">{{ $performance['worst_trade']['date'] }}</span>
                                    </div>
                                    <div class="pt-3 border-t border-gray-100">
                                        <span class="text-xs text-gray-500 block mb-2">Account</span>
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $performance['worst_trade']['account_number'] }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $performance['worst_trade']['account_currency'] }}
                                            </span>
                                            @if($performance['worst_trade']['platform_type'])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ strtoupper($performance['worst_trade']['platform_type']) === 'MT5' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ strtoupper($performance['worst_trade']['platform_type']) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <p class="text-sm italic">No trades yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($chartData) && count($chartData) > 0)
                <div class="bg-white p-6 rounded-xl shadow">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">Balance & Equity Trend (Last {{ $days }} Days)</h3>
                            <p class="text-sm text-gray-500 mt-1">Aggregated across all accounts in USD</p>
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                <span class="text-gray-600">Balance</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-gray-600">Equity</span>
                            </div>
                        </div>
                    </div>
                    <div class="relative" style="height: 400px;">
                        <canvas id="balanceEquityChart"></canvas>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('balanceEquityChart');
                        if (!ctx) return;

                        const chartData = @json($chartData);
                        
                        const labels = chartData.map(item => {
                            const date = new Date(item.date);
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        });
                        
                        const balanceData = chartData.map(item => item.balance);
                        const equityData = chartData.map(item => item.equity);

                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Balance (USD)',
                                        data: balanceData,
                                        borderColor: 'rgb(59, 130, 246)',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        borderWidth: 3,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointHoverRadius: 6,
                                        pointBackgroundColor: 'rgb(59, 130, 246)',
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2,
                                    },
                                    {
                                        label: 'Equity (USD)',
                                        data: equityData,
                                        borderColor: 'rgb(34, 197, 94)',
                                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                        borderWidth: 3,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointHoverRadius: 6,
                                        pointBackgroundColor: 'rgb(34, 197, 94)',
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                        padding: 12,
                                        titleFont: {
                                            size: 14,
                                            weight: 'bold'
                                        },
                                        bodyFont: {
                                            size: 13
                                        },
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                label += '$' + context.parsed.y.toLocaleString('en-US', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                });
                                                return label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: false,
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)',
                                        },
                                        ticks: {
                                            callback: function(value) {
                                                return '$' + value.toLocaleString('en-US', {
                                                    minimumFractionDigits: 0,
                                                    maximumFractionDigits: 0
                                                });
                                            },
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            font: {
                                                size: 12
                                            },
                                            maxRotation: 45,
                                            minRotation: 45
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
                @endif

                @if($symbolStats->count() > 0)
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold">Top Trading Symbols (Last {{ $days }} Days)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="width: 180px; min-width: 180px;">Symbol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trades</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit (USD)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Win Rate</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($symbolStats as $symbol)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="width: 180px; min-width: 180px; max-width: 180px;">
                                        <span class="symbol-hover-toggle cursor-help transition-all duration-200 inline-block" 
                                              data-raw="{{ $symbol->symbol }}"
                                              data-normalized="{{ $symbol->normalized_symbol }}"
                                              title="Raw: {{ $symbol->symbol }}">
                                            {{ $symbol->normalized_symbol }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($symbol->trade_count) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($symbol->total_volume, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $symbol->total_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($symbol->total_profit, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        {{ $symbol->trade_count > 0 ? round(($symbol->winning_trades / $symbol->trade_count) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($topAccounts->count() > 0)
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold">Top Performing Accounts</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Equity</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Active</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topAccounts as $account)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $account->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $account->account_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $account->account_currency }}
                                            </span>
                                            @if($account->platform_type)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ strtoupper($account->platform_type) === 'MT5' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ strtoupper($account->platform_type) }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $account->account_currency }} {{ number_format($account->balance, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $account->account_currency }} {{ number_format($account->equity, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $account->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $account->account_currency }} {{ number_format($account->profit, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $account->last_data_received_at ? $account->last_data_received_at->diffForHumans() : 'Never' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endif

        </div>
    </div>

    {{-- Symbol Hover Toggle Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const symbolToggles = document.querySelectorAll('.symbol-hover-toggle');
            
            symbolToggles.forEach(toggle => {
                const rawSymbol = toggle.dataset.raw;
                const normalizedSymbol = toggle.dataset.normalized;
                
                toggle.addEventListener('mouseenter', function() {
                    this.textContent = rawSymbol;
                    this.classList.add('text-blue-600', 'font-semibold');
                });
                
                toggle.addEventListener('mouseleave', function() {
                    this.textContent = normalizedSymbol;
                    this.classList.remove('text-blue-600', 'font-semibold');
                });
            });
        });
    </script>
</x-enterprise-layout>
