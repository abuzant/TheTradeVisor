@section('title', 'My Trading Accounts - TheTradeVisor | Manage MT5 Accounts')
@section('description', 'Manage all your MetaTrader 5 trading accounts in one place. View balances, monitor performance, and control account settings.')
@section('og_title', 'My Trading Accounts - TheTradeVisor')
@section('og_description', 'Manage your MT5 trading accounts and monitor performance')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            My Trading Accounts
        </h1>
        <p class="mt-1 text-sm text-gray-600">Manage and monitor all your MT5 accounts</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card p-6 mb-6">
                <form method="GET" action="{{ route('accounts.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Broker, account..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Broker</label>
                            <select name="broker" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Brokers</option>
                                @foreach($brokers as $b)
                                    <option value="{{ $b }}" {{ $broker === $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Currencies</option>
                                @foreach($currencies as $c)
                                    <option value="{{ $c }}" {{ $currency === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Statuses</option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="paused" {{ $status === 'paused' ? 'selected' : '' }}>Paused</option>
                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('accounts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Clear
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            {{-- Accounts Table --}}
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                <div class="p-6">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($accounts->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500">No accounts found.</p>
                        </div>
                    @else
                        <div class="mb-4 flex justify-between items-center">
                            <div class="flex items-center space-x-4">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="selectAll" class="text-sm text-gray-700">Select All</label>
                            </div>
                            <button onclick="deleteSelected()" id="deleteSelectedBtn" disabled
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                Delete Selected
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            <input type="checkbox" id="headerCheckbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </th>
                                        <x-sortable-header column="broker_name" label="Broker" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                        <x-sortable-header column="account_number" label="Account" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                        <x-sortable-header column="account_currency" label="Currency" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                        <x-sortable-header column="balance" label="Balance" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <x-sortable-header column="last_sync_at" label="Last Sync" :sortBy="$sortBy" :sortDirection="$sortDirection" />
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($accounts as $account)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" class="account-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                                       value="{{ $account->id }}" data-account-id="{{ $account->id }}">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <x-broker-name :broker="$account->broker_name" class="text-indigo-600 hover:text-indigo-900" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center space-x-2">
                                                    <span>{{ $account->account_number ?? 'Anonymous' }}</span>
                                                    <x-platform-badge :account="$account" />
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $account->account_currency }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($account->balance, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($account->is_paused)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Paused
                                                    </span>
                                                @elseif($account->is_active)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $account->last_sync_at ? $account->last_sync_at->diffForHumans() : 'Never' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('account.show', $account->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">View</a>

                                                @if($account->is_paused)
                                                    <form method="POST" action="{{ route('accounts.unpause', $account) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900">Resume</button>
                                                    </form>
                                                @else
                                                    <button onclick="pauseAccount({{ $account->id }})"
                                                            class="text-yellow-600 hover:text-yellow-900">Pause</button>
                                                @endif
                                                
                                                <form method="POST" action="{{ route('accounts.destroy', $account) }}" class="inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this account? The data will be transferred to an anonymous user for global analytics.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="8" class="px-6 py-3">
                                            <div class="flex items-center space-x-4 text-xs text-gray-600">
                                                <span class="font-semibold">Platform Legend:</span>
                                                <div class="flex items-center space-x-1">
                                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-semibold">MT4</span>
                                                    <span>= MetaTrader 4</span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-semibold">MT5</span>
                                                    <span>= MetaTrader 5</span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <span class="px-1.5 py-0.5 rounded bg-purple-200 text-purple-900 font-bold">N</span>
                                                    <span>= Netting</span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <span class="px-1.5 py-0.5 rounded bg-blue-200 text-blue-900 font-bold">H</span>
                                                    <span>= Hedging</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $accounts->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Pause Modal --}}
    <div id="pauseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Pause Account</h3>
                <form id="pauseForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason (optional)</label>
                        <textarea name="reason" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Why are you pausing this account?"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closePauseModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                            Pause Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function pauseAccount(accountId) {
            document.getElementById('pauseForm').action = `/accounts/${accountId}/pause`;
            document.getElementById('pauseModal').classList.remove('hidden');
        }

        function closePauseModal() {
            document.getElementById('pauseModal').classList.add('hidden');
        }

        // Checkbox functionality
        const headerCheckbox = document.getElementById('headerCheckbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        const accountCheckboxes = document.querySelectorAll('.account-checkbox');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

        function updateDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.account-checkbox:checked');
            deleteSelectedBtn.disabled = checkedBoxes.length === 0;
        }

        function updateHeaderCheckbox() {
            const totalBoxes = document.querySelectorAll('.account-checkbox');
            const checkedBoxes = document.querySelectorAll('.account-checkbox:checked');
            headerCheckbox.checked = totalBoxes.length > 0 && totalBoxes.length === checkedBoxes.length;
            selectAllCheckbox.checked = headerCheckbox.checked;
        }

        headerCheckbox.addEventListener('change', function() {
            accountCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            selectAllCheckbox.checked = this.checked;
            updateDeleteButton();
        });

        selectAllCheckbox.addEventListener('change', function() {
            accountCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            headerCheckbox.checked = this.checked;
            updateDeleteButton();
        });

        accountCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateDeleteButton();
                updateHeaderCheckbox();
            });
        });

        function deleteSelected() {
            const checkedBoxes = document.querySelectorAll('.account-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one account to delete.');
                return;
            }

            const accountIds = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (confirm(`Are you sure you want to delete ${accountIds.length} account(s)? The data will be transferred to an anonymous user for global analytics.`)) {
                // Delete accounts one by one
                accountIds.forEach((accountId, index) => {
                    setTimeout(() => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/accounts/${accountId}`;
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        
                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }, index * 100); // Small delay between submissions
                });
            }
        }

        // Initialize
        updateDeleteButton();
        updateHeaderCheckbox();
    </script>
    @endpush
</x-app-layout>
