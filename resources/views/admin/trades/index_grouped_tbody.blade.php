<tbody class="bg-white divide-y divide-gray-200">
    @forelse($groupedDeals as $group)
        @php
            // FIXED: Use IN deal (position type) for display, not OUT deal (closing action)
            $displayDeal = $group['in_deal'] ?? $group['out_deal'];
            $isOpen = $group['is_open'];
            $hasBoth = $group['out_deal'] && $group['in_deal'];
        @endphp
        
        {{-- Main Row (shows IN deal - position type, not closing action) --}}
        <tr class="hover:bg-gray-50 {{ $isOpen ? 'bg-blue-50' : '' }} {{ $hasBoth ? 'cursor-pointer' : '' }}" 
            @if($hasBoth) onclick="toggleDetails('position-{{ $group['position_id'] }}')" @endif>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                @if($hasBoth)
                    <span class="text-indigo-600 font-medium" title="Click to expand/collapse">
                        <span id="arrow-position-{{ $group['position_id'] }}">▶</span> {{ $displayDeal->ticket }}
                    </span>
                @else
                    {{ $displayDeal->ticket }}
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                @if($displayDeal->time)
                    <span title="{{ $displayDeal->time->format('Y-m-d H:i:s') }}">
                        {{ $displayDeal->time->format('M d, H:i') }}
                    </span>
                @else
                    <span class="text-gray-400">N/A</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                @if($displayDeal->tradingAccount && $displayDeal->tradingAccount->user)
                    <a href="{{ route('admin.users.show', $displayDeal->tradingAccount->user_id) }}"
                       class="text-indigo-600 hover:text-indigo-900 font-medium">
                        {{ $displayDeal->tradingAccount->user->name }}
                    </a>
                @else
                    <span class="text-gray-400">N/A</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                @if($displayDeal->tradingAccount)
                    <span class="text-indigo-600 hover:text-indigo-900" title="{{ $displayDeal->tradingAccount->broker_name }}">
                        {{ explode(' ', $displayDeal->tradingAccount->broker_name)[0] }}
                    </span>
                @else
                    N/A
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <a href="{{ route('admin.trades.index', ['symbol' => $displayDeal->normalized_symbol]) }}"
                   title="Raw: {{ $displayDeal->symbol }}"
                   class="text-indigo-600 hover:text-indigo-900">
                    {{ $displayDeal->normalized_symbol }}
                </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    {{ str_contains(strtolower($displayDeal->type), 'buy') ? 'bg-green-100 text-green-800' :
                       (str_contains(strtolower($displayDeal->type), 'sell') ? 'bg-red-100 text-red-800' :
                       'bg-gray-100 text-gray-800') }}">
                    {{ strtoupper($displayDeal->type) }}
                </span>
                @if($isOpen)
                    <span class="ml-1 text-blue-600 font-bold" title="Open Position">📊</span>
                @else
                    <span class="ml-1 text-gray-600 font-bold" title="Closed Position">✅</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ number_format($displayDeal->volume, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $displayDeal->formatted_price }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <span class="{{ $group['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    @if($displayDeal->tradingAccount)
                        {{ $displayDeal->tradingAccount->account_currency }} {{ number_format($group['total_profit'], 2) }}
                    @else
                        ${{ number_format($group['total_profit'], 2) }}
                    @endif
                </span>
            </td>
        </tr>
        
        {{-- Detail Row (hidden by default, shows OUT deal - closing details) --}}
        @if($hasBoth)
            <tr id="details-position-{{ $group['position_id'] }}" class="hidden bg-gray-50">
                <td colspan="9" class="px-6 py-4">
                    <div class="ml-8 border-l-2 border-red-200 pl-4">
                        <div class="text-xs font-semibold text-gray-600 mb-2">Closing Trade Details:</div>
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-2 py-1 text-left text-gray-600">Ticket</th>
                                    <th class="px-2 py-1 text-left text-gray-600">Time</th>
                                    <th class="px-2 py-1 text-left text-gray-600">Action</th>
                                    <th class="px-2 py-1 text-right text-gray-600">Volume</th>
                                    <th class="px-2 py-1 text-right text-gray-600">Price</th>
                                    <th class="px-2 py-1 text-right text-gray-600">Profit</th>
                                    <th class="px-2 py-1 text-right text-gray-600">Commission</th>
                                    <th class="px-2 py-1 text-right text-gray-600">Swap</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-2 py-1 text-left">{{ $group['out_deal']->ticket }}</td>
                                    <td class="px-2 py-1 text-left">
                                        @if($group['out_deal']->time)
                                            {{ $group['out_deal']->time->format('M d, H:i') }}
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1 text-left">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ strtoupper($group['out_deal']->type) }} (Close)
                                        </span>
                                    </td>
                                    <td class="px-2 py-1 text-center">{{ number_format($group['out_deal']->volume, 2) }}</td>
                                    <td class="px-2 py-1 text-center">{{ $group['out_deal']->formatted_price }}</td>
                                    <td class="px-2 py-1 text-center">
                                        <span class="{{ $group['out_deal']->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($group['out_deal']->profit, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-1 text-center">{{ number_format($group['out_deal']->commission ?? 0, 2) }}</td>
                                    <td class="px-2 py-1 text-center">{{ number_format($group['out_deal']->swap ?? 0, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        @endif
    @empty
        <tr>
            <td colspan="9" class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">No trades found matching your filters.</p>
            </td>
        </tr>
    @endforelse
</tbody>

<script>
function toggleDetails(positionId) {
    const detailsRow = document.getElementById('details-' + positionId);
    const arrow = document.getElementById('arrow-' + positionId);
    
    if (detailsRow.classList.contains('hidden')) {
        detailsRow.classList.remove('hidden');
        arrow.textContent = '▼';
    } else {
        detailsRow.classList.add('hidden');
        arrow.textContent = '▶';
    }
}
</script>
