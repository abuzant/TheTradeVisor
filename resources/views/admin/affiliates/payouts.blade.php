@extends('layouts.app')

@section('title', 'Affiliate Payouts')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Payout Management</h2>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Processing</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['processing'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Paid</p>
                <p class="text-2xl font-bold">${{ number_format($stats['total_amount'], 2) }}</p>
            </div>
        </div>

        <!-- Payouts Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Affiliate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wallet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payouts as $payout)
                    <tr>
                        <td class="px-6 py-4 text-sm">{{ $payout->requested_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm">{{ $payout->affiliate->username }}</td>
                        <td class="px-6 py-4 text-sm font-semibold">${{ number_format($payout->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-xs">
                            {{ substr($payout->wallet_address, 0, 10) }}...
                            <span class="text-indigo-600">({{ $payout->wallet_type }})</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($payout->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded">Pending</span>
                            @elseif($payout->status === 'processing')
                                <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">Processing</span>
                            @elseif($payout->status === 'completed')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Completed</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($payout->status === 'pending')
                                <button onclick="showProcessModal({{ $payout->id }}, '{{ $payout->wallet_address }}')" class="text-green-600 hover:text-green-900">Process</button>
                                <form method="POST" action="{{ route('admin.affiliates.payouts.reject', $payout) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Reject</button>
                                </form>
                            @elseif($payout->transaction_hash)
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">View TX</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $payouts->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Process Modal -->
<div id="processModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Process Payout</h3>
        <form id="processForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Wallet Address</label>
                <p id="modalWallet" class="text-sm font-mono bg-gray-50 p-2 rounded"></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Hash</label>
                <input type="text" name="transaction_hash" required class="w-full rounded-md border-gray-300" placeholder="Enter USDT transaction hash">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeProcessModal()" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
function showProcessModal(payoutId, wallet) {
    document.getElementById('processForm').action = `/admin/affiliates/payouts/${payoutId}/process`;
    document.getElementById('modalWallet').textContent = wallet;
    document.getElementById('processModal').classList.remove('hidden');
}

function closeProcessModal() {
    document.getElementById('processModal').classList.add('hidden');
}
</script>
@endsection
