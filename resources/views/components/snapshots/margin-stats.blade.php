@props(['chartData', 'margin', 'currency'])

<div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900">📊 Margin Usage</h3>
        <p class="text-sm text-gray-600">Margin utilization statistics</p>
    </div>

    <div class="relative" style="height: 200px;">
        <canvas id="marginChart"></canvas>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="text-center p-3 bg-purple-50 rounded-lg">
            <div class="text-xs text-gray-500 mb-1">Peak Margin</div>
            <div class="text-xl font-bold text-purple-600">
                {{ number_format($margin['max'], 2) }}
            </div>
            <div class="text-xs text-gray-500">{{ $currency }}</div>
        </div>
        <div class="text-center p-3 bg-blue-50 rounded-lg">
            <div class="text-xs text-gray-500 mb-1">Avg Margin</div>
            <div class="text-xl font-bold text-blue-600">
                {{ number_format($margin['avg'], 2) }}
            </div>
            <div class="text-xs text-gray-500">{{ $currency }}</div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
        <p class="text-xs text-gray-600">
            <strong>Margin</strong> is the amount of funds required to maintain open positions. 
            Monitor this to avoid margin calls.
        </p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('marginChart');
    if (!ctx) return;

    const chartData = @json($chartData);
    const currency = @json($currency);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Margin Used',
                    data: chartData.margin,
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                },
                {
                    label: 'Free Margin',
                    data: chartData.free_margin,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: {
                        size: 11
                    },
                    bodyFont: {
                        size: 11
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(context.parsed.y);
                            label += ' ' + currency;
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 10
                        },
                        callback: function(value) {
                            return new Intl.NumberFormat('en-US', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
