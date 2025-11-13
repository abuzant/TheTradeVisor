<tbody class="bg-white divide-y divide-gray-200">
    @forelse($groupedDeals as $group)
        @php
            // Use OUT deal for display if exists, otherwise IN deal
            $displayDeal = $group['out_deal'] ?? $group['in_deal'];
            $isOpen = $group['is_open'];
            $hasBoth = $group['out_deal'] && $group['in_deal'];
        @endphp
        
        {{-- Main Row (shows OUT deal or IN if no OUT) --}}
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
                    <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800" title="Open Position">
                        📊 OPEN
                    </span>
                @else
                    <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800" title="Closed Position">
                        ✅ CLOSED
                    </span>
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
        
        {{-- Detail Row (hidden by default, shows IN deal if OUT exists) --}}
        @if($hasBoth)
            <tr id="details-position-{{ $group['position_id'] }}" class="hidden bg-gray-50">
                <td colspan="9" class="px-6 py-4">
                    <div class="ml-8 border-l-2 border-indigo-200 pl-4">
                        <div class="text-xs font-semibold text-gray-600 mb-2">Position Opening (IN):</div>
                        <div class="grid grid-cols-8 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Ticket:</span>
                                <span class="font-medium">{{ $group['in_deal']->ticket }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Time:</span>
                                <span class="font-medium">{{ $group['in_deal']->time->format('M d, H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ str_contains(strtolower($group['in_deal']->type), 'buy') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ strtoupper($group['in_deal']->type) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Volume:</span>
                                <span class="font-medium">{{ number_format($group['in_deal']->volume, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Price:</span>
                                <span class="font-medium">{{ $group['in_deal']->formatted_price }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Commission:</span>
                                <span class="font-medium">{{ number_format($group['in_deal']->commission ?? 0, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Swap:</span>
                                <span class="font-medium">{{ number_format($group['in_deal']->swap ?? 0, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Fee:</span>
                                <span class="font-medium">{{ number_format($group['in_deal']->fee ?? 0, 2) }}</span>
                            </div>
                        </div>
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
