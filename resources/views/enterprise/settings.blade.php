@section('title', 'Enterprise Settings - TheTradeVisor')

<x-enterprise-layout>
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
                            @if($broker->subscription_ends_at && now()->diffInDays($broker->subscription_ends_at) < 90 && $broker->subscription_ends_at->isFuture())
                                <p class="text-xs text-indigo-600 font-medium mt-2">
                                    🎉 Extend for 1 year & get 10% off!
                                </p>
                            @endif
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <p class="text-sm text-gray-500 mb-1">Grace Period</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($broker->grace_period_ends_at)
                                    {{ $broker->grace_period_ends_at->format('M d, Y') }}
                                @elseif($broker->is_active)
                                    <span class="text-green-600">Active</span>
                                @else
                                    N/A
                                @endif
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
                                   {{ Auth::guard('enterprise')->user()->isViewer() ? 'readonly' : '' }}
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
                                   {{ Auth::guard('enterprise')->user()->isViewer() ? 'readonly' : '' }}
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
                        @if(Auth::guard('enterprise')->user()->canManage())
                            <div class="flex justify-end pt-4">
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-lg hover:shadow-xl transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Save Settings
                                </button>
                            </div>
                        @else
                            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4 mt-4">
                                <p class="text-sm text-yellow-800">
                                    👁️ <strong>View-Only Access:</strong> You can view settings but cannot make changes. Contact an administrator to modify these settings.
                                </p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- API Access --}}
            @if(Auth::guard('enterprise')->user()->canManage())
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-indigo-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        API Access
                    </h3>
                    <p class="text-sm text-indigo-100 mt-1">Programmatic access to your aggregated data</p>
                </div>
                <div class="p-6">
                    @if(session('new_api_key'))
                        <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-4">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-green-800 mb-2">✅ New API Key Generated!</h4>
                                    <p class="text-sm text-green-700 mb-3">⚠️ <strong>Copy this key now</strong> - it will only be shown once for security reasons.</p>
                                    <div class="bg-white border border-green-300 rounded-lg p-3 font-mono text-sm break-all">
                                        {{ session('new_api_key') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        {{-- Current API Key --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your API Key</label>
                            @if($apiKeys->count() > 0)
                                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="font-mono text-sm text-gray-600">{{ substr($apiKeys->first()->key, 0, 20) }}••••••••••••••••••••</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Created: {{ $apiKeys->first()->created_at->format('M d, Y') }}
                                                @if($apiKeys->first()->last_used_at)
                                                    • Last used: {{ $apiKeys->first()->last_used_at->diffForHumans() }}
                                                @else
                                                    • Never used
                                                @endif
                                            </p>
                                        </div>
                                        <form action="{{ route('enterprise.api-key.regenerate') }}" method="POST" 
                                              onsubmit="return confirm('⚠️ WARNING: This will revoke your current API key and generate a new one. Any applications using the old key will stop working immediately. Are you sure?');">
                                            @csrf
                                            <button type="submit" class="ml-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                                                🔄 Regenerate
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <p class="text-sm text-yellow-800">⚠️ No API key found. Please contact support.</p>
                                </div>
                            @endif
                        </div>

                        {{-- API Documentation --}}
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-indigo-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                📚 API Documentation
                            </h4>
                            <div class="text-sm text-indigo-800 space-y-2">
                                <p><strong>Base URL:</strong> <code class="bg-indigo-100 px-2 py-1 rounded">https://thetradevisor.com/api/enterprise/v1/</code></p>
                                <p><strong>Authentication:</strong> <code class="bg-indigo-100 px-2 py-1 rounded">Authorization: Bearer ent_...</code></p>
                                
                                <div class="mt-4 pt-4 border-t border-indigo-200">
                                    <p class="font-semibold mb-2">Available Endpoints:</p>
                                    <ul class="space-y-1 ml-4">
                                        <li>• <code class="bg-white px-2 py-0.5 rounded text-xs">GET /accounts</code> - List all trading accounts</li>
                                        <li>• <code class="bg-white px-2 py-0.5 rounded text-xs">GET /metrics</code> - Aggregated performance metrics</li>
                                        <li>• <code class="bg-white px-2 py-0.5 rounded text-xs">GET /performance</code> - Detailed performance data</li>
                                        <li>• <code class="bg-white px-2 py-0.5 rounded text-xs">GET /top-performers</code> - Top performing accounts</li>
                                        <li>• <code class="bg-white px-2 py-0.5 rounded text-xs">GET /trading-hours</code> - Trading hours analysis</li>
                                        <li>• <code class="bg-white px-2 py-0.5 rounded text-xs">GET /export</code> - Export data (CSV/JSON)</li>
                                    </ul>
                                </div>

                                <div class="mt-4 pt-4 border-t border-indigo-200">
                                    <p class="font-semibold mb-2">Example Request:</p>
                                    <div class="bg-gray-900 text-green-400 rounded-lg p-3 font-mono text-xs overflow-x-auto">
curl -H "Authorization: Bearer ent_your_key_here" \<br>
     https://thetradevisor.com/api/enterprise/v1/metrics
                                    </div>
                                </div>

                                <div class="mt-4 flex gap-3">
                                    <a href="{{ asset('docs/api/ENTERPRISE_API.md') }}" target="_blank" download
                                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Download API Documentation
                                    </a>
                                    <a href="mailto:enterprise@thetradevisor.com?subject=Enterprise API Support" 
                                       class="inline-flex items-center px-4 py-2 bg-white border border-indigo-300 text-indigo-700 rounded-lg hover:bg-indigo-50 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        API Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- How It Works --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 shadow-lg">
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
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 shadow-lg">
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
                            <p><strong>📧 Contact:</strong> <a href="mailto:
                            enterprise@thetradevisor.com" class="underline font-semibold">enterprise@thetradevisor.com</a> to add additional broker names</p>
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
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200 p-8 text-center shadow-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Need Help?</h4>
                <p class="text-sm text-gray-600 mb-4">Our team is here to assist you with setup, configuration, or any questions</p>
                <div class="flex justify-center space-x-4">
                    <a href="mailto:enterprise@thetradevisor.com" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
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
</x-enterprise-layout>
