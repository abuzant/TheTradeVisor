@extends('layouts.app')

@section('title', 'Affiliate Conversions')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Conversion Management</h2>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Approved</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Rejected</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Suspicious</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['suspicious'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" class="flex space-x-4">
                <select name="status" class="rounded-md border-gray-300">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <label class="flex items-center">
                    <input type="checkbox" name="suspicious" value="1" {{ request('suspicious') ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-2 text-sm">Suspicious Only</span>
                </label>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Filter</button>
            </form>
        </div>

        <!-- Conversions Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Affiliate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fraud Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($conversions as $conversion)
                    <tr class="{{ $conversion->is_suspicious ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 text-sm">{{ $conversion->converted_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm">{{ $conversion->affiliate->username }}</td>
                        <td class="px-6 py-4 text-sm">{{ $conversion->user->email }}</td>
                        <td class="px-6 py-4 text-sm font-semibold">${{ number_format($conversion->commission_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded {{ $conversion->fraud_score >= 50 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $conversion->fraud_score }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($conversion->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded">Pending</span>
                            @elseif($conversion->status === 'approved')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Approved</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            @if($conversion->status === 'pending')
                                <form method="POST" action="{{ route('admin.affiliates.conversions.approve', $conversion) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.affiliates.conversions.reject', $conversion) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $conversions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
