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
                <a href="{{ route('admin.users.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
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
                                    <dd class="text-xs text-gray-500 mt-1">{{ $stats['users_last_24_hours'] }} added users in the last 24 hours</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Total Accounts --}}
                <a href="{{ route('admin.accounts.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
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
                                    <dd class="text-xs text-gray-500 mt-1">{{ $stats['active_accounts'] }} active, {{ $stats['accounts_last_24_hours'] }} in the last 24 hours</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Active Positions --}}
                <a href="{{ route('admin.trades.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
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
                                    <dd class="text-xs text-gray-500 mt-1">{{ $stats['positions_opened_last_24_hours'] }} opened / {{ $stats['positions_closed_last_24_hours'] }} closed in the last 24 hours</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Brokers Statistics --}}
                <a href="{{ route('admin.brokers.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
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
                                    <dd class="text-xs text-gray-500 mt-1">{{ $stats['enterprise_brokers'] }} enterprise, {{ $stats['new_brokers_last_24_hours'] }} new in the last 24 hours</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Next Enterprise Expiry --}}
                <a href="{{ route('admin.brokers.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
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
                </a>

                {{-- Active Terminals --}}
                <a href="{{ route('admin.monitoring.dashboard') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
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
                </a>

                {{-- New Uninstalls --}}
                <a href="{{ route('admin.uninstall-feedback.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">New Uninstalls</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['uninstalls_last_24_hours']) }}</dd>
                                    <dd class="text-xs text-gray-500 mt-1">{{ $stats['uninstalls_with_comments_last_24_hours'] }} with comments / {{ $stats['uninstalls_with_email_last_24_hours'] }} with emails</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Backup Details --}}
                <a href="{{ route('admin.backup.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gray-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V2"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Backup Details</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_backups']) }} total backups</dd>
                                    <dd class="text-xs text-gray-500 mt-1">{{ $stats['filesystem_backups'] }} filesystem / {{ $stats['database_backups'] }} database backups</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Error Rate --}}
                <a href="{{ route('admin.monitoring.dashboard') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg block hover:bg-gray-50 transition-colors duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 {{ $stats['error_rate_last_hour'] > $stats['error_rate_threshold'] ? 'bg-red-500' : ($stats['error_rate_last_hour'] > ($stats['error_rate_threshold'] * 0.7) ? 'bg-yellow-500' : 'bg-green-500') }} rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Error Rate (Last Hour)</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['error_rate_last_hour'], 2) }}</dd>
                                    <dd class="text-xs text-gray-500 mt-1">
                                        @if($stats['error_rate_last_hour'] > $stats['error_rate_threshold'])
                                            above the threshold of {{ $stats['error_rate_threshold'] }}%
                                        @else
                                            within threshold of {{ $stats['error_rate_threshold'] }}%
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

            </div>

            {{-- Terminal Connections Map --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Terminal Connections (Last 50)</h3>
                        <div class="text-sm text-gray-500">
                            {{ $terminalLocations['unique_countries'] }} countries • {{ $terminalLocations['total_connections'] }} connections
                        </div>
                    </div>
                    <div id="terminal-map" class="h-96 rounded-lg border border-gray-200"></div>
                    <div class="mt-4 text-xs text-gray-500 text-center">
                        Latest connection: {{ $terminalLocations['latest_connection'] ? $terminalLocations['latest_connection']->diffForHumans() : 'No data' }}
                    </div>
                </div>
            </div>

            {{-- Map Scripts --}}
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize map
                    var map = L.map('terminal-map', { zoomControl: false, scrollWheelZoom: false }).setView([20, 0], 1.25);
                    
                    // Add OpenStreetMap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    
                    // Terminal locations data
                    var locations = @json($terminalLocations['locations']);
                    
                    // Add markers for each country
                    locations.forEach(function(location) {
                        var markerSize = Math.max(10, Math.min(30, location.connections * 5));
                        var color = location.connections > 10 ? '#dc2626' : location.connections > 5 ? '#f59e0b' : '#10b981';
                        
                        var marker = L.circleMarker([location.lat, location.lng], {
                            radius: markerSize,
                            fillColor: color,
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(map);
                        
                        // Create popup content
                        var popupContent = '<div class="text-sm">' +
                            '<strong>' + location.country + '</strong><br>' +
                            'Connections: ' + location.connections + '<br>' +
                            'IPs: ' + location.ips.slice(0, 3).join(', ') + (location.ips.length > 3 ? '...' : '') + '<br>' +
                            'Brokers: ' + location.brokers.slice(0, 2).join(', ') + (location.brokers.length > 2 ? '...' : '') + '<br>' +
                            'Last: ' + new Date(location.last_connection).toLocaleString() +
                            '</div>';
                        
                        marker.bindPopup(popupContent);
                    });
                    
                });
            </script>

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
