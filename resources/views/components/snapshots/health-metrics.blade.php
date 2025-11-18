@props(['current', 'changes', 'currency'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    
    {{-- Balance Card --}}
    <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card hover:shadow-card-hover transition-all duration-300 p-6">
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm font-medium text-gray-500">Balance</div>
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        @if($current)
            <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ number_format($current->balance, 2) }}
            </div>
            <div class="text-xs text-gray-500 mb-2">{{ $currency }}</div>
            <div class="flex items-center text-sm {{ $changes['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                @if($changes['balance'] >= 0)
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @endif
                <span class="font-semibold">{{ number_format(abs($changes['balance']), 2) }}%</span>
                <span class="ml-1 text-gray-500">24h</span>
            </div>
        @else
            <div class="text-gray-400">No data available</div>
        @endif
    </div>

    {{-- Equity Card --}}
    <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card hover:shadow-card-hover transition-all duration-300 p-6">
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm font-medium text-gray-500">Equity</div>
            <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
        @if($current)
            <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ number_format($current->equity, 2) }}
            </div>
            <div class="text-xs text-gray-500 mb-2">
                {{ $currency }} 
                @if($current->balance > 0)
                    ({{ number_format(($current->equity / $current->balance) * 100, 1) }}% of balance)
                @endif
            </div>
            <div class="flex items-center text-sm {{ $changes['equity'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                @if($changes['equity'] >= 0)
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @endif
                <span class="font-semibold">{{ number_format(abs($changes['equity']), 2) }}%</span>
                <span class="ml-1 text-gray-500">24h</span>
            </div>
        @else
            <div class="text-gray-400">No data available</div>
        @endif
    </div>

    {{-- Margin Level Card --}}
    <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card hover:shadow-card-hover transition-all duration-300 p-6">
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm font-medium text-gray-500">Margin Level</div>
            <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
        @if($current && $current->margin_level)
            <div class="text-2xl font-bold text-gray-900 mb-1">
                {{ number_format($current->margin_level, 2) }}%
            </div>
            <div class="text-xs mb-2 {{ $current->margin_level < 200 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                @if($current->margin_level < 100)
                    ⚠️ Critical Risk
                @elseif($current->margin_level < 200)
                    ⚠️ High Risk
                @elseif($current->margin_level < 500)
                    ⚡ Moderate
                @else
                    ✅ Healthy
                @endif
            </div>
            <div class="flex items-center text-sm {{ $changes['margin_level'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                @if($changes['margin_level'] >= 0)
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @endif
                <span class="font-semibold">{{ number_format(abs($changes['margin_level']), 2) }}%</span>
                <span class="ml-1 text-gray-500">24h</span>
            </div>
        @else
            <div class="text-gray-400">No margin used</div>
        @endif
    </div>

    {{-- Unrealized P/L Card --}}
    <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card hover:shadow-card-hover transition-all duration-300 p-6">
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm font-medium text-gray-500">Unrealized P/L</div>
            <div class="p-2 {{ $current && $current->profit >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg">
                <svg class="w-5 h-5 {{ $current && $current->profit >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        @if($current)
            <div class="text-2xl font-bold {{ $current->profit >= 0 ? 'text-green-600' : 'text-red-600' }} mb-1">
                {{ $current->profit >= 0 ? '+' : '' }}{{ number_format($current->profit, 2) }}
            </div>
            <div class="text-xs text-gray-500 mb-2">{{ $currency }}</div>
            <div class="flex items-center text-sm text-gray-600">
                <span class="font-semibold">{{ $changes['profit'] >= 0 ? '+' : '' }}{{ number_format($changes['profit'], 2) }}</span>
                <span class="ml-1">{{ $currency }} 24h</span>
            </div>
        @else
            <div class="text-gray-400">No data available</div>
        @endif
    </div>

</div>
