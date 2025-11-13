<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center">
                    <span class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-lg mr-3 text-2xl font-bold">
                        {{ strtoupper($symbol) }}
                    </span>
                    Complete Trading Analysis
                </h2>
                <p class="text-sm text-gray-600 mt-2">Deep dive into your {{ strtoupper($symbol) }} trading performance</p>
            </div>
            <a href="{{ route('trades.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                ← Back to All Trades
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Date Range Filter & Export -->
            <x-date-range-filter :route="route('trades.symbol', $symbol)">
                <div class="flex gap-2 ml-auto">
                    <a href="{{ route('export.symbol.csv', array_merge(['symbol' => $symbol], request()->all())) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export CSV
                    </a>
                </div>
            </x-date-range-filter>

            <!-- Primary Stats Cards - SIMPLIFIED -->
            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-4">
                <!-- Total Trades -->
                <div style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);" class="overflow-hidden shadow-lg rounded-lg p-6 text-white">
                    <div class="text-xs font-semibold uppercase tracking-wide opacity-90">Total Trades</div>
                    <div class="text-4xl font-extrabold mt-2">{{ number_format($stats['total_trades']) }}</div>
                    <div class="text-xs mt-2 opacity-90">{{ number_format($stats['trades_per_day'], 1) }} per day</div>
                </div>

                <!-- Win Rate -->
                <div style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);" class="overflow-hidden shadow-lg rounded-lg p-6 text-white">
                    <div class="text-xs font-semibold uppercase tracking-wide opacity-90">Win Rate</div>
                    <div class="text-4xl font-extrabold mt-2">{{ $stats['win_rate'] }}%</div>
                    <div class="text-xs mt-2 opacity-90">{{ $stats['winning_trades'] }}W / {{ $stats['losing_trades'] }}L</div>
                </div>

                <!-- Total Volume -->
                <div style="background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);" class="overflow-hidden shadow-lg rounded-lg p-6 text-white">
                    <div class="text-xs font-semibold uppercase tracking-wide opacity-90">Total Volume <em>(lots)</em></div>
                    <div class="text-4xl font-extrabold mt-2">{{ number_format($stats['total_volume'], 1) }}</div>
                    <div class="text-xs mt-2 opacity-90">Avg: {{ number_format($stats['avg_volume'], 2) }} lots</div>
                </div>

                <!-- Total P&L -->
                <div style="background: linear-gradient(135deg, {{ $stats['total_profit'] >= 0 ? '#10B981, #059669' : '#EF4444, #DC2626' }});" class="overflow-hidden shadow-lg rounded-lg p-6 text-white">
                    <div class="text-xs font-semibold uppercase tracking-wide opacity-90">Total P&L</div>
                    <div class="text-4xl font-extrabold mt-2">USD {{ number_format($stats['total_profit'], 2) }}</div>
                    <div class="text-xs mt-2 opacity-90">Avg: USD {{ number_format($stats['avg_profit'], 2) }}/trade</div>
                </div>

                <!-- Profit Factor -->
                <div style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);" class="overflow-hidden shadow-lg rounded-lg p-6 text-white">
                    <div class="text-xs font-semibold uppercase tracking-wide opacity-90">Profit Factor</div>
                    <div class="text-4xl font-extrabold mt-2">{{ number_format($stats['profit_factor'], 2) }}</div>
                    <div class="text-xs mt-2 opacity-90">{{ $stats['profit_factor'] >= 2 ? 'Excellent' : ($stats['profit_factor'] >= 1.5 ? 'Good' : ($stats['profit_factor'] >= 1 ? 'Fair' : 'Poor')) }}</div>
                </div>
            </div>
            <!-- Key Performance Indicators -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <!-- Risk Metrics -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Risk/Reward</dt>
                                    <dd class="text-2xl font-bold text-gray-900">1:{{ number_format($stats['risk_reward'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 mt-4 rounded-b-lg border-t border-gray-200">
                        <div class="text-sm mb-2">
                            <span class="text-gray-600">Avg Win:</span>
                            <span class="font-medium text-green-600 ml-2">USD {{ number_format($stats['avg_win'], 2) }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-600">Avg Loss:</span>
                            <span class="font-medium text-red-600 ml-2">USD {{ number_format($stats['avg_loss'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Best/Worst Trade -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Best Trade</dt>
                                    <dd class="text-2xl font-bold text-green-600">USD {{ number_format($stats['best_trade'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 mt-4 rounded-b-lg border-t border-gray-200">
                        <div class="text-sm mb-2">
                            <span class="text-gray-600">Worst Trade:</span>
                            <span class="font-medium text-red-600 ml-2">USD {{ number_format($stats['worst_trade'], 2) }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-600">Range:</span>
                            <span class="font-medium text-gray-900 ml-2">USD {{ number_format($stats['best_trade'] - $stats['worst_trade'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Trading Direction -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Best Direction</dt>
                                    <dd class="text-2xl font-bold text-gray-900">{{ $stats['best_direction'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 mt-4 rounded-b-lg border-t border-gray-200">
                        <div class="text-sm mb-2">
                            <span class="text-gray-600">Buy:</span>
                            <span class="font-medium text-green-600 ml-2">{{ $stats['buy_trades'] }} ({{ $stats['buy_percentage'] }}%)</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-600">Sell:</span>
                            <span class="font-medium text-red-600 ml-2">{{ $stats['sell_trades'] }} ({{ $stats['sell_percentage'] }}%)</span>
                        </div>
                    </div>
                </div>

                <!-- Streaks -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Max Win Streak</dt>
                                    <dd class="text-2xl font-bold text-green-600">{{ $stats['max_win_streak'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 mt-4 rounded-b-lg border-t border-gray-200">
                        <div class="text-sm mb-2">
                            <span class="text-gray-600">Max Loss Streak:</span>
                            <span class="font-medium text-red-600 ml-2">{{ $stats['max_loss_streak'] }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-600">Current:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $stats['current_streak'] > 0 ? '+' : '' }}{{ $stats['current_streak'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trading Activity Analysis -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Time Analysis -->
                <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Trading Timeline
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">First Trade:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $stats['first_trade'] }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Last Trade:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $stats['last_trade'] }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Active Days:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['trading_days'], 1) }} days</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Avg/Day:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['trades_per_day'], 1) }} trades</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-sm text-gray-600">Peak Hour:</span>
                            <span class="text-sm font-bold text-indigo-600">{{ $stats['most_active_hour'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Most Traded Days -->
                <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        Most Active Days
                    </h3>
                    <div class="space-y-2">
                        @foreach($stats['day_distribution'] as $day => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 font-medium">{{ $day }}</span>
                                    <span class="text-gray-600">{{ $count }} trades</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $stats['total_trades'] > 0 ? ($count / $stats['total_trades'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Costs & Fees -->
                <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                        Trading Costs
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Commission</span>
                            <span class="text-lg font-bold text-red-600">USD {{ number_format(abs($stats['total_commission']), 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Swap</span>
                            <span class="text-lg font-bold text-{{ $stats['total_swap'] >= 0 ? 'green' : 'red' }}-600">USD {{ number_format($stats['total_swap'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b">
                            <span class="text-sm text-gray-600">Total Fees</span>
                            <span class="text-lg font-bold text-red-600">USD {{ number_format($stats['total_fees'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 bg-gray-50 -mx-6 px-6 mt-3">
                            <span class="text-sm font-medium text-gray-700">Net After Costs</span>
                            <span class="text-xl font-bold text-{{ ($stats['total_profit'] - $stats['total_fees']) >= 0 ? 'green' : 'red' }}-600">
                                USD {{ number_format($stats['total_profit'] - $stats['total_fees'], 2) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 text-center mt-2">
                            Avg cost per trade: USD {{ number_format($stats['avg_cost_per_trade'], 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complete Trade History -->
            <div class="bg-white overflow-hidden shadow rounded-lg" x-data="{ showCommission: false, showSwap: false }">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Complete Trade History
                            <span class="ml-3 px-3 py-1 text-sm bg-indigo-100 text-indigo-800 rounded-full">{{ number_format($stats['total_trades']) }} Trades</span>
                        </h3>
                        <div class="flex gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="showCommission" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Show Commission</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="showSwap" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Show Swap</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entry Price</th>
                                <th x-show="showCommission" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                                <th x-show="showSwap" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Swap</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net P&L</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($deals as $deal)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">
                                        <div class="text-gray-900 font-semibold">#{{ $deal->ticket }}</div>
                                        @if($deal->tradingAccount)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ explode(' ', $deal->tradingAccount->broker_name)[0] }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $deal->time ? $deal->time->format('M d, Y') : 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $deal->time ? $deal->time->format('H:i:s') : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="font-semibold text-indigo-600">{{ strtoupper($deal->normalized_symbol) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $deal->is_buy ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $deal->display_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        {{ number_format($deal->volume, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">
                                        {{ $deal->formatted_price }}
                                    </td>
                                    <td x-show="showCommission" class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        USD {{ number_format(abs($deal->commission_usd), 2) }}
                                    </td>
                                    <td x-show="showSwap" class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $deal->swap_usd >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        USD {{ number_format($deal->swap_usd, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 inline-flex text-sm font-bold rounded {{ $deal->profit_usd >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            USD {{ number_format($deal->profit_usd, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-4 text-lg font-medium text-gray-900">No trades found</p>
                                        <p class="mt-2 text-sm text-gray-500">Start trading {{ strtoupper($symbol) }} to see your performance here!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($deals->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $deals->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
