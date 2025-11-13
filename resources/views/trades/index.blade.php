<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Trades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Date Range Filter -->
            <x-date-range-filter>
                <div class="flex gap-2">
                    <input type="text" name="search" placeholder="Search symbol..."
                           value="{{ request('search') }}"
                           class="rounded-md border-gray-300 shadow-sm">

                    <select name="type" class="rounded-md border-gray-300 shadow-sm">
                        <option value="">All Types</option>
                        <option value="buy" {{ request('type') == 'buy' ? 'selected' : '' }}>Buy</option>
                        <option value="sell" {{ request('type') == 'sell' ? 'selected' : '' }}>Sell</option>
                    </select>
                </div>
            </x-date-range-filter>

            <!-- Export Buttons -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Export Data</h3>
                    <div class="flex gap-3">
                        <a href="{{ route('export.trades.csv', request()->all()) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export CSV
                        </a>
                        <a href="{{ route('export.trades.pdf', request()->all()) }}"
                           class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Trades Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <x-sortable-header column="time" label="Time" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                    <x-sortable-header column="symbol" label="Symbol" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <x-sortable-header column="type" label="Type" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <x-sortable-header column="volume" label="Volume" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <x-sortable-header column="price" label="Price" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                    <x-sortable-header column="profit" label="Profit" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($deals as $deal)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $deal->time ? $deal->time->format('Y-m-d H:i') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $deal->tradingAccount->broker_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('trades.symbol', $deal->normalized_symbol) }}"
                                               class="text-indigo-600 hover:text-indigo-900"
                                               title="Raw: {{ $deal->symbol }}">
                                                {{ $deal->normalized_symbol }}
                                            </a>
                                            @if($deal->symbol !== $deal->normalized_symbol)
                                                <span class="text-xs text-gray-400 ml-1">({{ $deal->symbol }})</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ str_contains(strtolower($deal->type), 'buy') ? 'bg-green-100 text-green-800' :
                                                   (str_contains(strtolower($deal->type), 'sell') ? 'bg-red-100 text-red-800' :
                                                   'bg-gray-100 text-gray-800') }}">
                                                {{ strtoupper($deal->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ number_format($deal->volume, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $deal->formatted_price }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $deal->profit_usd >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            USD {{ number_format($deal->profit_usd, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No trades found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $deals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
