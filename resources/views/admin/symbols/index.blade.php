<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Symbol Mappings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Symbols</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Verified</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['verified'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Unverified</div>
                    <div class="text-3xl font-bold text-orange-600">{{ $stats['unverified'] }}</div>
                </div>
            </div>

            <!-- Symbols Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Symbol Mappings</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raw Symbol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Normalized</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Suffix</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($symbols as $symbol)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($symbol->is_verified)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Verified
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                    Unverified
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $symbol->raw_symbol }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $symbol->normalized_symbol }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $symbol->broker_suffix ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $symbol->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if(!$symbol->is_verified)
                                                <form method="POST" action="{{ route('admin.symbols.verify', $symbol->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                                        Verify
                                                    </button>
                                                </form>
                                            @endif
                                            <button onclick="editSymbol({{ $symbol->id }}, '{{ $symbol->normalized_symbol }}')" class="ml-3 text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No symbols detected yet. Data will appear here once the EA starts sending information.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $symbols->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal (Simple) -->
    <div id="editModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                <h3 class="text-lg font-medium mb-4">Edit Symbol Mapping</h3>
                <form id="editForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Normalized Symbol</label>
                        <input type="text" name="normalized_symbol" id="normalizedInput" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_verified" value="1" class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Mark as verified</span>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function editSymbol(id, normalized) {
            document.getElementById('editForm').action = '/admin/symbols/' + id;
            document.getElementById('normalizedInput').value = normalized;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
