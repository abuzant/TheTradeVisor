<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $broker }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Broker Performance & Statistics</p>
            </div>

            {{-- Time Period Filter --}}
            <div class="flex gap-2">
                <a href="{{ route('broker-details', ['broker' => urlencode($broker), 'days' => 7]) }}"
                   class="px-4 py-2 rounded-md text-sm {{ $days == 7 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    7 Days
                </a>
                <a href="{{ route('broker-details', ['broker' => urlencode($broker), 'days' => 30]) }}"
                   class="px-4 py-2 rounded-md text-sm {{ $days == 30 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    30 Days
                </a>
                <a href="{{ route('broker-details', ['broker' => urlencode($broker), 'days' => 90]) }}"
                   class="px-4 py-2 rounded-md text-sm {{ $days == 90 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    90 Days
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Overview Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Your Accounts</div>
                    <div class="text-3xl font-bold text-indigo-600 mt-2">{{ $stats['total_accounts'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $stats['active_accounts'] }} active</div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Open Positions</div>
                    <div class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['open_positions'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Currently active</div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Total Trades</div>
                    <div class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($stats['total_trades']) }}</div>
                    <div class="text-xs text-gray-500 mt-1">Last {{ $days }} days</div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Win Rate</div>
                    <div class="text-3xl font-bold {{ $stats['win_rate'] >= 50 ? 'text-green-600' : 'text-red-600' }} mt-2">
                        {{ $stats['win_rate'] }}%
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Success rate</div>
                </div>

            </div>

            {{-- Per-Account Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($userAccounts as $account)
                <div class="bg-white rounded-lg shadow p-6 border-l-4 {{ $account->is_active ? 'border-green-500' : 'border-gray-300' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-sm text-gray-600">Account</div>
                            <a href="{{ route('account.show', $account->id) }}" class="text-lg font-bold text-indigo-600 hover:text-indigo-800">
                                {{ $account->account_number }}
                            </a>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Balance:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $account->account_currency }} {{ number_format($account->balance, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Equity:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $account->account_currency }} {{ number_format($account->equity, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Profit:</span>
                            <span class="text-sm font-semibold {{ $account->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $account->account_currency }} {{ number_format($account->profit, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <span class="text-sm text-gray-600">Positions:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $account->openPositions->count() }}</span>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm text-gray-600">Total Volume</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_volume'], 1) }}</div>
                    <div class="text-xs text-gray-500 mt-1">Lots traded</div>
                </div>

            </div>

            {{-- Servers Info --}}
            @if($servers->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Connected Servers</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($servers as $server)
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                            {{ $server }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Daily Profit Trend --}}
            @if($dailyProfitTrend->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Profit Trend</h3>
                    <div class="h-64">
                        <canvas id="profitTrendChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- Top Traded Symbols --}}
            @if($topSymbols->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Traded Symbols</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trades</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topSymbols as $symbol)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $symbol->normalized_symbol }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($symbol->trades) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($symbol->volume, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif


        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if($dailyProfitTrend->count() > 0)
        // Daily Profit Trend Chart
        const profitCtx = document.getElementById('profitTrendChart').getContext('2d');
        const profitData = {!! json_encode($dailyProfitTrend) !!};

        new Chart(profitCtx, {
            type: 'line',
            data: {
                labels: profitData.map(d => d.date),
                datasets: [{
                    label: 'Daily Profit',
                    data: profitData.map(d => parseFloat(d.profit)),
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
