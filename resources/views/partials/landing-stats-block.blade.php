<section id="analytics" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Global Trading Analytics</h2>
            <p class="text-lg text-gray-600">Live data from our network <em class="text-xs">(Last 30 days)</em></p>
        </div>

        {{-- The requested stats block --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_traders']) }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Traders</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-green-600">{{ number_format($stats['active_traders']) }}</div>
                <div class="text-sm text-gray-600 mt-1">Active Traders</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_trades_30d']) }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Trades</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['avg_trades_per_day']) }}</div>
                <div class="text-sm text-gray-600 mt-1">Avg Trades/Day</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_volume_30d'], 0) }} <em class="text-xs font-normal text-gray-400">lots</em></div>
                <div class="text-sm text-gray-600 mt-1">Total Volume</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['avg_position_size'], 2) }} <em class="text-xs font-normal text-gray-400">lots</em></div>
                <div class="text-sm text-gray-600 mt-1">Avg Position Size</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ $stats['countries'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Countries</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ $stats['top_country']->country_code ?? 'N/A' }}</div>
                <div class="text-sm text-gray-600 mt-1">Top Country</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total_symbols'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Symbols Traded</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['most_traded_symbol']->symbol ?? 'N/A' }}</div>
                <div class="text-sm text-gray-600 mt-1">Most Traded</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-green-600">{{ number_format($stats['winning_trades']) }}</div>
                <div class="text-sm text-gray-600 mt-1">Winning Trades</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-red-600">{{ number_format($stats['losing_trades']) }}</div>
                <div class="text-sm text-gray-600 mt-1">Losing Trades</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-green-600">{{ $stats['win_rate'] }}%</div>
                <div class="text-sm text-gray-600 mt-1">Global Win Rate</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-green-700">${{ number_format(abs($stats['total_profit_30d']), 0) }}</div>
                <div class="text-sm text-gray-600 mt-1">Total P/L</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total_brokers'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Brokers</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['mt4_accounts'] }}</div>
                <div class="text-sm text-gray-600 mt-1">MT4 Accounts</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['mt5_accounts'] }}</div>
                <div class="text-sm text-gray-600 mt-1">MT5 Accounts</div>
            </div>
            <div class="stat-card bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['data_points']) }}</div>
                <div class="text-sm text-gray-600 mt-1">Data Points</div>
            </div>
        </div>
    </div>
</section>
