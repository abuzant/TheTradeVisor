@section('title', 'Account Health Overview')
@section('description', 'View health metrics and snapshots for your trading accounts')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            Account Health Overview
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Select an account to view detailed health metrics and snapshots
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($accounts as $account)
                    <a href="{{ route('account.snapshots', ['account' => $account->id, 'days' => 7]) }}" 
                       class="block bg-white/90 backdrop-blur-sm rounded-xl shadow-card hover:shadow-card-hover transition-all duration-300 p-6 group">
                        
                        {{-- Account Header --}}
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">
                                    <x-broker-name :broker="$account->broker_name" />
                                </h3>
                                <p class="text-sm text-gray-500">#{{ $account->account_number }}</p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Quick Metrics --}}
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Balance</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ number_format($account->balance, 2) }} {{ $account->account_currency }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Equity</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ number_format($account->equity, 2) }} {{ $account->account_currency }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Status</span>
                                @if($account->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- View Button --}}
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-indigo-600 group-hover:text-indigo-700">
                                <span class="text-sm font-medium">View 7-Day Health</span>
                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Info Box --}}
            <div class="mt-8 bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            <strong>Account Health</strong> shows you a 7-day snapshot of your account's performance, 
                            including balance trends, equity changes, margin levels, and maximum drawdown. 
                            Click on any account to view detailed metrics.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
