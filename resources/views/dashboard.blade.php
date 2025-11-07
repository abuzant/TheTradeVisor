<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="{{ route('export.dashboard.csv') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Summary
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Account Limit Info --}}
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            You're using <strong>{{ $accountLimit['current'] }} of {{ $accountLimit['max'] }}</strong> accounts
                            ({{ ucfirst($user->subscription_tier) }} Plan)
                            @if(!$accountLimit['can_add'])
                                - <a href="#" class="underline font-semibold">Upgrade to add more</a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Overview Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

                {{-- Total Balance --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Balance <em>(all accounts)</em></dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $totals['display_currency'] }} {{ number_format($totals['total_balance'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Equity --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Equity <em>(all accounts)</em></dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $totals['display_currency'] }} {{ number_format($totals['total_equity'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Profit --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 {{ $totals['total_profit'] >= 0 ? 'bg-green-500' : 'bg-red-500' }} rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Current Profit/Loss <em>(all accounts)</em></dt>
                                    <dd class="text-2xl font-semibold {{ $totals['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
					{{ $totals['display_currency'] }} {{ number_format($totals['total_profit'], 2) }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

            </div>






	{{-- ADD THIS ENTIRE SECTION --}}
	@if($accounts->isNotEmpty())
	<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
	    <div class="p-6">
	        <div class="flex justify-between items-center mb-4">
	            <div>
	                <h3 class="text-lg font-semibold text-gray-900">Account Performance (Last 30 Days)</h3>
	                <p class="text-sm text-gray-500 mt-1">Balance and Equity trends across your accounts</p>
	            </div>

	            {{-- Legend / Account Toggles --}}
	            <div class="flex flex-wrap gap-2">
	                @foreach($accountsChartData as $index => $accountData)
	                    <label class="inline-flex items-center cursor-pointer px-3 py-2 rounded-md border-2 transition-all hover:bg-gray-50"
	                           style="border-color: rgb({{ $accountData['color'] }});"
	                           data-account-toggle="{{ $accountData['account_id'] }}">
	                        <input type="checkbox"
	                               class="account-toggle mr-2 rounded"
	                               data-account-id="{{ $accountData['account_id'] }}"
	                               checked
	                               style="accent-color: rgb({{ $accountData['color'] }});">
	                        <span class="text-sm font-medium text-gray-700">
	                            {{ $accountData['account_name'] }}
	                        </span>
	                        <span class="ml-2 text-xs text-gray-500">({{ $accountData['currency'] }} → {{ $accountData['display_currency'] }})</span>
	                    </label>
	                @endforeach
	            </div>
	        </div>

	        {{-- Chart Container --}}
	        <div class="relative" style="height: 400px;">
	            <canvas id="accountsChart"></canvas>
	        </div>

	        {{-- Chart Legend --}}
	        <div class="mt-4 flex items-center justify-center gap-6 text-sm text-gray-600">
	            <div class="flex items-center">
	                <div class="w-4 h-0.5 bg-gray-600 mr-2"></div>
	                <span>Balance</span>
	            </div>
	            <div class="flex items-center">
	                <div class="w-4 h-0.5 border-t-2 border-dashed border-gray-600 mr-2"></div>
	                <span>Equity</span>
	            </div>
	        </div>
	    </div>
	</div>
	@endif



            {{-- Trading Accounts Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Trading Accounts</h3>

                    @if($accounts->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No trading accounts connected</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by connecting your MT5 terminal with the Expert Advisor.</p>
                            <div class="mt-6">
                                <a href="{{ route('settings.api-key') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    View API Key
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Broker</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Positions</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Sync</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($accounts as $account)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <x-broker-name :broker="$account->broker_name" class="text-indigo-600 hover:text-indigo-900" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $account->account_number ?? 'Anonymous' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $account->account_currency }} {{ number_format($account->balance, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $account->account_currency }} {{ number_format($account->equity, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $account->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $account->account_currency }} {{ number_format($account->profit, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $account->openPositions->count() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $account->last_sync_at ? $account->last_sync_at->diffForHumans() : 'Never' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('account.show', $account->id) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Activity --}}
            @if($recentDeals->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Trades</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentDeals as $deal)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $deal->time->format('M d, H:i') }}
                                            </td>
						<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
						    @if($deal->symbol && $deal->symbol !== '' && $deal->symbol !== 'UNKNOWN')
						    <a href="{{ route('trades.symbol', $deal->normalized_symbol) }}"
							       class="text-indigo-600 hover:text-indigo-900" title="({{ $deal->symbol }})"
							       title="Raw: {{ $deal->symbol }}">
							        {{ $deal->normalized_symbol }}
						    </a>
						    @else
						    <span class="text-gray-400 italic text-xs">{{ ucfirst($deal->deal_category) }}</span>
						    @endif
						</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $deal->type == 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ strtoupper($deal->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $deal->volume }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $deal->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $account->account_currency }} {{ number_format($deal->profit, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ strtoupper($deal->reason) }}
                                            </td>
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


	@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
	    // Chart data from backend
	    const accountsData = @json($accountsChartData ?? []);

	    if (accountsData.length === 0) return;

	    const ctx = document.getElementById('accountsChart');
	    if (!ctx) return;

	    // Build datasets
	    const datasets = [];

	    accountsData.forEach((account, index) => {
		const baseColor = `rgb(${account.color})`;
		const lightColor = `rgba(${account.color}, 0.1)`;

		// Balance line (solid)
		datasets.push({
		    label: `${account.account_name} - Balance`,
		    data: account.balance_data,
		    borderColor: baseColor,
		    backgroundColor: lightColor,
		    borderWidth: 2,
		    fill: false,
		    tension: 0.4,
		    pointRadius: 0,
		    pointHoverRadius: 5,
		    accountId: account.account_id,
		    hidden: false
		});

		// Equity line (dashed)
		datasets.push({
		    label: `${account.account_name} - Equity`,
		    data: account.equity_data,
		    borderColor: baseColor,
		    backgroundColor: 'transparent',
		    borderWidth: 2,
		    borderDash: [5, 5],
		    fill: false,
		    tension: 0.4,
		    pointRadius: 0,
		    pointHoverRadius: 5,
		    accountId: account.account_id,
		    hidden: false
		});
	    });

	    // Create chart
	    const chart = new Chart(ctx, {
		type: 'line',
		data: { datasets: datasets },
		options: {
		    responsive: true,
		    maintainAspectRatio: false,
		    interaction: {
			mode: 'index',
			intersect: false,
		    },
		    plugins: {
			legend: {
			    display: false // We have custom legend with toggles
			},
			tooltip: {
			    callbacks: {
				label: function(context) {
				    let label = context.dataset.label || '';
				    if (label) {
					label += ': ';
				    }
				    if (context.parsed.y !== null) {
					// Use display currency from data
					const displayCurrency = '{{ $totals["display_currency"] ?? "USD" }}';
					label += displayCurrency + ' ' + new Intl.NumberFormat('en-US', {
					    minimumFractionDigits: 2,
					    maximumFractionDigits: 2
					}).format(context.parsed.y);
				    }
				    return label;
				}
			    }
			}
		    },
		    scales: {
			x: {
			    type: 'time',
			    time: {
				unit: 'day',
				displayFormats: {
				    day: 'MMM dd'
				}
			    },
			    grid: {
				display: false
			    }
			},
			y: {
			    beginAtZero: false,
			    ticks: {
				callback: function(value) {
				    const displayCurrency = '{{ $totals["display_currency"] ?? "USD" }}';
				    return displayCurrency + ' ' + new Intl.NumberFormat('en-US', {
					minimumFractionDigits: 0,
					maximumFractionDigits: 0
				    }).format(value);
				}
			    }
			}
		    }
		}
	    });

	    // Handle account toggles
	    document.querySelectorAll('.account-toggle').forEach(checkbox => {
		checkbox.addEventListener('change', function() {
		    const accountId = parseInt(this.dataset.accountId);
		    const isChecked = this.checked;

		    // Toggle all datasets for this account (balance and equity)
		    chart.data.datasets.forEach(dataset => {
			if (dataset.accountId === accountId) {
			    dataset.hidden = !isChecked;
			}
		    });

		    chart.update();

		    // Update visual state of the toggle label
		    const label = this.closest('[data-account-toggle]');
		    if (isChecked) {
			label.style.opacity = '1';
			label.style.backgroundColor = '';
		    } else {
			label.style.opacity = '0.4';
			label.style.backgroundColor = '#f3f4f6';
		    }
		});
	    });
	});
	</script>
	@endpush

</x-app-layout>
