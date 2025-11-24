<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Meta Tags --}}
    <title>{{ $user->public_display_mode === 'anonymous' ? 'Anonymous Trader' : '@' . $user->public_username }} - Trading Performance | TheTradeVisor</title>
    <meta name="description" content="View {{ $user->public_display_mode === 'anonymous' ? 'anonymous trader' : '@' . $user->public_username }}'s trading performance: {{ $stats['win_rate'] }}% win rate, {{ $stats['total_trades'] }} trades, {{ number_format($stats['total_profit'], 2) }} {{ $stats['currency'] }} profit.">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $user->public_display_mode === 'anonymous' ? 'Anonymous Trader' : '@' . $user->public_username }} - {{ $stats['win_rate'] }}% Win Rate tracked by TheTradeVisor">
    <meta property="og:description" content="{{ number_format($stats['total_profit'], 2) }} {{ $stats['currency'] }} profit • {{ $stats['total_trades'] }} trades • {{ $stats['win_rate'] }}% win rateaccount tracking by TheTradeVisor">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ url()->current() }}">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $user->public_display_mode === 'anonymous' ? 'Anonymous Trader' : '@' . $user->public_username }}'s Trading Performance from TheTradeVisor">
    <meta name="twitter:description" content="{{ $stats['win_rate'] }}% win rate • {{ number_format($stats['total_profit'], 2) }} {{ $stats['currency'] }} profit • {{ $stats['total_trades'] }} trades on TheTradeVisor">
    
    {{-- Google Analytics Custom Dimensions --}}
    <meta name="profile-widget-preset" content="{{ $profile->widget_preset }}">
    <meta name="profile-platform-type" content="{{ $account->platform_type }}">
    <meta name="profile-display-mode" content="{{ $user->public_display_mode }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="bg-gray-50" data-widget-preset="{{ $profile->widget_preset }}" data-platform="{{ $account->platform_type }}" data-display-mode="{{ $user->public_display_mode }}">
    
    {{-- Header --}}
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                        {{ substr($account->broker_name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            @if($user->public_display_mode === 'username')
                                {{ '@' . $user->public_username }}
                            @elseif($user->public_display_mode === 'anonymous')
                                Anonymous Trader
                            @else
                                {{ $user->public_display_name }}
                            @endif
                        </h1>
                        @if($profile->custom_title)
                            <p class="text-lg text-gray-600">{{ $profile->custom_title }}</p>
                        @endif
                        <p class="text-sm text-gray-500">
                            <a href="{{ route('broker-details', ['broker' => urlencode($account->broker_name)]) }}" 
                               class="hover:underline" 
                               title="{{ $account->broker_name }}">{{ $account->broker_name }}</a> • 
                            <x-platform-badge :account="$account" /> •
                            Trading since {{ $milestones['first_trade_date'] ? $milestones['first_trade_date']->format('M Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-col items-end space-y-3">
                    <a href="{{ route('landing') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Track Your Performance Free
                    </a>
                    
                    {{-- Social Share Buttons --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500 mr-1">Share:</span>
                        
                        {{-- Twitter --}}
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode('Check out ' . ($user->public_display_mode === 'anonymous' ? 'this trader' : '@' . $user->public_username) . '\'s trading performance on TheTradeVisor') }}" 
                           target="_blank" 
                           class="p-2 rounded-full bg-gray-100 hover:bg-blue-100 transition"
                           title="Share on Twitter">
                            <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        
                        {{-- Facebook --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                           target="_blank" 
                           class="p-2 rounded-full bg-gray-100 hover:bg-blue-100 transition"
                           title="Share on Facebook">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        
                        {{-- LinkedIn --}}
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
                           target="_blank" 
                           class="p-2 rounded-full bg-gray-100 hover:bg-blue-100 transition"
                           title="Share on LinkedIn">
                            <svg class="w-4 h-4 text-blue-700" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        
                        {{-- WhatsApp --}}
                        <a href="https://wa.me/?text={{ urlencode('Check out ' . ($user->public_display_mode === 'anonymous' ? 'this trader' : '@' . $user->public_username) . '\'s trading performance: ' . url()->current()) }}" 
                           target="_blank" 
                           class="p-2 rounded-full bg-gray-100 hover:bg-green-100 transition"
                           title="Share on WhatsApp">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        </a>
                        
                        {{-- Copy Link --}}
                        <button onclick="navigator.clipboard.writeText('{{ url()->current() }}'); this.querySelector('span').textContent = 'Copied!'; setTimeout(() => this.querySelector('span').textContent = 'Copy', 2000)" 
                                class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition group relative"
                                title="Copy link">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Copy</span>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Badges --}}
            @if(count($badges) > 0)
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($badges as $badge)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $badge->badge_color }}-100 text-{{ $badge->badge_color }}-800">
                            {!! $badge->badge_icon !!} {{ $badge->badge_name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </header>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        {{-- Performance Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            {{-- Total Trades --}}
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-500">Total Trades</div>
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_trades']) }}</div>
                <div class="text-sm text-gray-600 mt-1">{{ $stats['winning_trades'] }}W / {{ $stats['losing_trades'] }}L</div>
            </div>
            
            {{-- Win Rate --}}
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-500">Win Rate</div>
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-green-600">{{ $stats['win_rate'] }}%</div>
                <div class="text-sm text-gray-600 mt-1">Last 30 days</div>
            </div>
            
            {{-- Total Profit --}}
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-500">Total Profit</div>
                    <svg class="w-8 h-8 {{ $stats['total_profit'] >= 0 ? 'text-green-400' : 'text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold {{ $stats['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($stats['total_profit'], 2) }} {{ $stats['currency'] }}
                </div>
                <div class="text-sm text-gray-600 mt-1">Last 30 days</div>
            </div>
            
            {{-- ROI --}}
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-500">ROI</div>
                    <svg class="w-8 h-8 {{ $stats['roi'] >= 0 ? 'text-green-400' : 'text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold {{ $stats['roi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $stats['roi'] >= 0 ? '+' : '' }}{{ number_format($stats['roi'], 2) }}%
                </div>
                <div class="text-sm text-gray-600 mt-1">Return on investment</div>
            </div>
            
            {{-- Monthly Change --}}
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-500">Monthly Change</div>
                    <svg class="w-8 h-8 {{ $stats['monthly_change'] >= 0 ? 'text-green-400' : 'text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold {{ $stats['monthly_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $stats['monthly_change'] >= 0 ? '+' : '' }}{{ number_format($stats['monthly_change'], 2) }}%
                </div>
                <div class="text-sm text-gray-600 mt-1">This month</div>
            </div>
            
            {{-- Profit Factor --}}
            <div class="bg-white rounded-xl shadow-card p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-500">Profit Factor</div>
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-indigo-600">{{ $stats['profit_factor'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Gross profit / loss</div>
            </div>
        </div>

        {{-- Equity Curve (All Presets) --}}
        @if(count($equity_curve) > 0)
            <div class="bg-white rounded-xl shadow-card p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Equity Curve (30 Days)</h2>
                <canvas id="equityChart" height="80"></canvas>
            </div>
        @endif

        {{-- Symbol Performance (Full Stats & Trader Showcase) --}}
        @if(in_array($profile->widget_preset, ['full_stats', 'trader_showcase']) && count($symbol_performance) > 0)
            <div class="bg-white rounded-xl shadow-card p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Top Symbols</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trades</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Win Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($symbol_performance as $symbol)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" style="min-width: 150px; width: 150px;">
                                        <span class="cursor-pointer inline-block" style="min-width: 120px;"
                                              onmouseover="this.innerHTML='{{ $symbol['symbol'] }}'" 
                                              onmouseout="this.innerHTML='{{ $symbol['normalized_symbol'] ?? $symbol['symbol'] }}'">
                                            {{ $symbol['normalized_symbol'] ?? $symbol['symbol'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $symbol['trades'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $symbol['win_rate'] }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $symbol['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $symbol['profit'] >= 0 ? '+' : '' }}{{ number_format($symbol['profit'], 2) }} {{ $stats['currency'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Recent Trades Timeline (Trader Showcase Only) --}}
        @if($profile->widget_preset === 'trader_showcase' && isset($recent_trades) && count($recent_trades) > 0)
            <div class="bg-white rounded-xl shadow-card p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Trades (Last 10)</h2>
                <div class="space-y-4">
                    @foreach($recent_trades as $trade)
                        <div class="border-l-4 {{ $trade['profit'] >= 0 ? 'border-green-500' : 'border-red-500' }} pl-4 px-2 py-2 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <span class="font-semibold text-gray-900">{{ $trade['symbol'] }}</span>
                                        <span class="px-2 py-1 text-xs rounded {{ $trade['type'] === 'buy' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ strtoupper($trade['type']) }}
                                        </span>
                                        <span class="text-sm text-gray-600">{{ $trade['volume'] }} lots</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($trade['open_time'])->format('M d, Y H:i') }} → 
                                        {{ \Carbon\Carbon::parse($trade['close_time'])->format('M d, Y H:i') }}
                                        <span class="ml-2">
                                            ({{ \Carbon\Carbon::parse($trade['open_time'])->diffForHumans(\Carbon\Carbon::parse($trade['close_time']), true) }})
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold {{ $trade['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $trade['profit'] >= 0 ? '+' : '' }}{{ number_format($trade['profit'], 2) }} {{ $stats['currency'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Risk Disclaimer --}}
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-amber-900 mb-2">Risk Disclaimer</h3>
                    <div class="text-sm text-amber-800 space-y-2">
                        <p>
                            <strong>Trading involves substantial risk of loss.</strong><br />
                            The performance data displayed on this page is for informational purposes only and does not constitute financial advice, investment advice, trading advice, or any other type of advice. 
                            Past performance is not indicative of future results. The trading results shown may not be typical and individual results will vary. You should not invest money that you cannot afford to lose.
                            All trading involves risk. Leveraged trading has large potential rewards, but also large potential risk. You must be aware of the risks and be willing to accept them in order to invest in the markets. Don't trade with money you can't afford to lose.
                        </p>
                        <p class="text-xs text-amber-700 mt-3">
                            TheTradeVisor is a performance tracking platform and does not provide trading signals, investment recommendations, or broker services.<br />
                            All trading decisions are made solely by the account holder.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA Section (only for guests) --}}
        @guest
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-card p-8 text-center text-white">
                <h2 class="text-2xl font-bold mb-2">Track Your Trading Performance</h2>
                <p class="text-indigo-100 mb-6">Join thousands of traders using TheTradeVisor to analyze their performance</p>
                <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Get Started Free
                </a>
            </div>
        @endguest
    </main>

    {{-- Unified Footer --}}
    <x-footer />

    <script>
        // Equity Curve Chart
        @if(count($equity_curve) > 0)
        const ctx = document.getElementById('equityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($equity_curve, 'date')) !!},
                datasets: [{
                    label: 'Equity',
                    data: {!! json_encode(array_column($equity_curve, 'equity')) !!},
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
        @endif

        // Google Analytics - Track widget preset view
        if (typeof gtag !== 'undefined') {
            gtag('event', 'public_profile_view', {
                'widget_preset': '{{ $profile->widget_preset }}',
                'platform_type': '{{ $account->platform_type }}',
                'display_mode': '{{ $user->public_display_mode }}',
                'page_path': window.location.pathname
            });
        }

        // Alternative: Google Analytics 4 - Set user properties
        if (typeof gtag !== 'undefined') {
            gtag('set', 'user_properties', {
                'profile_widget_preset': '{{ $profile->widget_preset }}',
                'profile_platform': '{{ $account->platform_type }}'
            });
        }
    </script>
</body>
</html>
