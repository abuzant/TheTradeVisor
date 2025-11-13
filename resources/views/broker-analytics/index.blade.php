@section('title', 'Broker Comparison Analytics - TheTradeVisor | Compare MT5 Brokers')
@section('description', 'Compare MetaTrader 5 brokers based on performance, reliability, costs, and trading conditions. Analyze broker statistics and make informed decisions.')
@section('og_title', 'Broker Comparison Analytics - TheTradeVisor')
@section('og_description', 'Comprehensive broker comparison and analytics for MT5 traders')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Broker Comparison Analytics') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">Compare brokers and analyze trading conditions</p>
            </div>

            {{-- Time Period Filter --}}
            <div class="flex gap-2">
                <a href="{{ route('broker.analytics', ['days' => 7]) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 7 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm' }}">
                    7 Days
                </a>
                <a href="{{ route('broker.analytics', ['days' => 30]) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 30 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm' }}">
                    30 Days
                </a>
                <a href="{{ route('broker.analytics', ['days' => 90]) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 90 ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm' }}">
                    90 Days
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(empty($analytics['brokers']))
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card rounded-xl">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Broker Data Available</h3>
                        <p class="mt-1 text-sm text-gray-500">There are no active trading accounts to analyze yet.</p>
                    </div>
                </div>
            @else

            {{-- Summary Cards --}}
            @if($analytics['summary'])
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="rounded-lg p-6 border border-blue-200" style="background: linear-gradient(to bottom right, #eff6ff, #dbeafe);">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-blue-600 font-medium">Most Popular</div>
                            <div class="text-xl font-bold text-blue-900">{{ $analytics['summary']['most_popular'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg p-6 border border-green-200" style="background: linear-gradient(to bottom right, #f0fdf4, #dcfce7);">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-green-600 font-medium">Most Reliable</div>
                            <div class="text-xl font-bold text-green-900">{{ $analytics['summary']['most_reliable'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg p-6 border border-purple-200" style="background: linear-gradient(to bottom right, #faf5ff, #f3e8ff);">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500 text-white mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-purple-600 font-medium">Lowest Cost</div>
                            <div class="text-xl font-bold text-purple-900">{{ $analytics['summary']['lowest_cost'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg p-6 border border-orange-200" style="background: linear-gradient(to bottom right, #fff7ed, #ffedd5);">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-500 text-white mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-orange-600 font-medium">Best Performance</div>
                            <div class="text-xl font-bold text-orange-900">{{ $analytics['summary']['best_performance'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Broker Comparison Table --}}
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                <div class="p-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">📊 Broker Overview</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Broker</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accounts</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uptime</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Win Rate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost/Trade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reliability</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($analytics['brokers'] as $broker)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <x-broker-name :broker="$broker['broker_name']" class="text-indigo-600 hover:text-indigo-900" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $broker['accounts']['total_accounts'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-500">{{ $broker['accounts']['active_accounts'] ?? 0 }} active</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $broker['accounts']['activity_rate'] ?? 0 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $broker['accounts']['activity_rate'] ?? 0 }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($broker['reliability'])
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $broker['reliability']['uptime_percentage'] >= 90 ? 'bg-green-100 text-green-800' : ($broker['reliability']['uptime_percentage'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $broker['reliability']['uptime_percentage'] }}%
                                        </span>
                                        @else
                                        <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($broker['performance'])
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $broker['performance']['win_rate'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $broker['performance']['win_rate'] }}%
                                        </span>
                                        @else
                                        <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($broker['costs'])
                                            USD {{ number_format($broker['costs']['cost_per_trade'], 2) }}
                                        @else
                                        <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($broker['reliability'])
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $broker['reliability']['reliability_score'] }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $broker['reliability']['reliability_score'] }}</span>
                                        </div>
                                        @else
                                        <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Detailed Broker Cards --}}
            @foreach($analytics['brokers'] as $broker)
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent border-b pb-2">
                        <x-broker-name :broker="$broker['broker_name']" class="text-indigo-600 hover:text-indigo-900" />
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Account Stats --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Account Statistics
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Accounts:</span>
                                    <span class="font-semibold">{{ $broker['accounts']['total_accounts'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Active:</span>
                                    <span class="font-semibold text-green-600">{{ $broker['accounts']['active_accounts'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Recently Active:</span>
                                    <span class="font-semibold">{{ $broker['accounts']['recently_active'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Avg Balance:</span>
                                    <span class="font-semibold">{{ $broker['accounts']['avg_balance_currency'] ?? 'USD' }} {{ number_format($broker['accounts']['avg_balance'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Cost Analysis --}}
                        @if($broker['costs'])
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Cost Analysis
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Commission:</span>
                                    <span class="font-semibold">USD {{ number_format($broker['costs']['total_commission'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Swap:</span>
                                    <span class="font-semibold">USD {{ number_format($broker['costs']['total_swap'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Avg Cost/Trade:</span>
                                    <span class="font-semibold text-orange-600">USD {{ number_format($broker['costs']['cost_per_trade'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Avg Cost/Lot:</span>
                                    <span class="font-semibold">USD {{ number_format($broker['costs']['avg_commission_per_lot'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Performance --}}
                        @if($broker['performance'])
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Performance Metrics
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Trades:</span>
                                    <span class="font-semibold">{{ number_format($broker['performance']['total_trades']) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Win Rate:</span>
                                    <span class="font-semibold {{ $broker['performance']['win_rate'] >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $broker['performance']['win_rate'] }}%
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Profit Factor:</span>
                                    <span class="font-semibold">{{ $broker['performance']['profit_factor'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Profit:</span>
                                    <span class="font-semibold {{ $broker['performance']['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        USD {{ number_format($broker['performance']['total_profit'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Spreads Table --}}
                    @if(!empty($broker['spreads']))
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-700 mb-3">📉 Average Spreads (Top Symbols)</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Avg Spread (Pips)</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sample Size</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($broker['spreads'] as $spread)
                                    <tr>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $spread['symbol'] }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $spread['avg_spread_pips'] }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ $spread['sample_size'] }} trades</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Reliability Info --}}
                    @if($broker['reliability'])
                    <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <h4 class="font-semibold text-blue-900 mb-2">🔒 Reliability Metrics</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <div class="text-blue-600">Uptime</div>
                                <div class="text-xl font-bold text-blue-900">{{ $broker['reliability']['uptime_percentage'] }}%</div>
                            </div>
                            <div>
                                <div class="text-blue-600">Synced (24h)</div>
                                <div class="text-xl font-bold text-blue-900">{{ $broker['reliability']['recently_synced_24h'] }}</div>
                            </div>
                            <div>
                                <div class="text-blue-600">Avg Sync Gap</div>
                                <div class="text-xl font-bold text-blue-900">{{ $broker['reliability']['avg_sync_gap_minutes'] }}m</div>
                            </div>
                            <div>
                                <div class="text-blue-600">Reliability Score</div>
                                <div class="text-xl font-bold text-blue-900">{{ $broker['reliability']['reliability_score'] }}/100</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Top Traded Symbols --}}
            @if(!empty($analytics['top_symbols']))
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                <div class="p-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">🔥 Top Traded Symbols (All Brokers)</h2>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        @foreach($analytics['top_symbols'] as $symbol)
                        <div class="rounded-lg p-4 text-center border border-indigo-200" style="background: linear-gradient(to bottom right, #eef2ff, #e0e7ff);">
                            <div class="text-2xl font-bold text-indigo-900">{{ $symbol['symbol'] }}</div>
                            <div class="text-sm text-indigo-600">{{ number_format($symbol['trades']) }} trades</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @endif
        </div>
    </div>
</x-app-layout>
