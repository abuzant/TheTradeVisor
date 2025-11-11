@props(['position', 'account'])

<tr x-data="{ expanded: false }" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
    {{-- Main Position Row --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm">
        @if($position->deal_count > 1)
            <button @click="expanded = !expanded" class="text-indigo-600 hover:text-indigo-900 focus:outline-none">
                <svg x-show="!expanded" class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="expanded" class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        @endif
        {{ $position->open_time->format('M d, H:i') }}
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
        <div class="flex items-center space-x-2">
            <a href="/trades/symbol/{{ $position->normalized_symbol }}" class="text-indigo-600 hover:text-indigo-900">
                {{ $position->normalized_symbol }}
            </a>
            @if($position->platform_type)
                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $position->platform_type === 'MT5' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $position->platform_type }}
                    @if($position->isNettingPosition())
                        <span class="text-purple-600">●</span>
                    @endif
                </span>
            @endif
        </div>
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $position->type == 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ strtoupper($position->type) }}
        </span>
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ rtrim(rtrim(number_format($position->volume, 2), '0'), '.') }}
        @if($position->deal_count > 1)
            <span class="text-xs text-gray-400" title="Total volume in/out">
                ({{ rtrim(rtrim(number_format($position->total_volume_in, 2), '0'), '.') }}/{{ rtrim(rtrim(number_format($position->total_volume_out, 2), '0'), '.') }})
            </span>
        @endif
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ rtrim(rtrim(number_format($position->open_price, 5), '0'), '.') }}
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        {{ $position->is_open ? rtrim(rtrim(number_format($position->current_price, 5), '0'), '.') : rtrim(rtrim(number_format($position->close_price ?? $position->current_price, 5), '0'), '.') }}
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $position->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
        {{ $account->account_currency }} {{ number_format($position->profit, 2) }}
    </td>
    
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        @if($position->is_open)
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">OPEN</span>
        @else
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">CLOSED</span>
        @endif
        @if($position->deal_count > 1)
            <span class="ml-1 text-xs text-gray-400">({{ $position->deal_count }} deals)</span>
        @endif
    </td>
</tr>

{{-- Expandable Deals Section --}}
@if($position->deal_count > 1)
<tr x-show="expanded" x-collapse class="bg-gray-50">
    <td colspan="8" class="px-6 py-4">
        <div class="pl-8">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">
                📊 Deal History ({{ $position->deal_count }} deals)
                @if($position->isNettingPosition())
                    <span class="text-xs text-purple-600 font-normal">MT5 Netting - Position ID: {{ $position->position_identifier }}</span>
                @endif
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ticket</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Entry</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($position->deals as $deal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600">{{ $deal->time->format('M d, H:i:s') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600">#{{ $deal->ticket }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ strtolower($deal->type) == 'buy' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ strtoupper($deal->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded {{ strtolower($deal->entry) == 'in' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ strtoupper($deal->entry) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600">{{ rtrim(rtrim(number_format($deal->volume, 2), '0'), '.') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600">{{ rtrim(rtrim(number_format($deal->price, 5), '0'), '.') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap font-medium {{ $deal->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $account->account_currency }} {{ number_format($deal->profit, 2) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-500 text-xs">{{ strtoupper($deal->reason) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </td>
</tr>
@endif
