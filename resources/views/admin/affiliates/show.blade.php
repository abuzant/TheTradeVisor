@extends('layouts.app')

@section('title', 'Affiliate Details')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Affiliate: {{ $affiliate->username }}</h2>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('admin.affiliates.toggle-status', $affiliate) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 {{ $affiliate->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-md">
                        {{ $affiliate->is_active ? 'Suspend' : 'Activate' }}
                    </button>
                </form>
                <a href="{{ route('admin.affiliates.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    Back to List
                </a>
            </div>
        </div>

        <!-- Affiliate Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Username</label>
                        <p class="text-gray-900">{{ $affiliate->username }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900">{{ $affiliate->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Referral Slug</label>
                        <p class="text-gray-900 font-mono">{{ $affiliate->slug }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <p>
                            @if($affiliate->is_active)
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">Suspended</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Joined</label>
                        <p class="text-gray-900">{{ $affiliate->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Last Login</label>
                        <p class="text-gray-900">{{ $affiliate->last_login_at ? $affiliate->last_login_at->diffForHumans() : 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500 mb-1">Total Clicks</p>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($affiliate->total_clicks) }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500 mb-1">Total Signups</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($affiliate->total_signups) }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500 mb-1">Paid Signups</p>
                <p class="text-3xl font-bold text-yellow-600">{{ number_format($affiliate->paid_signups) }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500 mb-1">Total Earnings</p>
                <p class="text-3xl font-bold text-purple-600">${{ number_format($affiliate->total_earnings, 2) }}</p>
            </div>
        </div>

        <!-- Earnings Breakdown -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Earnings Breakdown</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Pending</label>
                        <p class="text-2xl font-bold text-yellow-600">${{ number_format($affiliate->pending_earnings, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Approved</label>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($affiliate->approved_earnings, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Total Paid</label>
                        <p class="text-2xl font-bold text-indigo-600">${{ number_format($affiliate->total_paid, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Lifetime</label>
                        <p class="text-2xl font-bold text-purple-600">${{ number_format($affiliate->total_earnings, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Clicks -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Clicks (Last 50)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Converted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($affiliate->clicks as $click)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $click->clicked_at->format('M d, H:i') }}</td>
                                <td class="px-4 py-2 text-sm font-mono">{{ $click->ip_address }}</td>
                                <td class="px-4 py-2 text-sm">{{ $click->country_code }}, {{ $click->city }}</td>
                                <td class="px-4 py-2 text-sm">{{ $click->utm_campaign ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    @if($click->converted)
                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Yes</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded">No</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No clicks yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Conversions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Conversions (Last 50)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fraud Score</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($affiliate->conversions as $conversion)
                            <tr class="{{ $conversion->is_suspicious ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-2 text-sm">{{ $conversion->converted_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-sm">{{ $conversion->user->email }}</td>
                                <td class="px-4 py-2 text-sm font-semibold">${{ number_format($conversion->commission_amount, 2) }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded {{ $conversion->fraud_score >= 50 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $conversion->fraud_score }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    @if($conversion->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded">Pending</span>
                                    @elseif($conversion->status === 'approved')
                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Approved</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No conversions yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payout History -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payout History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Wallet</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Processed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($affiliate->payouts as $payout)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $payout->requested_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-sm font-semibold">${{ number_format($payout->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm font-mono text-xs">{{ substr($payout->wallet_address, 0, 10) }}...</td>
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
                                <td class="px-4 py-2 text-sm">{{ $payout->processed_at ? $payout->processed_at->format('M d, Y') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No payouts yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
