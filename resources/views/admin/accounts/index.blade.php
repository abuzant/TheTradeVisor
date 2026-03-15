<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin - Trading Accounts Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
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

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600 mb-1">Total Accounts</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $accounts->total() }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600 mb-1">Active</div>
                    <div class="text-3xl font-bold text-green-600">
                        {{ $accounts->where('is_active', true)->where('is_paused', false)->count() }}
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600 mb-1">Paused</div>
                    <div class="text-3xl font-bold text-orange-600">
                        {{ $accounts->where('is_paused', true)->count() }}
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600 mb-1">Inactive</div>
                    <div class="text-3xl font-bold text-red-600">
                        {{ $accounts->where('is_active', false)->count() }}
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.accounts.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <input type="text"
                               name="search"
                               placeholder="Search user, broker, account..."
                               value="{{ request('search') }}"
                               class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        <select name="broker" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Brokers</option>
                            @foreach($brokers as $broker)
                                <option value="{{ $broker }}" {{ request('broker') === $broker ? 'selected' : '' }}>
                                    {{ $broker }}
                                </option>
                            @endforeach
                        </select>

                        <select name="currency" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Currencies</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}" {{ request('currency') === $currency ? 'selected' : '' }}>
                                    {{ $currency }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Filter
                            </button>
                            <a href="{{ route('admin.accounts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Accounts Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

				<thead class="bg-gray-50">
				    <tr>
				        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
				            User
				        </th>

				        <x-sortable-header
				            column="broker_name"
				            label="Broker"
				            :sortBy="$sortBy"
				            :sortDirection="$sortDirection" />

				        <x-sortable-header
				            column="account_number"
				            label="Account"
				            :sortBy="$sortBy"
				            :sortDirection="$sortDirection" />

				        <x-sortable-header
				            column="account_currency"
				            label="Currency"
				            :sortBy="$sortBy"
				            :sortDirection="$sortDirection" />
<!--
				        <x-sortable-header
				            column="balance"
				            label="Balance"
				            :sortBy="$sortBy"
				            :sortDirection="$sortDirection" />

				        <x-sortable-header
				            column="equity"
				            label="Equity"
				            :sortBy="$sortBy"
				            :sortDirection="$sortDirection" />
-->
				        <x-sortable-header
				            column="last_sync_at"
				            label="Last Sync"
				            :sortBy="$sortBy"
				            :sortDirection="$sortDirection" />

				        <x-sortable-header
				            column="is_paused"
				            label="Status"
				            :sortBy="$sortBy"
				            :sortDirection="$sortDirection" />

				        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
				            Actions
				        </th>
				    </tr>
				</thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($accounts as $account)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <a href="{{ route('admin.users.show', $account->user_id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                                        {{ $account->user->name ?? 'N/A' }}
                                                    </a>
                                                    <div class="text-xs text-gray-500">{{ $account->user->email ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if(in_array($account->broker_name, $enterpriseBrokerNames))
                                                <span class="mr-1" title="Enterprise Broker">✨</span>
                                            @endif
                                            <x-broker-name :broker="$account->broker_name" class="text-indigo-600 hover:text-indigo-900" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $account->account_number ?? 'Anonymous' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $account->account_currency }}
                                            </span>
                                        </td>
                                        <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                            {{ $account->account_currency }} {{ number_format($account->balance, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $account->account_currency }} {{ number_format($account->equity, 2) }}
                                        </td> -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($account->last_sync_at)
                                                <span title="{{ $account->last_sync_at->format('Y-m-d H:i:s') }}">
                                                    {{ $account->last_sync_at->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">Never</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($account->is_paused)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    ⏸️ Paused
                                                </span>
                                                @if($account->pause_reason)
                                                    <div class="text-xs text-gray-500 mt-1" title="{{ $account->pause_reason }}">
                                                        {{ Str::limit($account->pause_reason, 30) }}
                                                    </div>
                                                @endif
                                            @elseif($account->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    ✓ Active
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    ✗ Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                            @if($account->is_paused)
                                                <form method="POST" action="{{ route('admin.accounts.unpause', $account) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="text-green-600 hover:text-green-900 font-medium"
                                                            onclick="return confirm('Resume this account?')">
                                                        ▶️ Resume
                                                    </button>
                                                </form>
                                            @else
                                                <button onclick="openPauseModal({{ $account->id }})"
                                                        class="text-yellow-600 hover:text-yellow-900 font-medium">
                                                    ⏸️ Pause
                                                </button>
                                            @endif
                                            
                                            <button onclick="openResetModal({{ $account->id }}, '{{ $account->broker_name }}', '{{ $account->account_number }}')"
                                                    class="text-red-600 hover:text-red-900 font-medium">
                                                🔄 Reset
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500">No accounts found matching your filters.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($accounts->hasPages())
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            {{ $accounts->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Pause Modal --}}
    <div id="pauseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="handleModalClick(event)">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">⏸️ Pause Account</h3>
                    <button onclick="closePauseModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="pauseForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for pausing <span class="text-gray-500">(optional)</span>
                        </label>
                        <textarea name="reason"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                  placeholder="E.g., Suspicious activity, User request, Maintenance..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">This reason will be visible to the user and stored in logs.</p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                        <p class="text-xs text-yellow-800">
                            <strong>⚠️ Note:</strong> Pausing this account will prevent it from sending new data until resumed.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button"
                                onclick="closePauseModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            ⏸️ Pause Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reset Modal --}}
    <div id="resetModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="handleResetModalClick(event)">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-red-900">🔄 Reset Account</h3>
                    <button onclick="closeResetModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="resetForm" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-700 mb-2">
                            You are about to reset account:
                        </p>
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-3 mb-3">
                            <p class="text-sm font-semibold" id="resetAccountInfo"></p>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    ⚠️ WARNING: This action cannot be undone!
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p class="mb-2">This will permanently delete:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>All deals/trades</li>
                                        <li>All open positions</li>
                                        <li>All pending orders</li>
                                        <li>All raw data files</li>
                                        <li>Account statistics (balance, equity, etc.)</li>
                                    </ul>
                                    <p class="mt-2 font-semibold">The account will start fresh as if it was just connected.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="confirmReset" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-700">
                                I understand this will delete all data for this account
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button"
                                onclick="closeResetModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                id="confirmResetButton"
                                disabled
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            🔄 Reset Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openPauseModal(accountId) {
            document.getElementById('pauseForm').action = '/admin/accounts/' + accountId + '/pause';
            document.getElementById('pauseModal').classList.remove('hidden');
            // Focus on textarea
            setTimeout(() => {
                document.querySelector('#pauseForm textarea[name="reason"]').focus();
            }, 100);
        }

        function closePauseModal() {
            document.getElementById('pauseModal').classList.add('hidden');
            document.querySelector('#pauseForm textarea[name="reason"]').value = '';
        }

        function handleModalClick(event) {
            if (event.target.id === 'pauseModal') {
                closePauseModal();
            }
        }

        function openResetModal(accountId, brokerName, accountNumber) {
            document.getElementById('resetForm').action = '/admin/accounts/' + accountId + '/reset';
            document.getElementById('resetAccountInfo').textContent = brokerName + ' - Account #' + accountNumber;
            document.getElementById('resetModal').classList.remove('hidden');
            document.getElementById('confirmReset').checked = false;
            document.getElementById('confirmResetButton').disabled = true;
        }

        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
            document.getElementById('confirmReset').checked = false;
            document.getElementById('confirmResetButton').disabled = true;
        }

        function handleResetModalClick(event) {
            if (event.target.id === 'resetModal') {
                closeResetModal();
            }
        }

        // Enable/disable reset button based on checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('confirmReset');
            const button = document.getElementById('confirmResetButton');
            
            if (checkbox && button) {
                checkbox.addEventListener('change', function() {
                    button.disabled = !this.checked;
                });
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePauseModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
