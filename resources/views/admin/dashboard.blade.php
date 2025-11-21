<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Total Users --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Accounts --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Trading Accounts</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_accounts']) }}</dd>
                                    <dd class="text-xs text-gray-500 mt-1">{{ $stats['active_accounts'] }} active</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active Positions --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Open Positions</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_positions']) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Brokers Statistics --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Brokers</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['known_brokers']) }}</dd>
                                    <dd class="text-xs text-gray-500 mt-1">{{ $stats['enterprise_brokers'] }} enterprise</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Next Enterprise Expiry --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Next Enterprise Expiry</dt>
                                    @if($nextExpiry)
                                        <dd class="text-lg font-semibold text-gray-900 truncate" title="{{ $nextExpiry->company_name }}">{{ $nextExpiry->company_name }}</dd>
                                        <dd class="text-xs text-gray-500 mt-1">{{ $nextExpiry->subscription_ends_at->format('M d, Y') }} ({{ $nextExpiry->subscription_ends_at->diffForHumans() }})</dd>
                                    @else
                                        <dd class="text-lg font-semibold text-gray-500">No active subscriptions</dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active Terminals --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-cyan-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Terminals</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['active_terminals']) }}</dd>
                                    <dd class="text-xs text-gray-500 mt-1">Last hour</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Quick Actions --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('admin.logs') }}" class="block text-sm text-white hover:text-indigo-100">
                            → View System Logs
                        </a>
                        <a href="{{ route('admin.services') }}" class="block text-sm text-white hover:text-indigo-100">
                            → Service Management
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="block text-sm text-white hover:text-indigo-100">
                            → Manage Users
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Users --}}
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                View All Users →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <x-sortable-header-custom column="name" label="Name" :sortBy="$usersSortBy" :sortDirection="$usersSortDirection" sortByParam="users_sort_by" sortDirectionParam="users_sort_direction" />
                        <x-sortable-header-custom column="email" label="Email" :sortBy="$usersSortBy" :sortDirection="$usersSortDirection" sortByParam="users_sort_by" sortDirectionParam="users_sort_direction" />
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Broker</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Accounts</th>
                        <x-sortable-header-custom column="created_at" label="Registered" :sortBy="$usersSortBy" :sortDirection="$usersSortDirection" sortByParam="users_sort_by" sortDirectionParam="users_sort_direction" />
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentUsers as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $user->name }}
                            @if($user->is_admin)
                                <span class="ml-1" title="Admin User">👨‍💼</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                                $brokers = $user->tradingAccounts->pluck('broker_name')->filter()->countBy();
                                $primaryBroker = $brokers->sortDesc()->keys()->first();
                                $isEnterprise = $primaryBroker && in_array($primaryBroker, $enterpriseBrokerNames);
                            @endphp
                            @if($primaryBroker)
                                @if($isEnterprise)
                                    <span class="mr-1" title="Enterprise Broker">✨</span>
                                @endif
                                <x-broker-name :broker="$primaryBroker" />
                                @if($brokers->count() > 1)
                                    <span class="text-xs text-gray-500 ml-1">(+{{ $brokers->count() - 1 }})</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">No accounts</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($primaryBroker)
                                {{ $brokers[$primaryBroker] }} {{ Str::plural('account', $brokers[$primaryBroker]) }}
                            @else
                                0 accounts
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">Manage</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $recentUsers->links() }}
        </div>
    </div>
</div>

            {{-- Recent Trading Accounts --}}
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Trading Account Activity</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <x-sortable-header-custom column="broker_name" label="Broker" :sortBy="$accountsSortBy" :sortDirection="$accountsSortDirection" sortByParam="accounts_sort_by" sortDirectionParam="accounts_sort_direction" />
                        <x-sortable-header-custom column="account_number" label="Account" :sortBy="$accountsSortBy" :sortDirection="$accountsSortDirection" sortByParam="accounts_sort_by" sortDirectionParam="accounts_sort_direction" />
                        <x-sortable-header-custom column="balance" label="Balance" :sortBy="$accountsSortBy" :sortDirection="$accountsSortDirection" sortByParam="accounts_sort_by" sortDirectionParam="accounts_sort_direction" />
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                        <x-sortable-header-custom column="last_sync_at" label="Last Sync" :sortBy="$accountsSortBy" :sortDirection="$accountsSortDirection" sortByParam="accounts_sort_by" sortDirectionParam="accounts_sort_direction" />
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentAccounts as $account)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.users.show', $account->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $account->user->name }}
                                @if($account->user->is_admin)
                                    <span class="ml-1" title="Admin User">👨‍💼</span>
                                @endif
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <x-broker-name :broker="$account->broker_name" class="text-indigo-600 hover:text-indigo-900" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $account->account_number ?? 'Anonymous' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->account_currency }} {{ number_format($account->balance, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
    @if($account->country_code)
        <span class="inline-flex items-center">
            {!! \App\Helpers\CountryHelper::getFlag($account->country_code) !!}
            {{ $account->country_name ?? $account->detected_country }}
        </span>
    @else
        <span class="inline-flex items-center">
            <i class="fi fi-globe mr-2"></i>
            {{ $account->detected_country ?? 'Unknown' }}
        </span>
    @endif
</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $account->last_sync_at ? $account->last_sync_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $recentAccounts->links() }}
        </div>
    </div>
</div>

        </div>
    </div>
</x-app-layout>
