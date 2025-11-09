@section('title', 'API Key Settings - TheTradeVisor | MT5 Integration')
@section('description', 'Manage your API key for MetaTrader 5 integration. Connect your MT5 terminal to TheTradeVisor for automated trading analytics.')
@section('og_title', 'API Key Settings - TheTradeVisor')
@section('og_description', 'Manage your MT5 API key and integration settings')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            {{ __('API Key Settings') }}
        </h1>
        <p class="mt-1 text-sm text-gray-600">Connect your MT5 terminal with your API key</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
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

            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                <div class="p-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">Your API Key</h2>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                        <div class="flex">
                            <input type="text"
                                   id="api-key"
                                   value="{{ session('new_key') ?? $user->api_key }}"
                                   readonly
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-sm font-mono">
                            <button onclick="copyApiKey()"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 text-sm font-medium">
                                Copy
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Use this API key in your MT5 Expert Advisor to connect your trading account.</p>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">MT5 Expert Advisor Configuration</h4>
                        <div class="bg-gray-50 rounded-md p-4 font-mono text-sm">
                            <p class="text-gray-700">API_URL: <span class="text-indigo-600">https://api.thetradevisor.com/api/v1/data/collect</span></p>
                            <p class="text-gray-700">API_KEY: <span class="text-indigo-600">{{ $user->api_key }}</span></p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Regenerate API Key</h4>
                        <p class="text-sm text-gray-500 mb-4">Warning: Regenerating your API key will disconnect all your MT5 terminals until you update them with the new key.</p>

                        <form method="POST" action="{{ route('settings.api-key.regenerate') }}" onsubmit="return confirm('Are you sure you want to regenerate your API key? This will disconnect all your MT5 terminals.')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                                Regenerate API Key
                            </button>
                        </form>
                    </div>

                </div>
            </div>

            <!-- HOW TO Section -->
            <div class="mt-8 bg-white/90 backdrop-blur-sm overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 rounded-xl">
                <div class="p-6">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">
                        📚 HOW TO: Enable Expert Advisor in MetaTrader 5
                    </h2>

                    <div class="space-y-6">
                        <!-- Step 1 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 font-bold">
                                    1
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Enable Algorithmic Trading</h3>
                                <p class="mt-1 text-sm text-gray-600">In MetaTrader 5, click the "AutoTrading" button or press Ctrl+E to enable algorithmic trading.</p>
                                <div class="mt-2 p-3 bg-blue-50 rounded-md">
                                    <p class="text-sm text-blue-800">💡 Make sure the AutoTrading button is green (enabled)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 font-bold">
                                    2
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Allow WebRequest for EA</h3>
                                <p class="mt-1 text-sm text-gray-600">The Expert Advisor needs to send data to our API. You must allow WebRequest in MT5 settings.</p>
                                
                                <div class="mt-3">
                                    <ol class="text-sm text-gray-600 space-y-2">
                                        <li>1. Go to <strong>Tools → Options</strong> or press Ctrl+O</li>
                                        <li>2. Click on the <strong>Expert Advisors</strong> tab</li>
                                        <li>3. Check <strong>"Allow WebRequest for listed URL"</strong></li>
                                        <li>4. Click <strong>"Add"</strong> and enter: <code class="bg-gray-100 px-2 py-1 text-xs">https://api.thetradevisor.com</code></li>
                                        <li>5. Click <strong>OK</strong> to save settings</li>
                                    </ol>
                                </div>

                                <div class="mt-3 p-3 bg-amber-50 rounded-md">
                                    <p class="text-sm text-amber-800">
                                        ⚠️ <strong>Important:</strong> Without allowing WebRequest, the EA cannot send data to our servers and will show connection errors.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 font-bold">
                                    3
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Install and Configure the EA</h3>
                                <p class="mt-1 text-sm text-gray-600">Copy your API key and configure the Expert Advisor with your credentials.</p>
                                
                                <div class="mt-3 space-y-2">
                                    <div class="p-3 bg-gray-50 rounded-md">
                                        <p class="text-sm font-mono text-gray-700">
                                            API_URL: <span class="text-indigo-600">https://api.thetradevisor.com/api/v1/data/collect</span><br>
                                            API_KEY: <span class="text-indigo-600">{{ $user->api_key }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Verification -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100 text-green-600 font-bold">
                                    ✓
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Verify Connection</h3>
                                <p class="mt-1 text-sm text-gray-600">After configuring, check the EA "Experts" tab in MT5. You should see connection success messages.</p>
                                <div class="mt-2 p-3 bg-green-50 rounded-md">
                                    <p class="text-sm text-green-800">✅ Success: "Connected to TheTradeVisor API"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- External Links -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">📖 Official Documentation</h4>
                        <div class="space-y-2">
                            <a href="https://www.metatrader5.com/en/terminal/help/startworking/settings#ea" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                MetaTrader 5 Official Guide - Expert Advisors Settings
                            </a>
                            <a href="https://meetalgo.com/docs/how-to/metatrader/how-to-allow-webrequest-for-auto-news-data-download/" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Step-by-Step Guide: Allow WebRequest in MT5
                            </a>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">
                            🔒 These are official MetaTrader 5 resources. What we're asking you to configure is standard practice for EAs that need internet access.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyApiKey() {
            const input = document.getElementById('api-key');
            input.select();
            document.execCommand('copy');

            // Show feedback
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            btn.classList.add('bg-green-600');
            btn.classList.remove('bg-indigo-600');

            setTimeout(() => {
                btn.textContent = originalText;
                btn.classList.remove('bg-green-600');
                btn.classList.add('bg-indigo-600');
            }, 2000);
        }
    </script>
</x-app-layout>
