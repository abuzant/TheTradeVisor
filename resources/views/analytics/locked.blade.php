<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Global Trading Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Global Analytics Locked 🔒
                    </h3>

                    <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
                        To access global trading analytics, you need to connect at least one active MT5 trading account.
                        Share your data, get access to aggregated insights from thousands of traders worldwide!
                    </p>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                        <h4 class="font-semibold text-blue-900 mb-3">What you'll get:</h4>
                        <ul class="text-left text-sm text-blue-800 space-y-2">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Market sentiment analysis</strong> - See what other traders are buying/selling</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Most traded pairs</strong> - Identify trending instruments</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Regional trading patterns</strong> - See what traders in your region focus on</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Broker comparisons</strong> - Anonymous performance metrics across brokers</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Optimal trading hours</strong> - When is the market most active?</span>
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <a href="{{ route('settings.api-key') }}"
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-base font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Get Your API Key & Connect MT5
                        </a>

                        <p class="text-sm text-gray-500">
                            Or <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-900">return to dashboard</a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
