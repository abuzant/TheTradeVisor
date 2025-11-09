<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User: {{ $user->name }}
                @if($user->is_admin)
                    <span class="ml-2" title="Admin User">👨‍💼</span>
                @endif
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                ← Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                            @if(session('new_api_key'))
                                <p class="text-sm text-green-700 mt-2 font-mono">New API Key: {{ session('new_api_key') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- User Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Total Accounts</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['total_accounts'] }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $stats['active_accounts'] }} active</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Total Balance</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['display_currency'] }} {{ number_format($stats['total_balance'], 2) }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Total Equity</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['display_currency'] }} {{ number_format($stats['total_equity'], 2) }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 mb-1">Member Since</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['member_since'] }}</div>
                        <div class="text-xs text-gray-500 mt-1">Last login: {{ $stats['last_login'] }}</div>
                    </div>
                </div>

            </div>

            {{-- User Details --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                                Edit User
                            </a>
                            @if($user->is_active)
                                <form method="POST" action="{{ route('admin.users.suspend', $user) }}" onsubmit="return confirm('Are you sure you want to suspend this user?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                                        Suspend User
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                                        Activate User
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Subscription Tier</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $user->subscription_tier === 'free' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $user->subscription_tier === 'basic' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $user->subscription_tier === 'pro' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $user->subscription_tier === 'enterprise' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                            {{ ucfirst($user->subscription_tier) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Account Limit</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->max_accounts }} accounts</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->is_active ? 'Active' : 'Suspended' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Admin</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $user->is_admin ? 'Yes 👨‍💼' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Registered</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- API Key --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-semibold text-gray-900">API Key</h4>
                            <form method="POST" action="{{ route('admin.users.regenerate-api-key', $user) }}" onsubmit="return confirm('Are you sure? This will disconnect all MT5 terminals.')">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-xs font-medium">
                                    Regenerate
                                </button>
                            </form>
                        </div>
                        <div class="bg-gray-50 rounded-md p-3">
                            <code class="text-xs font-mono text-gray-700 break-all">{{ $user->api_key }}</code>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            @if($user->id !== auth()->id())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Danger Zone</h3>
                    <div class="flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to DELETE this user? This action cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-900 text-sm font-medium">
                                Delete User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Trading Accounts --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Trading Accounts</h3>

                    @if($user->tradingAccounts->isEmpty())
                        <p class="text-sm text-gray-500">No trading accounts connected yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Broker</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
					<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Sync</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($user->tradingAccounts as $account)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <x-broker-name :broker="$account->broker_name" class="text-indigo-600 hover:text-indigo-900" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $account->account_number ?? 'Anonymous' }}</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm">
					    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
					        {{ $account->account_type === 'real' ? 'bg-green-100 text-green-800' : '' }}
					        {{ $account->account_type === 'demo' ? 'bg-yellow-100 text-yellow-800' : '' }}
					        {{ $account->account_type === 'contest' ? 'bg-blue-100 text-blue-800' : '' }}">
					        {{ ucfirst($account->account_type) }}
					    </span>
					</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->account_currency }} {{ number_format($account->balance, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->account_currency }} {{ number_format($account->equity, 2) }}</td>
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
                    @endif
                </div>
            </div>

            {{-- Payment History (Placeholder for future) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
                    <p class="text-sm text-gray-500 italic">Payment integration coming soon...</p>
                    {{-- We'll implement this later with Stripe/PayPal --}}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
