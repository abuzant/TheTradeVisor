<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Search and Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                            {{-- Search --}}
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input type="text"
                                       name="search"
                                       id="search"
                                       value="{{ $search }}"
                                       placeholder="Name or email..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Subscription Tier --}}
                            <div>
                                <label for="tier" class="block text-sm font-medium text-gray-700 mb-2">Subscription</label>
                                <select name="tier" id="tier" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Tiers</option>
                                    <option value="free" {{ $tier === 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="basic" {{ $tier === 'basic' ? 'selected' : '' }}>Basic</option>
                                    <option value="pro" {{ $tier === 'pro' ? 'selected' : '' }}>Pro</option>
                                    <option value="enterprise" {{ $tier === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                </select>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>

                            {{-- Submit --}}
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Filter
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Users Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        All Users ({{ $users->total() }})
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>

                                    <x-sortable-header
                                        column="name"
                                        label="Name"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <x-sortable-header
                                        column="email"
                                        label="Email"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <x-sortable-header
                                        column="subscription_tier"
                                        label="Plan"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Accounts
                                    </th>

                                    <x-sortable-header
                                        column="last_login_at"
                                        label="Last Login"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <x-sortable-header
                                        column="is_active"
                                        label="Status"
                                        :sortBy="$sortBy"
                                        :sortDirection="$sortDirection" />

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                        @if($user->is_admin)
                                            <span class="ml-1" title="Admin User">👨‍💼</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $user->subscription_tier === 'free' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $user->subscription_tier === 'basic' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $user->subscription_tier === 'enterprise' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                            {{ ucfirst($user->subscription_tier) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <span class="font-medium {{ $user->trading_accounts_count >= $user->max_accounts ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ $user->trading_accounts_count }}
                                            </span>
                                            <span class="text-gray-400 mx-1">/</span>
                                            <span class="text-gray-500">{{ $user->max_accounts }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($user->last_login_at)
                                            <span title="{{ $user->last_login_at->format('Y-m-d H:i:s') }}">
                                                {{ $user->last_login_at->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">Never</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $user->is_active ? '✓ Active' : '✗ Suspended' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Manage →
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No users found matching your filters.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($users->hasPages())
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            {{ $users->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
