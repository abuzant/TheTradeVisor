<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin - All Trades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info Notice -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>📊 IN</strong> = Position opening (profit is $0.00) • 
                            <strong>✅ OUT</strong> = Position closing (actual profit shown) • 
                            <span class="px-2 py-1 bg-blue-100 rounded">Blue rows</span> = Open positions (IN entries)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Trades</div>
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Today</div>
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['today']) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">This Week</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ number_format($stats['week']) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Active Users</div>
                    <div class="text-3xl font-bold text-green-600">{{ number_format($stats['active_users']) }}</div>
                </div>
            </div>

            <!-- Totals Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Filtered Results Summary <span class="text-sm text-gray-500 font-normal">(converted to USD)</span></h3>
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                        <div>
                            <div class="text-xs text-gray-600">Trades</div>
                            <div class="text-lg font-bold">{{ number_format($totals['tradeCount']) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600">Total Volume</div>
                            <div class="text-lg font-bold">{{ number_format($totals['totalVolume'], 2) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600">Total Profit</div>
                            <div class="text-lg font-bold {{ $totals['totalProfit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                USD ${{ number_format($totals['totalProfit'], 2) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600">Commission</div>
                            <div class="text-lg font-bold text-gray-700">USD ${{ number_format($totals['totalCommission'], 2) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600">Fees</div>
                            <div class="text-lg font-bold text-gray-700">USD ${{ number_format($totals['totalFees'], 2) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600">Swap</div>
                            <div class="text-lg font-bold {{ $totals['totalSwap'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                USD ${{ number_format($totals['totalSwap'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.trades.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <input type="text" name="search" placeholder="Search symbol, ticket, user..."
                                   value="{{ $search }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                            <select name="user_id" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="symbol" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Symbols</option>
                                @foreach($symbols as $sym)
                                    <option value="{{ $sym }}" {{ $symbol == $sym ? 'selected' : '' }}>
                                        {{ $sym }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="type" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all" {{ $type == 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="buy" {{ $type == 'buy' ? 'selected' : '' }}>Buy</option>
                                <option value="sell" {{ $type == 'sell' ? 'selected' : '' }}>Sell</option>
                                <option value="trades" {{ $type == 'trades' ? 'selected' : '' }}>Trades Only</option>
                                <option value="cashier" {{ $type == 'cashier' ? 'selected' : '' }}>Deposits/Withdrawals</option>
                                <option value="fees" {{ $type == 'fees' ? 'selected' : '' }}>Fees</option>
                                <option value="swaps" {{ $type == 'swaps' ? 'selected' : '' }}>Swaps</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input type="date" name="date_from" value="{{ $dateFrom }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Date From">

                            <input type="date" name="date_to" value="{{ $dateTo }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Date To">

                            <select name="per_page" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per page</option>
                                <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200 per page</option>
                            </select>

                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Filter
                                </button>
                                <a href="{{ route('admin.trades.index') }}" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Trades Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ticket
                                    </th>

                                    <x-sortable-header
                                        column="time"
                                        label="Time"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Account
                                    </th>

                                    <x-sortable-header
                                        column="symbol"
                                        label="Symbol"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <x-sortable-header
                                        column="type"
                                        label="Type"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <x-sortable-header
                                        column="volume"
                                        label="Volume"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <x-sortable-header
                                        column="price"
                                        label="Price"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <x-sortable-header
                                        column="profit"
                                        label="Profit"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($deals as $deal)
                                    <tr class="hover:bg-gray-50 {{ $deal->entry === 'in' ? 'bg-blue-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $deal->ticket }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($deal->time)
                                                <span title="{{ $deal->time->format('Y-m-d H:i:s') }}">
                                                    {{ $deal->time->format('M d, H:i') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($deal->tradingAccount && $deal->tradingAccount->user)
                                                <a href="{{ route('admin.users.show', $deal->tradingAccount->user_id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                    {{ $deal->tradingAccount->user->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($deal->tradingAccount)
                                                <span class="text-indigo-600 hover:text-indigo-900" title="{{ $deal->tradingAccount->broker_name }}">
                                                    {{ explode(' ', $deal->tradingAccount->broker_name)[0] }}
                                                </span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.trades.index', ['symbol' => $deal->normalized_symbol]) }}"
                                               title="Raw: {{ $deal->symbol }}"
                                               class="text-indigo-600 hover:text-indigo-900">
                                                {{ $deal->normalized_symbol }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ str_contains(strtolower($deal->type), 'buy') ? 'bg-green-100 text-green-800' :
                                                   (str_contains(strtolower($deal->type), 'sell') ? 'bg-red-100 text-red-800' :
                                                   'bg-gray-100 text-gray-800') }}">
                                                {{ strtoupper($deal->type) }}
                                            </span>
                                            @if($deal->entry === 'in')
                                                <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800" title="Position Opening">
                                                    📊 IN
                                                </span>
                                            @elseif($deal->entry === 'out')
                                                <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800" title="Position Closing">
                                                    ✅ OUT
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($deal->volume, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $deal->formatted_price }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @php
                                                // For open positions (entry='in'), show floating profit from Position
                                                $displayProfit = $deal->profit;
                                                if ($deal->entry === 'in' && isset($deal->openPosition) && $deal->openPosition) {
                                                    $displayProfit = $deal->openPosition->profit;
                                                }
                                            @endphp
                                            <span class="{{ $displayProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                @if($deal->tradingAccount)
                                                    {{ $deal->tradingAccount->account_currency }} {{ number_format($displayProfit, 2) }}
                                                @else
                                                    ${{ number_format($displayProfit, 2) }}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
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
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($deals->hasPages())
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            {{ $deals->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
