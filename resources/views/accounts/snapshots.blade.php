@section('title', 'Account Snapshots - ' . $account->broker_name . ' #' . $account->account_number)
@section('description', 'Historical account metrics and performance visualization for ' . $account->broker_name . ' account')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Account Snapshots
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    <x-broker-name :broker="$account->broker_name" /> 
                    #{{ $account->account_number }} 
                    <x-platform-badge :account="$account" />
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('account.show', $account->id) }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    ← Back to Account
                </a>
                <a href="{{ url('/api/v1/accounts/' . $account->id . '/snapshots/export?from=' . now()->subDays($days)->format('Y-m-d')) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    📥 Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Time Range Selector --}}
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Time Range:</span>
                        <x-time-filter 
                            :periods="$timePeriods" 
                            :currentPeriod="$days . 'd'" 
                            baseRoute="account.snapshots" 
                            :routeParams="['account' => $account->id]"
                        />
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold">{{ $statistics['total_snapshots'] }}</span> snapshots in last {{ $days }} days
                    </div>
                </div>
            </div>

            {{-- Health Metrics Cards --}}
            <x-snapshots.health-metrics 
                :current="$currentSnapshot" 
                :changes="$changes" 
                :currency="$account->account_currency" 
            />

            {{-- Balance & Equity Chart --}}
            <x-snapshots.balance-equity-chart 
                :chartData="$chartData" 
                :currency="$account->account_currency"
                :days="$days"
            />

            {{-- Bottom Row: Max Drawdown & Margin Timeline --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Max Drawdown Gauge --}}
                <x-snapshots.max-drawdown-gauge 
                    :maxDrawdown="$statistics['max_drawdown']"
                    :equity="$statistics['equity']"
                    :currency="$account->account_currency"
                />

                {{-- Margin Usage Stats --}}
                <x-snapshots.margin-stats
                    :chartData="$chartData"
                    :margin="$statistics['margin']"
                    :currency="$account->account_currency"
                />
            </div>

            {{-- Statistics Summary --}}
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Period Statistics ({{ $days }} days)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Peak Balance</div>
                        <div class="text-xl font-bold text-gray-900">{{ number_format($statistics['balance']['max'], 2) }} {{ $account->account_currency }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Lowest Balance</div>
                        <div class="text-xl font-bold text-gray-900">{{ number_format($statistics['balance']['min'], 2) }} {{ $account->account_currency }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Peak Equity</div>
                        <div class="text-xl font-bold text-green-600">{{ number_format($statistics['equity']['max'], 2) }} {{ $account->account_currency }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Lowest Equity</div>
                        <div class="text-xl font-bold text-red-600">{{ number_format($statistics['equity']['min'], 2) }} {{ $account->account_currency }}</div>
                    </div>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            <strong>About Snapshots:</strong> Account snapshots are automatically captured every time your EA sends data. 
                            Historical data is aggregated after 30 days (hourly) and 90 days (daily) to optimize storage. 
                            Data older than 180 days is automatically removed.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
