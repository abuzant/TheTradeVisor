@section('title', 'Enterprise Settings - TheTradeVisor')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    {{ __('Enterprise Settings') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">Configure your broker whitelist and subscription details</p>
            </div>
            <a href="{{ route('enterprise.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 rounded-r-lg p-4 shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Current Status Card --}}
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 overflow-hidden shadow-lg rounded-xl border-2 border-indigo-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">📊 Current Subscription Status</h3>
                        @if($broker->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                ✓ Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                ✗ Inactive
                            </span>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <p class="text-sm text-gray-500 mb-1">Monthly Fee</p>
                            <p class="text-2xl font-bold text-gray-900">${{ number_format($broker->monthly_fee, 2) }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <p class="text-sm text-gray-500 mb-1">Subscription Ends</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $broker->subscription_ends_at ? $broker->subscription_ends_at->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <p class="text-sm text-gray-500 mb-1">Grace Period</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $broker->grace_period_ends_at ? $broker->grace_period_ends_at->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Configuration Form --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">⚙️ Broker Configuration</h3>

                    <form method="POST" action="{{ route('enterprise.settings.update') }}" class="space-y-6">
                        @csrf

                        {{-- Company Name --}}
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="company_name" 
                                   id="company_name" 
                                   value="{{ old('company_name', $broker->company_name) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   required>
                            @error('company_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                📝 Your brokerage company's display name (e.g., "Equiti Capital UK Ltd")
                            </p>
                        </div>

                        {{-- Official Broker Name --}}
                        <div>
                            <label for="official_broker_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Official Broker Name (MT4/MT5 Server Name) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="official_broker_name" 
                                   id="official_broker_name" 
                                   value="{{ old('official_broker_name', $broker->official_broker_name) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono bg-gray-50"
                                   required>
                            @error('official_broker_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-2 space-y-2">
                                <p class="text-sm text-gray-700">
                                    🔑 <strong>Critical:</strong> This must match <strong>exactly</strong> the broker name returned by MT4/MT5 <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">AccountCompany()</code> function
                                </p>
                                <p class="text-sm text-gray-600">
                                    ✅ Example: <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">Equiti-Demo</code> or <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">ICMarkets-Live03</code>
                                </p>
                                <p class="text-sm text-gray-600">
                                    ⚠️ Case-sensitive, spaces matter, must be exact match
                                </p>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-lg hover:shadow-xl transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- How It Works --}}
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-6 shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800 mb-3">💡 How Broker Whitelisting Works</h4>
                        <div class="text-sm text-blue-700 space-y-2">
                            <p><strong>1. User Registration:</strong> Traders create their own free accounts on TheTradeVisor</p>
                            <p><strong>2. EA Connection:</strong> They download the MT4/MT5 EA and enter their personal API key</p>
                            <p><strong>3. Data Transmission:</strong> EA sends trading data including the broker name from <code class="bg-blue-100 px-1 rounded">AccountCompany()</code></p>
                            <p><strong>4. Automatic Detection:</strong> System checks if broker name matches <code class="bg-blue-100 px-1 rounded">{{ $broker->official_broker_name }}</code></p>
                            <p><strong>5. Instant Activation:</strong> If matched, user gets unlimited free accounts automatically\! 🎉</p>
                            <p class="pt-2 border-t border-blue-200 mt-3"><strong>🔒 Security:</strong> Broker name cannot be spoofed - it's read-only from MT4/MT5 server</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Multi-Entity Information --}}
            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg p-6 shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800 mb-2">🏢 Multiple Legal Entities</h4>
                        <div class="text-sm text-yellow-700 space-y-2">
                            <p>If you operate multiple legal entities with different MT4/MT5 server names, each requires a separate enterprise subscription.</p>
                            <p><strong>Example:</strong> Equiti with 4 entities (Equiti-Demo, Equiti-Live, Equiti-UK, Equiti-Jordan) = 4 separate subscriptions</p>
                            <p class="pt-2"><strong>💰 Pricing:</strong> ${{ number_format($broker->monthly_fee, 2) }}/month per server name</p>
                            <p><strong>📧 Contact:</strong> <a href="mailto:hello@thetradevisor.com" class="underline font-semibold">hello@thetradevisor.com</a> to add additional broker names</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Value Proposition --}}
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 overflow-hidden shadow-lg rounded-xl border-2 border-green-200 p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">💎 Value Proposition</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <p class="text-sm text-gray-500 mb-1">You Pay</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($broker->monthly_fee, 2) }}/mo</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <p class="text-sm text-gray-500 mb-1">Each Client Gets</p>
                        <p class="text-2xl font-bold text-green-600">$29/mo value</p>
                        <p class="text-xs text-gray-500 mt-1">(Pro plan equivalent)</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <p class="text-sm text-gray-500 mb-1">With 100 Clients</p>
                        <p class="text-2xl font-bold text-purple-600">$2,900/mo</p>
                        <p class="text-xs text-gray-500 mt-1">value delivered</p>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-700">
                        <strong>ROI:</strong> <span class="text-green-600 font-bold text-lg">580%</span> value to your clients
                    </p>
                </div>
            </div>

            {{-- Support Contact --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 text-center">
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Need Help?</h4>
                <p class="text-sm text-gray-600 mb-4">Our team is here to assist you with setup, configuration, or any questions</p>
                <div class="flex justify-center space-x-4">
                    <a href="mailto:hello@thetradevisor.com" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email Support
                    </a>
                    <a href="https://thetradevisor.com" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        Visit Website
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
