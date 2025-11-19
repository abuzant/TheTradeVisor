@section('title', 'Enterprise Dashboard - TheTradeVisor')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Enterprise Dashboard
                </h1>
                <p class="mt-1 text-sm text-gray-600">{{ $broker->company_name }} - Comprehensive Analytics</p>
            </div>
            <a href="{{ route('enterprise.settings') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50">
                Settings
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                        <p class="text-sm text-gray-500">Total Balance</p>
                        <p class="text-3xl font-bold">${{ number_format($stats['total_balance'], 2) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Total Equity</p>
                        <p class="text-3xl font-bold">${{ number_format($stats['total_equity'], 2) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow">
                        <p class="text-sm text-gray-500">Total Profit/Loss</p>
                        <p class="text-3xl font-bold {{ $stats['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($stats['total_profit'], 2) }}
                        </p>
                    </div>
                </div>

                @if(!empty($performance) && $performance['total_trades'] > 0)
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-4">Trading Performance (Last 30 Days)</h3>
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
                            <p class="text-sm text-gray-500">Profit</p>
                            <p class="text-2xl font-bold {{ $performance['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($performance['total_profit'], 2) }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                @if($symbolStats->count() > 0)
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold">Top Trading Symbols (Last 30 Days)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Trades</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Volume</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Win Rate</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($symbolStats as $symbol)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $symbol->symbol }}</td>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">${{ number_format($account->balance, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">${{ number_format($account->equity, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $account->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($account->profit, 2) }}
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
</x-app-layout>
