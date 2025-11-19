@props(['chartData', 'currency', 'days'])

@php
    $chartId = 'balanceEquityChart_' . uniqid();
@endphp

<div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900">📈 Balance & Equity Trend</h3>
        <p class="text-sm text-gray-600">Historical performance over the last {{ $days }} days</p>
    </div>

    <div class="relative" style="height: 400px;">
        <canvas id="{{ $chartId }}"></canvas>
    </div>

    <div class="mt-4 flex items-center justify-center space-x-6 text-sm">
        <div class="flex items-center">
            <div class="w-4 h-0.5 bg-blue-500 mr-2"></div>
            <span class="text-gray-700">Balance</span>
        </div>
        <div class="flex items-center">
            <div class="w-4 h-0.5 bg-green-500 mr-2" style="border-top: 2px dashed;"></div>
            <span class="text-gray-700">Equity</span>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $chartId }}');
    if (!ctx) return;

    const chartData = @json($chartData);
    const currency = @json($currency);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Balance',
                    data: chartData.balance,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Equity',
                    data: chartData.equity,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6,
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
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    displayColors: true,
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
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: false,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value) + ' ' + currency;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
