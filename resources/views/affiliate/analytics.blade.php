@extends('layouts.affiliate')

@section('title', 'Analytics')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Time Period Filter -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('affiliate.analytics') }}" class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Time Period:</label>
                    <select name="days" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="365" {{ $days == 365 ? 'selected' : '' }}>Last Year</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Conversion Rate</h4>
                    <p class="text-3xl font-bold text-indigo-600">
                        {{ $metrics['total_clicks'] > 0 ? number_format(($metrics['total_signups'] / $metrics['total_clicks']) * 100, 2) : 0 }}%
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Click to Signup</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Paid Conversion Rate</h4>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $metrics['total_signups'] > 0 ? number_format(($metrics['total_paid_signups'] / $metrics['total_signups']) * 100, 2) : 0 }}%
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Signup to Paid</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Average Earnings Per Click</h4>
                    <p class="text-3xl font-bold text-purple-600">
                        ${{ $metrics['total_clicks'] > 0 ? number_format($metrics['total_earnings'] / $metrics['total_clicks'], 4) : 0 }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">EPC</p>
                </div>
            </div>
        </div>

        <!-- Performance Trend Chart -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Trend</h3>
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Geographic Distribution -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Geographic Distribution</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Conversions</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">CVR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($geoDistribution as $geo)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $geo['country_code'] }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($geo['clicks']) }}</td>
                                    <td class="px-4 py-2 text-sm text-green-600">{{ number_format($geo['conversions']) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $geo['clicks'] > 0 ? number_format(($geo['conversions'] / $geo['clicks']) * 100, 2) : 0 }}%
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Campaigns -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Campaigns</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Conv.</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">CVR</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($topCampaigns as $campaign)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        <div class="font-medium">{{ $campaign['utm_campaign'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $campaign['utm_source'] ?? 'Direct' }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($campaign['clicks']) }}</td>
                                    <td class="px-4 py-2 text-sm text-green-600">{{ number_format($campaign['conversions']) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $campaign['clicks'] > 0 ? number_format(($campaign['conversions'] / $campaign['clicks']) * 100, 2) : 0 }}%
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No campaigns tracked yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Funnel -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversion Funnel</h3>
                <div class="space-y-4">
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Clicks</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($metrics['total_clicks']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-indigo-600 h-4 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Signups</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($metrics['total_signups']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-green-600 h-4 rounded-full" style="width: {{ $metrics['total_clicks'] > 0 ? ($metrics['total_signups'] / $metrics['total_clicks']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Paid Signups</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($metrics['total_paid_signups']) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-yellow-600 h-4 rounded-full" style="width: {{ $metrics['total_clicks'] > 0 ? ($metrics['total_paid_signups'] / $metrics['total_clicks']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Trend Chart
const ctx = document.getElementById('trendChart').getContext('2d');
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
                tension: 0.4,
                fill: true
            },
            {
                label: 'Signups',
                data: dailyData.map(d => d.signups),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Paid Signups',
                data: dailyData.map(d => d.paid_signups),
                borderColor: 'rgb(234, 179, 8)',
                backgroundColor: 'rgba(234, 179, 8, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Earnings ($)',
                data: dailyData.map(d => d.earnings),
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            if (context.dataset.yAxisID === 'y1') {
                                label += '$' + context.parsed.y.toFixed(2);
                            } else {
                                label += context.parsed.y;
                            }
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Count'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false,
                },
                title: {
                    display: true,
                    text: 'Earnings ($)'
                }
            }
        }
    }
});
</script>
@endsection
