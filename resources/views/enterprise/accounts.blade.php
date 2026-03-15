@section('title', 'Enterprise Accounts - TheTradeVisor')

<x-enterprise-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Accounts Overview') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">All trading accounts using {{ $broker->official_broker_name }}</p>
            </div>
            <a href="{{ route('enterprise.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Filters</h3>
                <form method="GET" action="{{ route('enterprise.accounts') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Platform Filter --}}
                    <div>
                        <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                        <select name="platform" id="platform" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all" {{ $platform === 'all' ? 'selected' : '' }}>All Platforms</option>
                            @foreach($platforms as $p)
                                <option value="{{ strtolower($p) }}" {{ $platform === strtolower($p) ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Country Filter --}}
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <select name="country" id="country" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all" {{ $country === 'all' ? 'selected' : '' }}>All Countries</option>
                            @foreach($countries as $c)
                                <option value="{{ strtolower($c->country_code) }}" {{ $country === strtolower($c->country_code) ? 'selected' : '' }}>
                                    {{ $c->country_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Accounts</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active (Last 30 days)</option>
                            <option value="dormant" {{ $status === 'dormant' ? 'selected' : '' }}>Dormant (30+ days)</option>
                        </select>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-indigo-700 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            {{-- Accounts Table --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">
                            📊 Accounts ({{ $accounts->total() }})
                        </h3>
                    </div>
                </div>

                @if($accounts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Seen</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($accounts as $account)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $account->account_number }}</div>
                                            <div class="text-xs text-gray-500">{{ $account->account_name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $account->platform_type === 'MT5' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $account->platform_type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $account->country_name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">{{ $account->country_code }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                ${{ number_format($account->balance, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium {{ $account->equity >= $account->balance ? 'text-green-600' : 'text-red-600' }}">
                                                ${{ number_format($account->equity, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($account->whitelistedBrokerUsage->first() && $account->whitelistedBrokerUsage->first()->last_seen_at)
                                                <div class="text-sm text-gray-900">
                                                    {{ $account->whitelistedBrokerUsage->first()->last_seen_at->diffForHumans() }}
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">Never</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($account->whitelistedBrokerUsage->first() && $account->whitelistedBrokerUsage->first()->last_seen_at && $account->whitelistedBrokerUsage->first()->last_seen_at >= now()->subDays(30))
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    Dormant
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($accounts->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $accounts->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No accounts found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-enterprise-layout>
