@section('title', 'Enterprise Analytics - TheTradeVisor')

<x-enterprise-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Enterprise Analytics') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">User activity and engagement metrics</p>
            </div>
            <a href="{{ route('enterprise.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Active Accounts (7d)</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_accounts_7d']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Trades ({{ $days }}d)</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_trades']) }}</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Time Period Selector --}}
            <div class="flex justify-center space-x-2 mb-6">
                <a href="{{ route('enterprise.analytics', ['days' => 7]) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $days == 7 ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                    7 Days
                </a>
                <a href="{{ route('enterprise.analytics', ['days' => 30]) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $days == 30 ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                    30 Days
                </a>
                <a href="{{ route('enterprise.analytics', ['days' => 90]) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $days == 90 ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                    90 Days
                </a>
                <a href="{{ route('enterprise.analytics', ['days' => 180]) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $days == 180 ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                    180 Days
                </a>
            </div>

            {{-- Trading Performance Metrics --}}
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">💰 Trading Performance (Last {{ $days }} Days)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Win Rate --}}
                    <div class="bg-yellow-50 rounded-xl p-6 border-2 border-yellow-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-yellow-600 mb-1">Win Rate</p>
                        <p class="text-3xl font-bold text-yellow-900">{{ number_format($stats['win_rate'], 1) }}%</p>
                        <p class="text-xs text-yellow-600 mt-1">{{ number_format($stats['winning_trades']) }} wins / {{ number_format($stats['total_trades']) }} trades</p>
                    </div>

                    {{-- Profit Factor --}}
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-orange-600 mb-1">Profit Factor</p>
                        <p class="text-3xl font-bold text-orange-900">{{ number_format($stats['profit_factor'], 2) }}</p>
                        <p class="text-xs text-orange-600 mt-1">{{ $stats['profit_factor'] > 1 ? '✓ Profitable' : '✗ Unprofitable' }}</p>
                    </div>

                    {{-- Total Profit --}}
                    <div class="bg-green-50 rounded-xl p-6 border-2 border-green-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-green-600 mb-1">Total Profit</p>
                        <p class="text-3xl font-bold {{ $stats['total_profit'] >= 0 ? 'text-green-900' : 'text-red-900' }}">${{ number_format($stats['total_profit'], 2) }}</p>
                    </div>

                    {{-- Net Profit --}}
                    <div class="bg-cyan-50 rounded-xl p-6 border-2 border-cyan-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-cyan-600 mb-1">Net Profit</p>
                        <p class="text-3xl font-bold {{ $stats['net_profit'] >= 0 ? 'text-cyan-900' : 'text-red-900' }}">${{ number_format($stats['net_profit'], 2) }}</p>
                        <p class="text-xs text-cyan-600 mt-1">After fees & swap</p>
                    </div>

                    {{-- Average Win --}}
                    <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-emerald-600 mb-1">Average Win</p>
                        <p class="text-3xl font-bold text-emerald-900">${{ number_format($stats['avg_win'], 2) }}</p>
                    </div>

                    {{-- Average Loss --}}
                    <div class="bg-rose-50 rounded-xl p-6 border-2 border-rose-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-rose-600 mb-1">Average Loss</p>
                        <p class="text-3xl font-bold text-rose-900">${{ number_format($stats['avg_loss'], 2) }}</p>
                    </div>

                    {{-- Best Trade --}}
                    <div class="bg-green-50 rounded-xl p-6 border-2 border-green-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-green-600 mb-1">Best Trade</p>
                        <p class="text-3xl font-bold text-green-900">${{ number_format($stats['best_trade'], 2) }}</p>
                    </div>

                    {{-- Worst Trade --}}
                    <div class="bg-red-50 rounded-xl p-6 border-2 border-red-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-red-600 mb-1">Worst Trade</p>
                        <p class="text-3xl font-bold text-red-900">${{ number_format($stats['worst_trade'], 2) }}</p>
                    </div>
                </div>
            </div>

            {{-- Account Balance Metrics --}}
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">💼 Account Balances</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Total Balance --}}
                    <div class="bg-blue-50 rounded-xl p-6 border-2 border-blue-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-blue-600 mb-1">Total Balance</p>
                        <p class="text-3xl font-bold text-blue-900">${{ number_format($stats['total_balance'], 2) }}</p>
                    </div>

                    {{-- Total Equity --}}
                    <div class="bg-indigo-50 rounded-xl p-6 border-2 border-indigo-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-indigo-600 mb-1">Total Equity</p>
                        <p class="text-3xl font-bold text-indigo-900">${{ number_format($stats['total_equity'], 2) }}</p>
                    </div>

                    {{-- Average Balance --}}
                    <div class="bg-purple-50 rounded-xl p-6 border-2 border-purple-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-purple-600 mb-1">Avg Balance</p>
                        <p class="text-3xl font-bold text-purple-900">${{ number_format($stats['avg_balance'], 2) }}</p>
                    </div>

                    {{-- Average Equity --}}
                    <div class="bg-pink-50 rounded-xl p-6 border-2 border-pink-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-pink-600 mb-1">Avg Equity</p>
                        <p class="text-3xl font-bold text-pink-900">${{ number_format($stats['avg_equity'], 2) }}</p>
                    </div>

                    {{-- Max Balance --}}
                    <div class="bg-teal-50 rounded-xl p-6 border-2 border-teal-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-teal-600 mb-1">Largest Account</p>
                        <p class="text-3xl font-bold text-teal-900">${{ number_format($stats['max_balance'], 2) }}</p>
                    </div>

                    {{-- Min Balance --}}
                    <div class="bg-gray-50 rounded-xl p-6 border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-gray-600 mb-1">Smallest Account</p>
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['min_balance'], 2) }}</p>
                    </div>

                    {{-- Average Leverage --}}
                    <div class="bg-amber-50 rounded-xl p-6 border-2 border-amber-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-amber-600 mb-1">Avg Leverage</p>
                        <p class="text-3xl font-bold text-amber-900">1:{{ number_format($stats['avg_leverage'], 0) }}</p>
                    </div>

                    {{-- Total Margin Used --}}
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-orange-600 mb-1">Total Margin</p>
                        <p class="text-3xl font-bold text-orange-900">${{ number_format($stats['total_margin_used'], 2) }}</p>
                    </div>
                </div>
            </div>

            {{-- Trading Volume Metrics --}}
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">📊 Trading Volume</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Winning Trades --}}
                    <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-emerald-600 mb-1">Winning Trades</p>
                        <p class="text-3xl font-bold text-emerald-900">{{ number_format($stats['winning_trades']) }}</p>
                        <p class="text-xs text-emerald-600 mt-1">{{ $stats['total_trades'] > 0 ? number_format(($stats['winning_trades'] / $stats['total_trades']) * 100, 1) : 0 }}%</p>
                    </div>

                    {{-- Losing Trades --}}
                    <div class="bg-red-50 rounded-xl p-6 border-2 border-red-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-red-600 mb-1">Losing Trades</p>
                        <p class="text-3xl font-bold text-red-900">{{ number_format($stats['losing_trades']) }}</p>
                        <p class="text-xs text-red-600 mt-1">{{ $stats['total_trades'] > 0 ? number_format(($stats['losing_trades'] / $stats['total_trades']) * 100, 1) : 0 }}%</p>
                    </div>

                    {{-- Breakeven Trades --}}
                    <div class="bg-gray-50 rounded-xl p-6 border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-gray-600 mb-1">Breakeven</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['breakeven_trades']) }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $stats['total_trades'] > 0 ? number_format(($stats['breakeven_trades'] / $stats['total_trades']) * 100, 1) : 0 }}%</p>
                    </div>

                    {{-- Total Volume --}}
                    <div class="bg-indigo-50 rounded-xl p-6 border-2 border-indigo-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-indigo-600 mb-1">Total Volume</p>
                        <p class="text-3xl font-bold text-indigo-900">{{ number_format($stats['total_volume'], 2) }}</p>
                        <p class="text-xs text-indigo-600 mt-1">Lots</p>
                    </div>

                    {{-- Avg Volume Per Trade --}}
                    <div class="bg-purple-50 rounded-xl p-6 border-2 border-purple-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-purple-600 mb-1">Avg Volume/Trade</p>
                        <p class="text-3xl font-bold text-purple-900">{{ number_format($stats['avg_volume_per_trade'], 2) }}</p>
                        <p class="text-xs text-purple-600 mt-1">Lots</p>
                    </div>

                    {{-- Max Volume Trade --}}
                    <div class="bg-pink-50 rounded-xl p-6 border-2 border-pink-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-pink-600 mb-1">Largest Trade</p>
                        <p class="text-3xl font-bold text-pink-900">{{ number_format($stats['max_volume_trade'], 2) }}</p>
                        <p class="text-xs text-pink-600 mt-1">Lots</p>
                    </div>

                    {{-- Most Traded Symbol --}}
                    <div class="bg-blue-50 rounded-xl p-6 border-2 border-blue-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-blue-600 mb-1">Most Traded</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['most_traded_symbol'] ?? 'N/A' }}</p>
                    </div>

                    {{-- Most Profitable Symbol --}}
                    <div class="bg-green-50 rounded-xl p-6 border-2 border-green-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-green-600 mb-1">Most Profitable</p>
                        <p class="text-2xl font-bold text-green-900">{{ $stats['most_profitable_symbol'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Fees & Costs --}}
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">💸 Fees & Costs</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Total Commission --}}
                    <div class="bg-amber-50 rounded-xl p-6 border-2 border-amber-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-amber-600 mb-1">Total Commission</p>
                        <p class="text-3xl font-bold text-amber-900">${{ number_format(abs($stats['total_commission']), 2) }}</p>
                    </div>

                    {{-- Total Swap --}}
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-orange-600 mb-1">Total Swap</p>
                        <p class="text-3xl font-bold {{ $stats['total_swap'] >= 0 ? 'text-orange-900' : 'text-red-900' }}">${{ number_format($stats['total_swap'], 2) }}</p>
                    </div>

                    {{-- Avg Profit Per Trade --}}
                    <div class="bg-lime-50 rounded-xl p-6 border-2 border-lime-200 shadow-lg hover:shadow-xl transition-shadow">
                        <p class="text-sm font-medium text-lime-600 mb-1">Avg Profit/Trade</p>
                        <p class="text-3xl font-bold {{ $stats['avg_profit_per_trade'] >= 0 ? 'text-lime-900' : 'text-red-900' }}">${{ number_format($stats['avg_profit_per_trade'], 2) }}</p>
                    </div>
                </div>
            </div>

            {{-- Users Table --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">User Activity</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Seen</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $usage)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $usage->user->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $usage->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $usage->account_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $usage->first_seen_at ? $usage->first_seen_at->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $usage->last_seen_at ? $usage->last_seen_at->diffForHumans() : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($usage->last_seen_at && $usage->last_seen_at->gt(now()->subDays(7)))
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                        No users found. Users will appear here once they connect accounts with your broker.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-enterprise-layout>
