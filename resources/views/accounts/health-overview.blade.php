@section('title', 'Account Health Overview')
@section('description', 'Compare health metrics and performance across all your trading accounts')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            Account Health Overview
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Compare performance metrics across all your accounts
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Account Selectors & Time Range --}}
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card p-4">
                <form method="GET" action="{{ route('account.health') }}" id="accountHealthForm" class="space-y-4">
                    {{-- Account Selectors --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account 1</label>
                            <select name="accounts[]" onchange="document.getElementById('accountHealthForm').submit()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($allAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ in_array($acc->id, $selectedIds) && $loop->index == array_search($acc->id, $selectedIds) ? 'selected' : '' }}>
                                        {{ $acc->broker_name }} #{{ $acc->account_number }} ({{ $acc->platform_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account 2</label>
                            <select name="accounts[]" onchange="document.getElementById('accountHealthForm').submit()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($allAccounts as $acc)
                                    <option value="{{ $acc->id }}" {{ isset($selectedIds[1]) && $acc->id == $selectedIds[1] ? 'selected' : (!isset($selectedIds[1]) && $loop->index == 1 ? 'selected' : '') }}>
                                        {{ $acc->broker_name }} #{{ $acc->account_number }} ({{ $acc->platform_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Time Range Selector --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">Time Range:</span>
                            <div class="flex space-x-2">
                                @foreach([7, 30, 90, 180] as $period)
                                    <button type="submit" name="days" value="{{ $period }}"
                                       class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $days == $period ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                        {{ $period }}d
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            Comparing <span class="font-semibold">{{ count($accountsData) }}</span> account{{ count($accountsData) > 1 ? 's' : '' }}
                        </div>
                    </div>
                </form>
            </div>

            {{-- Accounts Grid --}}
            <div class="grid grid-cols-1 {{ count($accountsData) > 1 ? 'lg:grid-cols-2' : '' }} gap-6">
                @foreach($accountsData as $accountData)
                    @php
                        $account = $accountData['account'];
                        $data = $accountData['data'];
                        $currentSnapshot = $data['currentSnapshot'];
                        $changes = $data['changes'];
                        $chartData = $data['chartData'];
                        $statistics = $data['statistics'];
                    @endphp

                    <div class="space-y-6">
                        {{-- Account Header Card --}}
                        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        <x-broker-name :broker="$account->broker_name" />
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        #{{ $account->account_number }} 
                                        <x-platform-badge :account="$account" />
                                    </p>
                                </div>
                                <a href="{{ route('account.snapshots', ['account' => $account->id, 'days' => $days]) }}" 
                                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                    View Details →
                                </a>
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
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">Peak Balance</div>
                                    <div class="text-lg font-bold text-gray-900">{{ number_format($statistics['balance']['max'], 2) }} {{ $account->account_currency }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Lowest Balance</div>
                                    <div class="text-lg font-bold text-gray-900">{{ number_format($statistics['balance']['min'], 2) }} {{ $account->account_currency }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Peak Equity</div>
                                    <div class="text-lg font-bold text-green-600">{{ number_format($statistics['equity']['max'], 2) }} {{ $account->account_currency }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Lowest Equity</div>
                                    <div class="text-lg font-bold text-red-600">{{ number_format($statistics['equity']['min'], 2) }} {{ $account->account_currency }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
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
                            <strong>Account Health</strong> shows side-by-side comparison of all your accounts' performance metrics, 
                            including balance trends, equity changes, margin levels, and maximum drawdown. 
                            Click "View Details" on any account to see the full snapshot history.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
