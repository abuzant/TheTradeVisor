@extends('layouts.affiliate')

@section('title', 'Payouts')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Earnings Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Pending Earnings</h4>
                    <p class="text-3xl font-bold text-yellow-600">${{ number_format($affiliate->pending_earnings, 2) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Approved Earnings</h4>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($affiliate->approved_earnings, 2) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Total Paid</h4>
                    <p class="text-3xl font-bold text-indigo-600">${{ number_format($affiliate->total_paid, 2) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Lifetime Earnings</h4>
                    <p class="text-3xl font-bold text-purple-600">${{ number_format($affiliate->total_earnings, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Request Payout Section -->
        @if($affiliate->approved_earnings >= 50)
        <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-green-900 mb-2">Ready for Payout!</h3>
                    <p class="text-sm text-green-700">You have ${{ number_format($affiliate->approved_earnings, 2) }} available for withdrawal</p>
                </div>
                <form method="POST" action="{{ route('affiliate.payouts.request') }}">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                        Request Payout
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-6 rounded-lg">
            <h3 class="text-lg font-semibold text-yellow-900 mb-2">Minimum Payout: $50.00</h3>
            <p class="text-sm text-yellow-700">You need ${{ number_format(50 - $affiliate->approved_earnings, 2) }} more in approved earnings to request a payout</p>
            <div class="mt-3 w-full bg-yellow-200 rounded-full h-3">
                <div class="bg-yellow-600 h-3 rounded-full" style="width: {{ min(($affiliate->approved_earnings / 50) * 100, 100) }}%"></div>
            </div>
        </div>
        @endif

        <!-- Wallet Settings -->
        @if(!$affiliate->usdt_wallet_address)
        <div class="bg-red-50 border-l-4 border-red-400 p-6 mb-6 rounded-lg">
            <h3 class="text-lg font-semibold text-red-900 mb-2">⚠️ Wallet Not Configured</h3>
            <p class="text-sm text-red-700 mb-3">Please set up your USDT wallet address to receive payouts</p>
            <a href="{{ route('affiliate.settings') }}" class="inline-block px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                Configure Wallet
            </a>
        </div>
        @else
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payout Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Wallet Address</label>
                        <p class="text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded mt-1">{{ $affiliate->usdt_wallet_address }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Wallet Type</label>
                        <p class="text-sm text-gray-900 mt-1">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full">{{ $affiliate->wallet_type }}</span>
                        </p>
                    </div>
                </div>
                <a href="{{ route('affiliate.settings') }}" class="text-sm text-indigo-600 hover:text-indigo-900 mt-3 inline-block">
                    Update Wallet Settings →
                </a>
            </div>
        </div>
        @endif

        <!-- Pending Conversions -->
        @if($pendingConversions->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pending Conversions ({{ $pendingConversions->count() }})</h3>
                <p class="text-sm text-gray-600 mb-4">These conversions are awaiting approval (7-day cooling period + admin review)</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pendingConversions as $conversion)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $conversion->converted_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">User #{{ $conversion->user_id }}</td>
                                <td class="px-4 py-2 text-sm font-semibold text-green-600">${{ number_format($conversion->commission_amount, 2) }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded">
                                        {{ ucfirst($conversion->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Payout History -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payout History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Request Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">USDT Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Processed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($payouts as $payout)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $payout->requested_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-2 text-sm font-semibold text-gray-900">${{ number_format($payout->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($payout->usdt_amount, 2) }} USDT</td>
                                <td class="px-4 py-2">
                                    @if($payout->status === 'completed')
                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Completed</span>
                                    @elseif($payout->status === 'processing')
                                        <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">Processing</span>
                                    @elseif($payout->status === 'rejected')
                                        <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">Rejected</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    {{ $payout->processed_at ? $payout->processed_at->format('M d, Y') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No payout history yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($payouts->hasPages())
                <div class="mt-4">
                    {{ $payouts->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-6 mt-6 rounded-lg">
            <h4 class="text-sm font-semibold text-blue-900 mb-2">💡 Payout Information</h4>
            <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                <li>Minimum payout threshold: $50.00 USD</li>
                <li>Conversions must be approved before payout (7-day cooling period)</li>
                <li>Payouts are processed in USDT (TRC20 or ERC20)</li>
                <li>Processing time: 1-3 business days after approval</li>
                <li>Make sure your wallet address is correct before requesting payout</li>
            </ul>
        </div>
    </div>
</div>
@endsection
