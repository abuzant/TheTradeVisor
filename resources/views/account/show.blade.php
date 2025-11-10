<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <x-broker-name :broker="$account->broker_name" class="text-indigo-600 hover:text-indigo-900" /> - {{ $account->account_number ?? 'Account Details' }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Account Overview Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Balance --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Balance</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $account->account_currency }} {{ number_format($account->balance, 2) }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $account->account_currency }}</div>
                    </div>
                </div>

                {{-- Equity --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Equity</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $account->account_currency }} {{ number_format($account->equity, 2) }}</div>
                        <div class="text-xs text-gray-500 mt-1">Leverage: 1:{{ $account->leverage }}</div>
                    </div>
                </div>

                {{-- Profit --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Current P&L</div>
                        <div class="text-2xl font-bold {{ $account->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $account->account_currency }} {{ number_format($account->profit, 2) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Open positions</div>
                    </div>
                </div>

                {{-- Margin Level --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Margin Level</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $account->margin_level ? number_format($account->margin_level, 2) . '%' : 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Free: {{ $account->account_currency }} {{ number_format($account->free_margin, 2) }}</div>
                    </div>
                </div>

            </div>

            {{-- Trading Statistics --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Trading Statistics (Last 30 Days)</h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <div class="text-3xl font-bold text-gray-900">{{ $stats['total_trades'] }}</div>
                            <div class="text-sm text-gray-500">Total Trades</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-green-600">{{ $stats['winning_trades'] }}</div>
                            <div class="text-sm text-gray-500">Winning Trades</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-red-600">{{ $stats['losing_trades'] }}</div>
                            <div class="text-sm text-gray-500">Losing Trades</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-indigo-600">{{ $stats['win_rate'] }}%</div>
                            <div class="text-sm text-gray-500">Win Rate</div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-2xl font-bold {{ $stats['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $account->account_currency }} {{ number_format($stats['total_profit'], 2) }}
                            </div>
                            <div class="text-sm text-gray-500">Total P&L</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $account->account_currency }} {{ number_format($stats['avg_profit'], 2) }}</div>
                            <div class="text-sm text-gray-500">Avg per Trade</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['most_traded_symbol'] }}</div>
                            <div class="text-sm text-gray-500">Most Traded</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Equity Curve Chart --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Equity Curve (Last 30 Days)</h3>
                        <div class="h-64">
                            <canvas id="equityChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Symbol Distribution --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Trading by Symbol</h3>
                        <div class="h-64">
                            <canvas id="symbolChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Trading Hours Heatmap --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Trading Activity by Hour</h3>
                    <div class="h-48">
                        <canvas id="hoursChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Open Positions --}}
            @if($account->openPositions->isNotEmpty())
            <div class="bg-blue-50 border-2 border-blue-200 overflow-hidden shadow-sm sm:rounded-lg"
                 x-data="{
                     positions: {{ $account->openPositions->toJson() }},
                     sortColumn: 'open_time',
                     sortDirection: 'desc',
                     sortBy(column) {
                         if (this.sortColumn === column) {
                             this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                         } else {
                             this.sortColumn = column;
                             this.sortDirection = 'asc';
                         }
                         this.positions = this.positions.sort((a, b) => {
                             let aVal = a[column];
                             let bVal = b[column];
                             if (aVal === null) return 1;
                             if (bVal === null) return -1;
                             if (typeof aVal === 'string') {
                                 aVal = aVal.toLowerCase();
                                 bVal = bVal.toLowerCase();
                             }
                             return this.sortDirection === 'asc' ? (aVal > bVal ? 1 : -1) : (aVal < bVal ? 1 : -1);
                         });
                     }
                 }">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            📊 Open Positions <span class="ml-2 px-2 py-1 bg-gradient-to-r from-green-500 to-green-600 text-white text-xs rounded-full animate-pulse transition-all duration-1000">LIVE</span>
                        </h3>
                        <p class="text-xs text-blue-700">
                            💡 Profit shown is from last sync. Actual P/L may vary with current market prices.
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th @click="sortBy('symbol')" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none">
                                        <div class="flex items-center space-x-1">
                                            <span>Symbol</span>
                                            <span x-show="sortColumn === 'symbol'">
                                                <svg x-show="sortDirection === 'asc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                                <svg x-show="sortDirection === 'desc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th @click="sortBy('type')" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none">
                                        <div class="flex items-center space-x-1">
                                            <span>Type</span>
                                            <span x-show="sortColumn === 'type'">
                                                <svg x-show="sortDirection === 'asc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                                <svg x-show="sortDirection === 'desc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th @click="sortBy('volume')" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none">
                                        <div class="flex items-center space-x-1">
                                            <span>Volume</span>
                                            <span x-show="sortColumn === 'volume'">
                                                <svg x-show="sortDirection === 'asc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                                <svg x-show="sortDirection === 'desc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th @click="sortBy('open_price')" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none">
                                        <div class="flex items-center space-x-1">
                                            <span>Open Price</span>
                                            <span x-show="sortColumn === 'open_price'">
                                                <svg x-show="sortDirection === 'asc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                                <svg x-show="sortDirection === 'desc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th @click="sortBy('current_price')" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none">
                                        <div class="flex items-center space-x-1">
                                            <span>Current</span>
                                            <span x-show="sortColumn === 'current_price'">
                                                <svg x-show="sortDirection === 'asc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                                <svg x-show="sortDirection === 'desc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">S/L</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">T/P</th>
                                    <th @click="sortBy('profit')" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none">
                                        <div class="flex items-center space-x-1">
                                            <span>Profit</span>
                                            <span x-show="sortColumn === 'profit'">
                                                <svg x-show="sortDirection === 'asc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                                <svg x-show="sortDirection === 'desc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                    <th @click="sortBy('open_time')" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none">
                                        <div class="flex items-center space-x-1">
                                            <span>Opened</span>
                                            <span x-show="sortColumn === 'open_time'">
                                                <svg x-show="sortDirection === 'asc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                                                <svg x-show="sortDirection === 'desc'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="position in positions" :key="position.id">
                                    <tr class="bg-blue-50 hover:bg-blue-100 transition-colors">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a :href="`/trades/symbol/${position.normalized_symbol}`"
                                               class="text-indigo-600 hover:text-indigo-900"
                                               :title="`Raw: ${position.symbol}`"
                                               x-text="position.normalized_symbol"></a>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  :class="position.type === 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                  x-text="position.type.toUpperCase()"></span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500" x-text="Number(position.volume).toFixed(2).replace(/\.?0+$/, '')"></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500" x-text="Number(position.open_price).toFixed(5).replace(/\.?0+$/, '')"></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500" x-text="Number(position.current_price).toFixed(5).replace(/\.?0+$/, '')"></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500" x-text="position.sl ? Number(position.sl).toFixed(5).replace(/\.?0+$/, '') : '-'"></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500" x-text="position.tp ? Number(position.tp).toFixed(5).replace(/\.?0+$/, '') : '-'"></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium"
                                            :class="position.profit >= 0 ? 'text-green-600' : 'text-red-600'"
                                            x-text="`{{ $account->account_currency }} ${parseFloat(position.profit).toFixed(2)}`"></td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500" x-text="position.open_time_human"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Pending Orders --}}
            @if($account->activeOrders->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Orders</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">S/L</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">T/P</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($account->activeOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->normalized_symbol }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ strtoupper(str_replace('_', ' ', $order->type)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rtrim(rtrim(number_format($order->volume_current, 2), '0'), '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rtrim(rtrim(number_format($order->price_open, 5), '0'), '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->sl ? rtrim(rtrim(number_format($order->sl, 5), '0'), '.') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->tp ? rtrim(rtrim(number_format($order->tp, 5), '0'), '.') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->expiration ? $order->expiration->format('M d, H:i') : 'No expiry' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->time_setup->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Trading History --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Trades</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <x-sortable-header column="time" label="Time" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <x-sortable-header column="symbol" label="Symbol" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <x-sortable-header column="type" label="Type" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entry</th>
                                    <x-sortable-header column="volume" label="Volume" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <x-sortable-header column="price" label="Price" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <x-sortable-header column="profit" label="Profit" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($deals as $deal)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $deal->time->format('M d, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $deal->normalized_symbol }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $deal->type == 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ strtoupper($deal->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ strtoupper($deal->entry) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rtrim(rtrim(number_format($deal->volume, 2), '0'), '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rtrim(rtrim(number_format($deal->price, 5), '0'), '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $deal->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $account->account_currency }} {{ number_format($deal->profit, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ strtoupper($deal->reason) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $deals->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js Scripts --}}
    @push('scripts')
    <script>
        // Equity Curve Chart
        const equityCtx = document.getElementById('equityChart').getContext('2d');
        const equityChart = new Chart(equityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['equity']['labels']) !!},
                datasets: [{
                    label: 'Equity',
                    data: {!! json_encode($chartData['equity']['data']) !!},
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4,
                    fill: true
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
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Symbol Distribution Chart
        const symbolCtx = document.getElementById('symbolChart').getContext('2d');
        const symbolChart = new Chart(symbolCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['symbols']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['symbols']['data']) !!},
                    backgroundColor: [
                        'rgb(79, 70, 229)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)'
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

        // Trading Hours Chart
        const hoursCtx = document.getElementById('hoursChart').getContext('2d');
        const hoursChart = new Chart(hoursCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['hours']['labels']) !!},
                datasets: [{
                    label: 'Trades',
                    data: {!! json_encode($chartData['hours']['data']) !!},
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
    </script>
    @endpush
</x-app-layout>
