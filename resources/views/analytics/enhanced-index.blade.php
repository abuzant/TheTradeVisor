@extends('layouts.app')

@section('title', 'Advanced Global Trading Analytics - TheTradeVisor | Worldwide MT5 Trading Insights')
@section('description', 'Explore comprehensive trading analytics from thousands of MT5 traders worldwide. Real-time insights on country-platform matrices, symbol heatmaps, risk analytics, and market correlations.')
@section('og_title', 'Advanced Global Trading Analytics - TheTradeVisor')
@section('og_description', 'Comprehensive trading analytics and insights from MT5 traders around the world')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Advanced Global Analytics') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">Comprehensive insights from traders worldwide</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info Banner --}}
            <div class="bg-purple-50 border-l-4 border-purple-500 rounded-r-lg p-4 shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-purple-700">
                            <strong>Enhanced Analytics:</strong> 15+ interconnected data blocks showing country-platform matrices, symbol heatmaps, risk analytics, correlations, and real-time activity monitoring.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Time Period Selector --}}
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card rounded-xl p-4 mb-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Select Time Period</h3>
                        <p class="text-sm text-gray-600">View analytics for different time ranges</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('analytics', ['days' => 1]) }}"
                           class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 1 ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm border border-gray-200' }}">
                            Today
                        </a>
                        <a href="{{ route('analytics', ['days' => 7]) }}"
                           class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 7 ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm border border-gray-200' }}">
                            7 Days
                        </a>
                        <a href="{{ route('analytics', ['days' => 30]) }}"
                           class="px-4 py-2 rounded-lg font-medium transition-all duration-300 {{ $days == 30 ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 shadow-sm border border-gray-200' }}">
                            30 Days
                        </a>
                    </div>
                </div>
            </div>

            {{-- Enhanced Overview Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
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

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #f97316, #ea580c);">
                    <div class="text-orange-100 text-sm font-medium">Countries</div>
                    <div class="text-3xl font-bold mt-2">{{ $analytics['overview']['countries'] }}</div>
                    <div class="text-orange-100 text-xs mt-1">Global reach</div>
                </div>

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #a855f7, #9333ea);">
                    <div class="text-purple-100 text-sm font-medium">Platforms</div>
                    <div class="text-3xl font-bold mt-2">2</div>
                    <div class="text-purple-100 text-xs mt-1">MT4 & MT5</div>
                </div>

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #ec4899, #db2777);">
                    <div class="text-pink-100 text-sm font-medium">Brokers</div>
                    <div class="text-3xl font-bold mt-2">{{ $analytics['overview']['total_brokers'] }}</div>
                    <div class="text-pink-100 text-xs mt-1">Network</div>
                </div>

                <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to bottom right, #3b82f6, #2563eb);">
                    <div class="text-blue-100 text-sm font-medium">Open Positions</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($analytics['overview']['open_positions']) }}</div>
                    <div class="text-blue-100 text-xs mt-1">Currently active</div>
                </div>
            </div>

            {{-- ROW 1: Country-Platform Matrix & Symbol Heatmap --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Country-Platform Analytics Matrix --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">🌍 Country-Platform Matrix</h2>
                        
                        @if($analytics['country_platform_matrix'] && $analytics['country_platform_matrix']->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Platform</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Trades</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Win Rate</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($analytics['country_platform_matrix']->take(8) as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $item['country'] }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['platform_type'] === 'MT5' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $item['platform_type'] }}
                                            </span>
                                            <span class="ml-1 text-xs text-gray-500">{{ $item['account_mode'] }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ $item['total_trades'] }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $item['win_rate'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $item['win_rate'] }}%
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm font-medium {{ $item['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $displayCurrency }} {{ number_format($item['total_profit'], 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">No country-platform data available yet</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Symbol-Country Performance Heatmap --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">🔥 Symbol-Country Heatmap</h2>
                        
                        @if($analytics['symbol_country_heatmap'] && $analytics['symbol_country_heatmap']->isNotEmpty())
                        @php
                            $groupedHeatmap = $analytics['symbol_country_heatmap']
                                ->groupBy('symbol')
                                ->map(function($group) {
                                    $first = $group->first();
                                    $totalTrades = $group->sum('total_trades');
                                    $totalProfit = $group->sum('total_profit');
                                    $winningTrades = $group->sum(function($item) {
                                        return $item['total_trades'] * ($item['win_rate'] / 100);
                                    });
                                    $winRate = $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0;

                                    $performanceScore = $group->avg('performance_score');

                                    return [
                                        'symbol' => $first['symbol'],
                                        'country' => $first['country'],
                                        'total_trades' => $totalTrades,
                                        'total_profit' => $totalProfit,
                                        'win_rate' => $winRate,
                                        'performance_score' => $performanceScore,
                                    ];
                                })
                                ->sortByDesc('total_trades')
                                ->values();
                        @endphp
                        <div class="grid grid-cols-1 gap-2 max-h-96 overflow-y-auto">
                            @foreach($groupedHeatmap->take(15) as $item)
                            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50" 
                                 style="background: linear-gradient(90deg, rgba({{ $item['performance_score'] > 50 ? '34, 197, 94' : '239, 68, 68' }}, {{ $item['performance_score'] / 200 }}) 0%, transparent 100%);">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-bold text-gray-900">{{ $item['symbol'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $item['country'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2 text-xs">
                                    <span class="font-medium">{{ $item['total_trades'] }} trades</span>
                                    <span class="{{ $item['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $displayCurrency }}{{ number_format($item['total_profit'], 0) }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $item['win_rate'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item['win_rate'] }}%
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">No symbol-country data available yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ROW 2: Trading Sessions & Risk Analytics --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Trading Session Analysis --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">🌏 Trading Session Analysis</h2>
                        
                        @if($analytics['trading_sessions'] && $analytics['trading_sessions']->isNotEmpty())
                        <div class="mb-4">
                            <div class="h-64">
                                <canvas id="sessionComparisonChart"></canvas>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @foreach($analytics['trading_sessions'] as $session)
                            <div class="border-l-4 {{ $session['total_profit'] >= 0 ? 'border-green-500' : 'border-red-500' }} bg-gray-50 p-4 rounded-r-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $session['session_name'] }}</h3>
                                        <p class="text-sm text-gray-600">{{ $session['time_range'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold {{ $session['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $displayCurrency }} {{ number_format($session['total_profit'], 0) }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $session['total_trades'] }} trades</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Win Rate:</span>
                                        <span class="font-medium {{ $session['win_rate'] >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $session['win_rate'] }}%</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Volume:</span>
                                        <span class="font-medium">{{ number_format($session['total_volume'], 0) }} lots</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Avg/Trade:</span>
                                        <span class="font-medium">{{ $displayCurrency }}{{ number_format($session['avg_profit'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">No trading session data available yet</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Risk Analytics Dashboard --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">⚠️ Risk Analytics Dashboard</h2>
                        
                        @if($analytics['risk_analytics'] && $analytics['risk_analytics']->isNotEmpty())
                        @php
                            $groupedRiskAnalytics = $analytics['risk_analytics']
                                ->groupBy('symbol')
                                ->map(function($group) {
                                    $first = $group->first();
                                    $totalTrades = $group->sum('total_trades');
                                    $totalProfit = $group->sum('total_profit');
                                    $winRate = $group->avg('win_rate');
                                    $lossRate = $group->avg('loss_rate');
                                    $avgWin = $group->avg('avg_win');
                                    $avgLoss = $group->avg('avg_loss');
                                    $maxWin = $group->max('max_win');
                                    $maxLoss = $group->min('max_loss');
                                    $profitFactor = $group->avg('profit_factor');
                                    $riskReward = $group->avg('risk_reward_ratio');
                                    $volatility = $group->avg('volatility');
                                    $riskScore = $group->avg('risk_score');
                                    $riskLevel = $group->sortByDesc('risk_score')->first()['risk_level'];

                                    return [
                                        'symbol' => $first['symbol'],
                                        'total_trades' => $totalTrades,
                                        'total_profit' => $totalProfit,
                                        'win_rate' => round($winRate, 1),
                                        'loss_rate' => round($lossRate, 1),
                                        'avg_win' => $avgWin,
                                        'avg_loss' => $avgLoss,
                                        'max_win' => $maxWin,
                                        'max_loss' => $maxLoss,
                                        'profit_factor' => $profitFactor,
                                        'risk_reward_ratio' => $riskReward,
                                        'volatility' => $volatility,
                                        'risk_score' => round($riskScore, 1),
                                        'risk_level' => $riskLevel,
                                    ];
                                })
                                ->sortByDesc('risk_score')
                                ->values();
                        @endphp
                        <div class="mb-4">
                            <div class="h-64">
                                <canvas id="riskAnalyticsChart"></canvas>
                            </div>
                        </div>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($groupedRiskAnalytics->take(10) as $item)
                            <div class="border rounded-lg p-3 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">{{ $item['symbol'] }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $item['risk_level'] === 'Very High' ? 'bg-red-100 text-red-800' : ($item['risk_level'] === 'High' ? 'bg-orange-100 text-orange-800' : ($item['risk_level'] === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                            {{ $item['risk_level'] }}
                                        </span>
                                        <span class="text-xs text-gray-500">Score: {{ $item['risk_score'] }}</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-4 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-600">WR:</span>
                                        <span class="font-medium">{{ $item['win_rate'] }}%</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">PF:</span>
                                        <span class="font-medium text-blue-600">{{ $item['profit_factor'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">RR:</span>
                                        <span class="font-medium text-purple-600">{{ $item['risk_reward_ratio'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Vol:</span>
                                        <span class="font-medium text-orange-600">{{ $item['volatility'] }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">No risk analytics data available yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ROW 3: Performance Leaderboards --}}
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                <div class="p-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">🏆 Performance Leaderboards</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Top Countries --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">🌍 Top Countries by Profit</h3>
                            @if($analytics['performance_leaderboards']['top_countries'] && $analytics['performance_leaderboards']['top_countries']->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($analytics['performance_leaderboards']['top_countries']->take(5) as $index => $country)
                                <div class="flex items-center justify-between p-2 rounded-lg {{ $index === 0 ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }}">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg font-bold {{ $index === 0 ? 'text-yellow-600' : 'text-gray-600' }}">{{ $index + 1 }}</span>
                                        <span class="text-sm font-medium">{{ $country['country'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold {{ $country['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $displayCurrency }}{{ number_format($country['total_profit'], 0) }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $country['unique_accounts'] }} accounts</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-sm text-gray-500">No data available</p>
                            @endif
                        </div>

                        {{-- Top Symbols --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">📈 Top Symbols by Win Rate</h3>
                            @if($analytics['performance_leaderboards']['top_symbols'] && $analytics['performance_leaderboards']['top_symbols']->isNotEmpty())
                            @php
                                $groupedTopSymbols = $analytics['performance_leaderboards']['top_symbols']
                                    ->groupBy('symbol')
                                    ->map(function($group) {
                                        $totalTrades = $group->sum('total_trades');
                                        $winningTrades = $group->sum('winning_trades');
                                        $totalProfit = $group->sum('total_profit');
                                        $winRate = $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0;

                                        return [
                                            'symbol' => $group->first()['symbol'],
                                            'win_rate' => $winRate,
                                            'total_trades' => $totalTrades,
                                            'total_profit' => $totalProfit,
                                        ];
                                    })
                                    ->sortByDesc('win_rate')
                                    ->values();
                            @endphp
                            <div class="space-y-2">
                                @foreach($groupedTopSymbols->take(5) as $index => $symbol)
                                <div class="flex items-center justify-between p-2 rounded-lg {{ $index === 0 ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg font-bold {{ $index === 0 ? 'text-green-600' : 'text-gray-600' }}">{{ $index + 1 }}</span>
                                        <span class="text-sm font-medium">{{ $symbol['symbol'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-green-600">{{ $symbol['win_rate'] }}%</div>
                                        <div class="text-xs text-gray-500">{{ $symbol['total_trades'] }} trades</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-sm text-gray-500">No data available</p>
                            @endif
                        </div>

                        {{-- Top Brokers --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">🏢 Top Brokers by Volume</h3>
                            @if($analytics['performance_leaderboards']['top_brokers'] && $analytics['performance_leaderboards']['top_brokers']->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($analytics['performance_leaderboards']['top_brokers']->take(5) as $index => $broker)
                                <div class="flex items-center justify-between p-2 rounded-lg {{ $index === 0 ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50' }}">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg font-bold {{ $index === 0 ? 'text-blue-600' : 'text-gray-600' }}">{{ $index + 1 }}</span>
                                        <span class="text-sm font-medium">{{ $broker['broker_name'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-blue-600">{{ number_format($broker['total_volume'], 0) }} lots</div>
                                        <div class="text-xs text-gray-500">{{ $broker['unique_accounts'] }} accounts</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-sm text-gray-500">No data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 4: Real-Time Activity & Profit/Loss Distribution --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Real-Time Activity Monitor --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">📡 Real-Time Activity Monitor</h2>
                        
                        <div class="mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Last Updated:</span>
                                <span class="text-sm font-medium text-gray-500" data-last-updated="{{ $analytics['real_time_activity']['last_updated'] }}">
                                    {{ \Carbon\Carbon::parse($analytics['real_time_activity']['last_updated'])->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="h-48">
                                <canvas id="realTimeActivityChart"></canvas>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">🔥 Recent Activity (Last Hour)</h3>
                                @if($analytics['real_time_activity']['recent_activity'] && $analytics['real_time_activity']['recent_activity']->isNotEmpty())
                                <div class="space-y-2">
                                    @foreach($analytics['real_time_activity']['recent_activity']->take(5) as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium">{{ $item['symbol'] }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $item['activity_level'] === 'Very High' ? 'bg-red-100 text-red-800' : ($item['activity_level'] === 'High' ? 'bg-orange-100 text-orange-800' : ($item['activity_level'] === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                                {{ $item['activity_level'] }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium">{{ $item['trades_last_hour'] }} trades</div>
                                            <div class="text-xs {{ $item['profit_last_hour'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $displayCurrency }}{{ number_format($item['profit_last_hour'], 0) }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-sm text-gray-500">No recent activity</p>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">📊 Current Open Positions</h3>
                                @if($analytics['real_time_activity']['open_positions'] && $analytics['real_time_activity']['open_positions']->isNotEmpty())
                                <div class="space-y-2">
                                    @foreach($analytics['real_time_activity']['open_positions']->take(5) as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                        <span class="text-sm font-medium">{{ $item['symbol'] }}</span>
                                        <div class="text-right">
                                            <div class="text-sm font-medium">{{ $item['open_positions'] }} positions</div>
                                            <div class="text-xs {{ $item['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $displayCurrency }}{{ number_format($item['total_profit'], 0) }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-sm text-gray-500">No open positions</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Profit/Loss Distribution --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">💰 Profit/Loss Distribution</h2>
                        
                        @if($analytics['profit_loss_distribution']['total_trades'] > 0)
                        <div class="mb-4">
                            <div class="h-48">
                                <canvas id="profitLossDistributionChart"></canvas>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Win Rate</div>
                                <div class="text-2xl font-bold text-green-600">{{ $analytics['profit_loss_distribution']['win_rate'] }}%</div>
                                <div class="text-xs text-gray-500">{{ $analytics['profit_loss_distribution']['profitable_trades'] }}/{{ $analytics['profit_loss_distribution']['total_trades'] }} trades</div>
                            </div>
                            <div class="bg-red-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600">Avg Profit/Loss</div>
                                <div class="text-2xl font-bold text-red-600">{{ $displayCurrency }}{{ number_format($analytics['profit_loss_distribution']['avg_loss'], 2) }}</div>
                                <div class="text-xs text-gray-500">Loss per trade</div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">📈 Profit Distribution</h4>
                                @if(!empty($analytics['profit_loss_distribution']['profit_distribution']))
                                <div class="space-y-1">
                                    @foreach($analytics['profit_loss_distribution']['profit_distribution']->take(3) as $bucket)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600">{{ $bucket['range'] }}</span>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $bucket['percentage'] }}%"></div>
                                            </div>
                                            <span class="font-medium">{{ $bucket['percentage'] }}%</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">📉 Loss Distribution</h4>
                                @if(!empty($analytics['profit_loss_distribution']['loss_distribution']))
                                <div class="space-y-1">
                                    @foreach($analytics['profit_loss_distribution']['loss_distribution']->take(3) as $bucket)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600">{{ $bucket['range'] }}</span>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $bucket['percentage'] }}%"></div>
                                            </div>
                                            <span class="font-medium">{{ $bucket['percentage'] }}%</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">No profit/loss distribution data available yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ROW 5: Correlation Matrix --}}
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                <div class="p-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">🔗 Symbol Correlation Matrix</h2>
                    
                    <div class="mb-4">
                        <div class="h-64">
                            <canvas id="correlationHeatmap"></canvas>
                        </div>
                    </div>
                    
                    @if($analytics['correlation_matrix'] && $analytics['correlation_matrix']->isNotEmpty())
                    @php
                        $groupedCorrelation = $analytics['correlation_matrix']
                            ->groupBy(function($item) {
                                return $item['symbol1'].'|'.$item['symbol2'];
                            })
                            ->map(function($group) {
                                $first = $group->first();
                                $corr = $group->avg('correlation');
                                $strength = $group->sortByDesc(function($item) {
                                    return abs($item['correlation']);
                                })->first()['strength'];

                                return [
                                    'symbol1' => $first['symbol1'],
                                    'symbol2' => $first['symbol2'],
                                    'correlation' => round($corr, 3),
                                    'strength' => $strength,
                                ];
                            })
                            ->sortByDesc(function($item) {
                                return abs($item['correlation']);
                            })
                            ->values();
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Symbol 1</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Symbol 2</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Correlation</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Strength</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($groupedCorrelation->take(15) as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $item['symbol1'] }}</td>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $item['symbol2'] }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full" style="width: {{ abs($item['correlation']) * 100 }}%; background-color: {{ $item['correlation'] > 0 ? '#10b981' : '#ef4444' }}"></div>
                                            </div>
                                            <span class="font-medium {{ $item['correlation'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $item['correlation'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $item['strength'] === 'Very Strong' ? 'bg-purple-100 text-purple-800' : ($item['strength'] === 'Strong' ? 'bg-blue-100 text-blue-800' : ($item['strength'] === 'Moderate' ? 'bg-gray-100 text-gray-800' : 'bg-gray-50 text-gray-600')) }}">
                                            {{ $item['strength'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-sm">No correlation data available yet</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ROW 6: Trading Patterns & Market Volatility --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Trading Patterns Analysis --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">📅 Trading Patterns Analysis</h2>
                        
                        <div class="mb-4">
                            <div class="h-48">
                                <canvas id="dayOfWeekPerformanceChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">📊 Best Trading Days</h3>
                                @if($analytics['trading_patterns']['day_of_week_analysis'] && $analytics['trading_patterns']['day_of_week_analysis']->isNotEmpty())
                                <div class="space-y-2">
                                    @foreach($analytics['trading_patterns']['day_of_week_analysis']->sortByDesc('total_profit')->take(3) as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium">{{ $item['day_name'] }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $item['win_rate'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $item['win_rate'] }}% WR
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold {{ $item['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $displayCurrency }}{{ number_format($item['total_profit'], 0) }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $item['total_trades'] }} trades</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-sm text-gray-500">No day-of-week data available</p>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">🎯 Position Patterns</h3>
                                @if($analytics['trading_patterns']['position_patterns'] && $analytics['trading_patterns']['position_patterns']->isNotEmpty())
                                @php
                                    $groupedPositionPatterns = $analytics['trading_patterns']['position_patterns']
                                        ->groupBy(function($item) {
                                            return $item['symbol'].'|'.$item['type'];
                                        })
                                        ->map(function($group) {
                                            $first = $group->first();
                                            $totalCount = $group->sum('count');
                                            $avgProfit = $totalCount > 0
                                                ? $group->sum(function($item) { return $item['avg_profit'] * $item['count']; }) / $totalCount
                                                : 0;

                                            return [
                                                'symbol' => $first['symbol'],
                                                'type' => $first['type'],
                                                'count' => $totalCount,
                                                'avg_profit' => $avgProfit,
                                            ];
                                        })
                                        ->sortByDesc('count')
                                        ->values();
                                @endphp
                                <div class="space-y-2">
                                    @foreach($groupedPositionPatterns->take(5) as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium">{{ $item['symbol'] }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $item['type'] === 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ strtoupper($item['type']) }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium">{{ $item['count'] }} trades</div>
                                            <div class="text-xs {{ $item['avg_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $displayCurrency }}{{ number_format($item['avg_profit'], 2) }}/trade
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-sm text-gray-500">No position pattern data available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Market Volatility Analysis --}}
                <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">📈 Market Volatility Analysis</h2>
                        
                        <div class="mb-4">
                            <div class="h-48">
                                <canvas id="symbolPerformanceTrendsChart"></canvas>
                            </div>
                        </div>
                        
                        @if($analytics['market_volatility'] && $analytics['market_volatility']->isNotEmpty())
                        @php
                            $groupedVolatility = $analytics['market_volatility']
                                ->groupBy('symbol')
                                ->map(function($group) {
                                    $first = $group->first();
                                    $totalTrades = $group->sum('total_trades');
                                    $avgVol = $group->avg('avg_daily_volatility');
                                    $maxVol = $group->max('max_daily_volatility');
                                    $avgProfit = $group->avg('avg_profit');
                                    $riskLevel = $group->sortByDesc('avg_daily_volatility')->first()['risk_level'];
                                    $trend = $group->sortByDesc('total_trades')->first()['volatility_trend'];

                                    return [
                                        'symbol' => $first['symbol'],
                                        'avg_daily_volatility' => round($avgVol, 2),
                                        'max_daily_volatility' => round($maxVol, 2),
                                        'total_trades' => $totalTrades,
                                        'avg_profit' => round($avgProfit, 2),
                                        'risk_level' => $riskLevel,
                                        'volatility_trend' => $trend,
                                    ];
                                })
                                ->sortByDesc('avg_daily_volatility')
                                ->values();
                        @endphp
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach($groupedVolatility->take(8) as $item)
                            <div class="border rounded-lg p-3 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">{{ $item['symbol'] }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $item['risk_level'] === 'Very High' ? 'bg-red-100 text-red-800' : ($item['risk_level'] === 'High' ? 'bg-orange-100 text-orange-800' : ($item['risk_level'] === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                            {{ $item['risk_level'] }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $item['volatility_trend'] }}</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-600">Avg Vol:</span>
                                        <span class="font-medium text-orange-600">{{ $item['avg_daily_volatility'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Max Vol:</span>
                                        <span class="font-medium text-red-600">{{ $item['max_daily_volatility'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Trades:</span>
                                        <span class="font-medium">{{ $item['total_trades'] }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">No volatility data available yet</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Enhanced Analytics Charts
        document.addEventListener('DOMContentLoaded', function() {
            
            // Trading Session Comparison Chart
            const sessionCtx = document.getElementById('sessionComparisonChart');
            if (sessionCtx) {
                const sessionData = {!! json_encode($analytics['trading_sessions']) !!};
                new Chart(sessionCtx.getContext('2d'), {
                    type: 'radar',
                    data: {
                        labels: ['Total Trades', 'Total Profit', 'Win Rate', 'Total Volume', 'Avg Profit'],
                        datasets: sessionData.map((session, index) => ({
                            label: session.session_name,
                            data: [
                                session.total_trades / 10, // Scale down for radar chart
                                Math.abs(session.total_profit) / 100, // Scale profit
                                session.win_rate,
                                session.total_volume / 100, // Scale volume
                                Math.abs(session.avg_profit) * 10 // Scale avg profit
                            ],
                            backgroundColor: `rgba(${index === 0 ? '99, 102, 241' : (index === 1 ? '16, 185, 129' : (index === 2 ? '245, 158, 11' : '239, 68, 68'))}, 0.2)`,
                            borderColor: `rgba(${index === 0 ? '99, 102, 241' : (index === 1 ? '16, 185, 129' : (index === 2 ? '245, 158, 11' : '239, 68, 68'))}, 1)`,
                            borderWidth: 2,
                            pointBackgroundColor: `rgba(${index === 0 ? '99, 102, 241' : (index === 1 ? '16, 185, 129' : (index === 2 ? '245, 158, 11' : '239, 68, 68'))}, 1)`
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Trading Session Performance Comparison'
                            }
                        },
                        scales: {
                            r: {
                                beginAtZero: true,
                                ticks: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Risk Analytics Bubble Chart
            const riskCtx = document.getElementById('riskAnalyticsChart');
            if (riskCtx) {
                const riskData = {!! json_encode($analytics['risk_analytics']->take(15)) !!};
                new Chart(riskCtx.getContext('2d'), {
                    type: 'bubble',
                    data: {
                        datasets: [{
                            label: 'Symbol Risk Analysis',
                            data: riskData.map(item => ({
                                x: item.win_rate,
                                y: item.profit_factor,
                                r: Math.min(Math.max(item.volatility / 5, 5), 20) // Bubble size based on volatility
                            })),
                            backgroundColor: riskData.map(item => 
                                item.risk_level === 'Very High' ? 'rgba(239, 68, 68, 0.6)' :
                                item.risk_level === 'High' ? 'rgba(245, 158, 11, 0.6)' :
                                item.risk_level === 'Medium' ? 'rgba(59, 130, 246, 0.6)' :
                                'rgba(16, 185, 129, 0.6)'
                            ),
                            borderColor: riskData.map(item => 
                                item.risk_level === 'Very High' ? 'rgba(239, 68, 68, 1)' :
                                item.risk_level === 'High' ? 'rgba(245, 158, 11, 1)' :
                                item.risk_level === 'Medium' ? 'rgba(59, 130, 246, 1)' :
                                'rgba(16, 185, 129, 1)'
                            )
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Risk vs Reward Analysis (Bubble Size = Volatility)'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const item = riskData[context.dataIndex];
                                        return [
                                            `${item.symbol}`,
                                            `Win Rate: ${item.win_rate}%`,
                                            `Profit Factor: ${item.profit_factor}`,
                                            `Volatility: ${item.volatility}`,
                                            `Risk Level: ${item.risk_level}`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Win Rate (%)'
                                },
                                min: 0,
                                max: 100
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Profit Factor'
                                },
                                min: 0,
                                max: 5
                            }
                        }
                    }
                });
            }

            // Correlation Heatmap
            const correlationCtx = document.getElementById('correlationHeatmap');
            if (correlationCtx) {
                const correlationData = {!! json_encode($analytics['correlation_matrix']->take(20)) !!};
                
                // Create matrix data
                const symbols = [...new Set(correlationData.flatMap(d => [d.symbol1, d.symbol2]))];
                const matrix = [];
                
                symbols.forEach((symbol1, i) => {
                    symbols.forEach((symbol2, j) => {
                        const correlation = correlationData.find(d => 
                            (d.symbol1 === symbol1 && d.symbol2 === symbol2) ||
                            (d.symbol1 === symbol2 && d.symbol2 === symbol1)
                        );
                        matrix.push({
                            x: j,
                            y: i,
                            v: correlation ? Math.abs(correlation.correlation) : (i === j ? 1 : 0)
                        });
                    });
                });

                new Chart(correlationCtx.getContext('2d'), {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: 'Correlation Strength',
                            data: matrix,
                            backgroundColor: matrix.map(m => 
                                `rgba(${255 - (m.v * 200)}, ${m.v * 200}, 100, 0.8)`
                            )
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Symbol Correlation Heatmap'
                            }
                        },
                        scales: {
                            x: {
                                type: 'linear',
                                position: 'bottom',
                                ticks: {
                                    callback: function(value) {
                                        return symbols[value] || '';
                                    }
                                },
                                min: 0,
                                max: symbols.length - 1
                            },
                            y: {
                                type: 'linear',
                                ticks: {
                                    callback: function(value) {
                                        return symbols[value] || '';
                                    }
                                },
                                min: 0,
                                max: symbols.length - 1
                            }
                        }
                    }
                });
            }

            // Profit/Loss Distribution Chart
            const pldCtx = document.getElementById('profitLossDistributionChart');
            if (pldCtx) {
                const pldData = {!! json_encode($analytics['profit_loss_distribution']) !!};
                
                new Chart(pldCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Profitable Trades', 'Losing Trades', 'Breakeven'],
                        datasets: [{
                            label: 'Trade Count',
                            data: [
                                pldData.profitable_trades,
                                pldData.losing_trades,
                                pldData.breakeven_trades
                            ],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(156, 163, 175, 0.8)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(239, 68, 68, 1)',
                                'rgba(156, 163, 175, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Profit/Loss Trade Distribution'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Trades'
                                }
                            }
                        }
                    }
                });
            }

            // Real-Time Activity Line Chart
            const activityCtx = document.getElementById('realTimeActivityChart');
            if (activityCtx) {
                const activityData = {!! json_encode($analytics['real_time_activity']['recent_activity']) !!};
                
                new Chart(activityCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: activityData.map(d => d.symbol),
                        datasets: [{
                            label: 'Trades Last Hour',
                            data: activityData.map(d => d.trades_last_hour),
                            borderColor: 'rgba(99, 102, 241, 1)',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Profit Last Hour',
                            data: activityData.map(d => Math.abs(d.profit_last_hour)),
                            borderColor: 'rgba(16, 185, 129, 1)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Real-Time Trading Activity (Last Hour)'
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Number of Trades'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Profit (Abs)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            }

            // Day of Week Performance Chart
            const dayOfWeekCtx = document.getElementById('dayOfWeekPerformanceChart');
            if (dayOfWeekCtx) {
                const dayData = {!! json_encode($analytics['trading_patterns']['day_of_week_analysis']) !!};
                
                new Chart(dayOfWeekCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: dayData.map(d => d.day_name),
                        datasets: [{
                            label: 'Total Profit',
                            data: dayData.map(d => d.total_profit),
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 2,
                            yAxisID: 'y'
                        }, {
                            label: 'Win Rate (%)',
                            data: dayData.map(d => d.win_rate),
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 2,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Day of Week Performance Analysis'
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Total Profit'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Win Rate (%)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                min: 0,
                                max: 100
                            }
                        }
                    }
                });
            }

            // Symbol Performance Trends Chart
            const trendsCtx = document.getElementById('symbolPerformanceTrendsChart');
            if (trendsCtx) {
                const trendsData = {!! json_encode($analytics['symbol_performance_trends']) !!};
                
                new Chart(trendsCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: trendsData.map(d => d.symbol),
                        datasets: [{
                            label: 'Total Profit',
                            data: trendsData.map(d => d.total_profit),
                            borderColor: 'rgba(99, 102, 241, 1)',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Win Rate (%)',
                            data: trendsData.map(d => d.profitable_days / (d.profitable_days + d.losing_days) * 100),
                            borderColor: 'rgba(16, 185, 129, 1)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Symbol Performance Trends'
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Total Profit'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Profitable Days (%)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                min: 0,
                                max: 100
                            }
                        }
                    }
                });
            }

            // Auto-refresh for real-time activity
            setInterval(function() {
                const lastUpdated = document.querySelector('[data-last-updated]');
                if (lastUpdated) {
                    const now = new Date();
                    const lastUpdate = new Date(lastUpdated.dataset.lastUpdated);
                    const diff = Math.floor((now - lastUpdate) / 1000);
                    
                    if (diff < 60) {
                        lastUpdated.textContent = `${diff} seconds ago`;
                    } else if (diff < 3600) {
                        lastUpdated.textContent = `${Math.floor(diff / 60)} minutes ago`;
                    } else {
                        lastUpdated.textContent = `${Math.floor(diff / 3600)} hours ago`;
                    }
                }
            }, 1000);

        });
    </script>
    @endpush
</x-app-layout>
