<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $broker->company_name }} - All Accounts
            </h2>
            <a href="{{ route('admin.brokers.show', $broker->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Broker
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.brokers.accounts', $broker->id) }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            {{-- Status Filter --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="all">All Accounts</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Last 30 days)</option>
                                    <option value="dormant" {{ request('status') === 'dormant' ? 'selected' : '' }}>Dormant (30+ days)</option>
                                </select>
                            </div>

                            {{-- Submit --}}
                            <div class="flex items-end space-x-2 md:col-span-2">
                                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Filter
                                </button>
                                <a href="{{ route('admin.brokers.accounts', $broker->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Accounts Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Accounts ({{ $accounts->total() }})
                    </h3>

                    @if($accounts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Seen</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Seen</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($accounts as $usage)
                                        @if($usage->tradingAccount)
                                            @php
                                                $account = $usage->tradingAccount;
                                                $isActive = $usage->last_seen_at && $usage->last_seen_at->greaterThan(now()->subDays(30));
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $usage->account_number }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $usage->user->name ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $usage->user->email ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $account->platform_type }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if($account->country_name)
                                                        <div class="flex items-center">
                                                            <span class="mr-2">{{ strtoupper($account->country_code) }}</span>
                                                            <span class="text-gray-500">{{ $account->country_name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400">Unknown</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ number_format($account->balance, 2) }} {{ $account->account_currency }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ number_format($account->equity, 2) }} {{ $account->account_currency }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="{{ $account->profit >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                        {{ $account->profit >= 0 ? '+' : '' }}{{ number_format($account->profit, 2) }} {{ $account->account_currency }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $usage->first_seen_at ? $usage->first_seen_at->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $usage->last_seen_at ? $usage->last_seen_at->diffForHumans() : 'Never' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($isActive)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Dormant
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $accounts->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No accounts found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request('status'))
                                    Try adjusting your filters.
                                @else
                                    No accounts have connected to this broker yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Summary Stats --}}
            @if($accounts->total() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary Statistics</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-sm font-medium text-blue-600 mb-1">Total Accounts</div>
                                <div class="text-2xl font-bold text-blue-900">{{ $accounts->total() }}</div>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm font-medium text-green-600 mb-1">Active (30d)</div>
                                <div class="text-2xl font-bold text-green-900">
                                    {{ $accounts->filter(function($usage) {
                                        return $usage->last_seen_at && $usage->last_seen_at->greaterThan(now()->subDays(30));
                                    })->count() }}
                                </div>
                            </div>

                            <div class="bg-yellow-50 rounded-lg p-4">
                                <div class="text-sm font-medium text-yellow-600 mb-1">Dormant</div>
                                <div class="text-2xl font-bold text-yellow-900">
                                    {{ $accounts->filter(function($usage) {
                                        return !$usage->last_seen_at || $usage->last_seen_at->lessThan(now()->subDays(30));
                                    })->count() }}
                                </div>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="text-sm font-medium text-purple-600 mb-1">Unique Users</div>
                                <div class="text-2xl font-bold text-purple-900">
                                    {{ $accounts->pluck('user_id')->unique()->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
