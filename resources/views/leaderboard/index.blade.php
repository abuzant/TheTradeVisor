@if(auth()->check())
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('🏆 Top Traders Leaderboard') }}
            </h2>
        </x-slot>
@else
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Top Traders Leaderboard | {{ config('app.name') }}</title>
        
        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            
            {{-- Simple Navigation for Guests --}}
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                                <img src="{{ asset('logo.svg') }}" alt="TheTradeVisor" class="h-8 w-8">
                                <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">TheTradeVisor</span>
                            </a>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Sign Up</a>
                        </div>
                    </div>
                </div>
            </nav>
@endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg p-8 mb-8 text-white">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-3">🏆 Top Traders</h1>
                    <p class="text-indigo-100 text-lg mb-6">
                        Discover the best performing traders on TheTradeVisor
                    </p>
                    <p class="text-sm text-indigo-200">
                        Rankings based on last 30 days performance • Updated daily
                    </p>
                </div>
            </div>

            {{-- Filter Tabs --}}
            <div class="bg-white rounded-xl shadow-card mb-6 p-4">
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="{{ route('leaderboard') }}?rank_by=total_profit" 
                       class="px-6 py-3 rounded-lg font-semibold transition-colors {{ $rankBy === 'total_profit' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        💰 Total Profit
                    </a>
                    <a href="{{ route('leaderboard') }}?rank_by=roi" 
                       class="px-6 py-3 rounded-lg font-semibold transition-colors {{ $rankBy === 'roi' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        📈 ROI %
                    </a>
                    <a href="{{ route('leaderboard') }}?rank_by=win_rate" 
                       class="px-6 py-3 rounded-lg font-semibold transition-colors {{ $rankBy === 'win_rate' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        🎯 Win Rate
                    </a>
                    <a href="{{ route('leaderboard') }}?rank_by=profit_factor" 
                       class="px-6 py-3 rounded-lg font-semibold transition-colors {{ $rankBy === 'profit_factor' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        ⚡ Profit Factor
                    </a>
                </div>
            </div>

            {{-- Leaderboard Table --}}
            @if($traders->isEmpty())
                <div class="bg-white rounded-xl shadow-card p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Traders Yet</h3>
                    <p class="text-gray-600 mb-6">Be the first to join the leaderboard!</p>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        Set Up Your Public Profile
                    </a>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rank
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Trader
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Profit
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ROI
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Win Rate
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Profit Factor
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Trades
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($traders as $index => $trader)
                                    {{-- Wrapper for both rows to share Alpine state --}}
                                    <template x-data="{ expanded: false }">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        {{-- Rank --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($index === 0)
                                                    <span class="text-2xl">🥇</span>
                                                @elseif($index === 1)
                                                    <span class="text-2xl">🥈</span>
                                                @elseif($index === 2)
                                                    <span class="text-2xl">🥉</span>
                                                @else
                                                    <span class="text-lg font-bold text-gray-600">{{ $index + 1 }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Trader Info --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($trader['account_count'] > 1)
                                                    <button @click="expanded = !expanded" class="mr-2 text-gray-400 hover:text-gray-600">
                                                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-90': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                                        {{ $trader['account_count'] }}
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        @if($trader['user']->public_display_mode === 'username')
                                                            {{ '@' . $trader['user']->public_username }}
                                                        @elseif($trader['user']->public_display_mode === 'anonymous')
                                                            Anonymous Trader
                                                        @else
                                                            {{ $trader['user']->public_display_name }}
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $trader['account_count'] }} {{ $trader['account_count'] === 1 ? 'Account' : 'Accounts' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Profit --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-semibold {{ $trader['stats']['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $trader['stats']['total_profit'] >= 0 ? '+' : '' }}{{ number_format($trader['stats']['total_profit'], 2) }} {{ $trader['accounts'][0]['account']->account_currency }}
                                            </div>
                                        </td>

                                        {{-- ROI --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm {{ $trader['stats']['roi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $trader['stats']['roi'] >= 0 ? '+' : '' }}{{ $trader['stats']['roi'] }}%
                                            </div>
                                        </td>

                                        {{-- Win Rate --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm text-gray-900">
                                                {{ $trader['stats']['win_rate'] }}%
                                            </div>
                                        </td>

                                        {{-- Profit Factor --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm text-gray-900">
                                                {{ $trader['stats']['profit_factor'] }}
                                            </div>
                                        </td>

                                        {{-- Trades --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm text-gray-900">
                                                {{ $trader['stats']['total_trades'] }}
                                            </div>
                                        </td>

                                        {{-- Action --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($trader['account_count'] > 1)
                                                <button @click="expanded = !expanded" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                    <span x-show="!expanded">Show Accounts →</span>
                                                    <span x-show="expanded">Hide Accounts ↑</span>
                                                </button>
                                            @else
                                                <a href="{{ url('/@' . $trader['user']->public_username . '/' . $trader['accounts'][0]['profile']->account_slug . '/' . $trader['accounts'][0]['account']->account_number) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                    View Profile →
                                                </a>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Expandable Accounts Sub-table --}}
                                    @if($trader['account_count'] > 1)
                                        <tr x-show="expanded" 
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 transform scale-95"
                                            x-transition:enter-end="opacity-100 transform scale-100"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 transform scale-100"
                                            x-transition:leave-end="opacity-0 transform scale-95"
                                            class="bg-gray-50">
                                            <td colspan="8" class="px-6 py-4">
                                                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                                    <table class="min-w-full">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Account</th>
                                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">Profit</th>
                                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">ROI</th>
                                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">Win Rate</th>
                                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">Profit Factor</th>
                                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">Trades</th>
                                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-600">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            @foreach($trader['accounts'] as $accountData)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="px-4 py-3">
                                                                        <div class="flex items-center">
                                                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center text-white font-bold text-xs mr-3">
                                                                                {{ substr($accountData['account']->broker_name, 0, 1) }}
                                                                            </div>
                                                                            <div>
                                                                                <div class="text-sm font-medium text-gray-900">
                                                                                    <x-broker-name :broker="$accountData['account']->broker_name" /> #{{ $accountData['account']->account_number }}
                                                                                </div>
                                                                                <div class="text-xs text-gray-500">
                                                                                    <x-platform-badge :account="$accountData['account']" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right">
                                                                        <span class="text-sm font-semibold {{ $accountData['stats']['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                            {{ $accountData['stats']['total_profit'] >= 0 ? '+' : '' }}{{ number_format($accountData['stats']['total_profit'], 2) }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right">
                                                                        <span class="text-sm {{ $accountData['stats']['roi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                            {{ $accountData['stats']['roi'] >= 0 ? '+' : '' }}{{ $accountData['stats']['roi'] }}%
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right text-sm text-gray-900">
                                                                        {{ $accountData['stats']['win_rate'] }}%
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right text-sm text-gray-900">
                                                                        {{ $accountData['stats']['profit_factor'] }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right text-sm text-gray-900">
                                                                        {{ $accountData['stats']['total_trades'] }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right">
                                                                        <a href="{{ url('/@' . $trader['user']->public_username . '/' . $accountData['profile']->account_slug . '/' . $accountData['account']->account_number) }}" 
                                                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">
                                                                            View →
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    </template>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Info Footer --}}
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Want to join the leaderboard?</strong> Enable "Show on Leaderboard" in your 
                                <a href="{{ route('profile.edit') }}" class="underline font-semibold">profile settings</a> 
                                and make at least one trading account public.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

@if(auth()->check())
    </x-app-layout>
@else
    {{-- Footer for guests --}}
    <x-footer />
    </div>
    </body>
    </html>
@endif
