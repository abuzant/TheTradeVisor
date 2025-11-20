@extends('layouts.app')

@section('title', 'Affiliate Management')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Affiliate Management</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.affiliates.conversions') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                    Conversions
                </a>
                <a href="{{ route('admin.affiliates.payouts') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Payouts
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Affiliates</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_affiliates']) }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Active</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['active_affiliates']) }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Clicks</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_clicks']) }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Conversions</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total_conversions']) }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-sm text-gray-500">Pending Payouts</p>
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending_payouts']) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" class="flex space-x-4">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" class="flex-1 rounded-md border-gray-300">
                <select name="status" class="rounded-md border-gray-300">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Filter</button>
            </form>
        </div>

        <!-- Affiliates Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Affiliate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clicks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Signups</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Earnings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($affiliates as $affiliate)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $affiliate->username }}</div>
                            <div class="text-sm text-gray-500">{{ $affiliate->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ number_format($affiliate->total_clicks) }}</td>
                        <td class="px-6 py-4 text-sm">{{ number_format($affiliate->total_signups) }}</td>
                        <td class="px-6 py-4 text-sm">${{ number_format($affiliate->total_earnings, 2) }}</td>
                        <td class="px-6 py-4">
                            @if($affiliate->is_active)
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            <form method="POST" action="{{ route('admin.affiliates.toggle-status', $affiliate) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                    {{ $affiliate->is_active ? 'Suspend' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4">
                {{ $affiliates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
