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

                {{-- Today's Trades --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Trades Today</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_trades_today']) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Today's Volume --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Volume Today</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_volume_today'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ route('admin.logs') }}" class="block text-sm text-white hover:text-indigo-100">
                                → View System Logs
                            </a>
                            <a href="{{ route('admin.services') }}" class="block text-sm text-white hover:text-indigo-100">
                                → Service Management
                            </a>
                        </div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $user->subscription_tier === 'free' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $user->subscription_tier === 'basic' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $user->subscription_tier === 'pro' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $user->subscription_tier === 'enterprise' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                {{ ucfirst($user->subscription_tier) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->tradingAccounts->count() }} / {{ $user->max_accounts }}
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($account->balance, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $account->detected_country ?? 'Unknown' }}</td>
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
