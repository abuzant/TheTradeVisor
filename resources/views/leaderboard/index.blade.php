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
        
        {{-- Simple Navigation --}}
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                            <img src="{{ asset('images/logo.svg') }}" alt="TheTradeVisor" class="h-8 w-8">
                            <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">TheTradeVisor</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Sign Up</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

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
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                                        {{ substr($trader['account']->broker_name, 0, 1) }}
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
                                                        <x-broker-name :broker="$trader['account']->broker_name" />
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Profit --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-semibold {{ $trader['stats']['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $trader['stats']['total_profit'] >= 0 ? '+' : '' }}{{ number_format($trader['stats']['total_profit'], 2) }} {{ $trader['account']->account_currency }}
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
                                            <a href="{{ url('/@' . $trader['user']->public_username . '/' . $trader['profile']->account_slug . '/' . $trader['account']->account_number) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                View Profile →
                                            </a>
                                        </td>
                                    </tr>
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
    </div>
</body>
</html>
