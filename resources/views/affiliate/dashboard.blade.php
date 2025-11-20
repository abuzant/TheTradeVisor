@extends('layouts.affiliate')

@section('title', 'Affiliate Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Clicks -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Clicks</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_clicks']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Signups -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Signups</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_signups']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid Signups -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Paid Signups</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($metrics['total_paid_signups']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Earnings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Earnings</dt>
                                <dd class="text-2xl font-semibold text-gray-900">${{ number_format($metrics['total_earnings'], 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Affiliate Info Card -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold mb-2">Your Referral Link</h3>
                    <div class="flex items-center space-x-2">
                        <code class="bg-white/20 px-4 py-2 rounded text-sm">{{ $affiliate->referral_url }}</code>
                        <button onclick="copyToClipboard('{{ $affiliate->referral_url }}')" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded text-sm transition">
                            Copy
                        </button>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90">Commission Rate</p>
                    <p class="text-3xl font-bold">$1.99</p>
                    <p class="text-xs opacity-75">per paid signup</p>
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Overview (Last 30 Days)</h3>
                <canvas id="performanceChart" height="80"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Clicks -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Clicks</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentClicks as $click)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $click->clicked_at->format('M d, H:i') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $click->country_code ?? 'Unknown' }}</td>
                                    <td class="px-4 py-2">
                                        @if($click->converted)
                                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Converted</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No clicks yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Conversions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Conversions</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentConversions as $conversion)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $conversion->converted_at->format('M d, H:i') }}</td>
                                    <td class="px-4 py-2 text-sm font-semibold text-green-600">${{ number_format($conversion->commission_amount, 2) }}</td>
                                    <td class="px-4 py-2">
                                        @if($conversion->status === 'approved')
                                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Approved</span>
                                        @elseif($conversion->status === 'paid')
                                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">Paid</span>
                                        @elseif($conversion->status === 'rejected')
                                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded">Rejected</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No conversions yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Campaigns -->
        @if(!empty($topCampaigns))
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Campaigns</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Conversions</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">CVR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($topCampaigns as $campaign)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $campaign['utm_campaign'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $campaign['utm_source'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($campaign['clicks']) }}</td>
                                <td class="px-4 py-2 text-sm font-semibold text-green-600">{{ number_format($campaign['conversions']) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $campaign['clicks'] > 0 ? number_format(($campaign['conversions'] / $campaign['clicks']) * 100, 2) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Referral link copied to clipboard!');
    });
}

// Performance Chart
const ctx = document.getElementById('performanceChart').getContext('2d');
const dailyData = @json($metrics['daily_data']);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: dailyData.map(d => d.date),
        datasets: [
            {
                label: 'Clicks',
                data: dailyData.map(d => d.clicks),
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4
            },
            {
                label: 'Signups',
                data: dailyData.map(d => d.signups),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4
            },
            {
                label: 'Paid Signups',
                data: dailyData.map(d => d.paid_signups),
                borderColor: 'rgb(234, 179, 8)',
                backgroundColor: 'rgba(234, 179, 8, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection
