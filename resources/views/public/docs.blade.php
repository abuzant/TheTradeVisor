<x-public-layout>
    <x-slot name="title">Documentation - TheTradeVisor | Complete Guide</x-slot>
    <x-slot name="description">Complete documentation for TheTradeVisor trading analytics platform. Setup guides, features, API reference, and troubleshooting.</x-slot>

    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-5xl font-bold text-gray-900 mb-6">Documentation</h1>
            <p class="text-xl text-gray-600 mb-12">Everything you need to know about TheTradeVisor</p>

            <div class="grid md:grid-cols-4 gap-8">
                {{-- Sidebar --}}
                <div class="md:col-span-1">
                    <nav class="sticky top-20 space-y-1">
                        <a href="#getting-started" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Getting Started</a>
                        <a href="#installation" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Installation</a>
                        <a href="#features" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Features</a>
                        <a href="#analytics" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Analytics</a>
                        <a href="#api" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">API</a>
                        <a href="#troubleshooting" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Troubleshooting</a>
                    </nav>
                </div>

                {{-- Content --}}
                <div class="md:col-span-3 prose prose-lg max-w-none">
                    
                    {{-- Getting Started --}}
                    <div id="getting-started" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Getting Started</h2>
                        <p class="text-gray-600 mb-4">TheTradeVisor is a professional trading analytics platform that aggregates real-time data from MT4 and MT5 trading terminals worldwide.</p>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Quick Start</h3>
                        <ol class="list-decimal list-inside space-y-2 text-gray-700">
                            <li><a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700">Create a free account</a></li>
                            <li>Download the Expert Advisor (EA) from your dashboard</li>
                            <li>Install the EA on your MT4/MT5 terminal</li>
                            <li>Enter your API key in the EA settings</li>
                            <li>Start trading and view your analytics in real-time</li>
                        </ol>

                        <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-600 rounded">
                            <p class="text-blue-900 font-semibold">💡 Pro Tip</p>
                            <p class="text-blue-800 mt-1">Your first account is completely free, forever. No credit card required!</p>
                        </div>
                    </div>

                    {{-- Installation --}}
                    <div id="installation" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Installation Guide</h2>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">MT4 Installation</h3>
                        <ol class="list-decimal list-inside space-y-3 text-gray-700">
                            <li>Download <code>TheTradeVisor.ex4</code> from your dashboard</li>
                            <li>Open your MT4 terminal</li>
                            <li>Go to <strong>File → Open Data Folder</strong></li>
                            <li>Navigate to <code>MQL4/Experts/</code></li>
                            <li>Copy the EA file into this folder</li>
                            <li>Restart MT4</li>
                            <li>Drag the EA onto any chart</li>
                            <li>Enable <strong>Allow DLL imports</strong> and <strong>Allow WebRequest</strong></li>
                            <li>Enter your API key in the settings</li>
                            <li>Click OK</li>
                        </ol>

                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">MT5 Installation</h3>
                        <p class="text-gray-700 mb-4">Same process as MT4, but use the <code>MQL5/Experts/</code> folder instead.</p>

                        <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-600 rounded">
                            <p class="text-yellow-900 font-semibold">⚠️ Important</p>
                            <p class="text-yellow-800 mt-1">Make sure to enable AutoTrading (green button in MT4/MT5 toolbar) for the EA to function.</p>
                        </div>
                    </div>

                    {{-- Features --}}
                    <div id="features" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Features Overview</h2>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Real-Time Analytics</h3>
                        <p class="text-gray-700 mb-4">Track your trading performance in real-time with comprehensive metrics:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li>Win rate and profit factor</li>
                            <li>Average trade duration</li>
                            <li>Risk-reward ratios</li>
                            <li>Drawdown analysis</li>
                            <li>Position size distribution</li>
                        </ul>

                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Global Market Insights</h3>
                        <p class="text-gray-700 mb-4">Access unique insights from our global network:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li>Most traded symbols worldwide</li>
                            <li>Country-based trading patterns</li>
                            <li>Broker performance comparison</li>
                            <li>Market sentiment analysis</li>
                            <li>Platform comparison (MT4 vs MT5)</li>
                        </ul>

                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Risk Management</h3>
                        <p class="text-gray-700 mb-4">Advanced risk analytics to protect your capital:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li>Volatility tracking</li>
                            <li>Correlation matrices</li>
                            <li>Maximum drawdown alerts</li>
                            <li>Position sizing recommendations</li>
                        </ul>
                    </div>

                    {{-- Analytics --}}
                    <div id="analytics" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Analytics Dashboard</h2>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Personal Dashboard</h3>
                        <p class="text-gray-700 mb-4">Your personal dashboard shows analytics for all your connected accounts:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li><strong>Overview:</strong> Quick snapshot of your trading performance</li>
                            <li><strong>Performance:</strong> Detailed metrics and charts</li>
                            <li><strong>Trades:</strong> Complete trade history with filters</li>
                            <li><strong>Symbols:</strong> Performance breakdown by trading pair</li>
                            <li><strong>Brokers:</strong> Compare your brokers' performance</li>
                        </ul>

                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Global Analytics</h3>
                        <p class="text-gray-700 mb-4">Access aggregated data from our entire network:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li>Real-time trading volume</li>
                            <li>Popular trading pairs</li>
                            <li>Geographic trading patterns</li>
                            <li>Market sentiment indicators</li>
                        </ul>
                    </div>

                    {{-- API --}}
                    <div id="api" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">API Access</h2>
                        <p class="text-gray-700 mb-4">All plans include API access with tier-based rate limits. <a href="/api-docs" class="text-blue-600 hover:text-blue-700">View complete API documentation →</a></p>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Authentication</h3>
                        <p class="text-gray-700 mb-4">All API requests require authentication using your API key:</p>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto">
                            <code>Authorization: Bearer YOUR_API_KEY</code>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Rate Limits</h3>
                        <p class="text-gray-700 mb-3">Rate limits are based on your subscription tier and calculated per rolling 60-minute window:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li><strong>Free:</strong> 100 requests/hour</li>
                            <li><strong>Pro:</strong> 1,000 requests/hour</li>
                            <li><strong>Enterprise:</strong> Unlimited requests</li>
                        </ul>
                        <p class="text-gray-600 mt-3 text-sm">All responses include rate limit headers (<code>X-RateLimit-*</code>) to help you track your usage.</p>
                    </div>

                    {{-- Troubleshooting --}}
                    <div id="troubleshooting" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Troubleshooting</h2>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">EA Not Connecting</h3>
                        <p class="text-gray-700 mb-4">If your EA isn't sending data:</p>
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 ml-4">
                            <li>Check that AutoTrading is enabled (green button)</li>
                            <li>Verify your API key is correct</li>
                            <li>Ensure WebRequest is allowed for <code>https://api.thetradevisor.com</code></li>
                            <li>Check your internet connection</li>
                            <li>Restart your MT4/MT5 terminal</li>
                        </ol>

                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Data Not Updating</h3>
                        <p class="text-gray-700 mb-4">If your dashboard isn't showing recent trades:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li>Check that the EA is running (smiley face in chart corner)</li>
                            <li>Verify your account is active in the Accounts page</li>
                            <li>Wait a few minutes - data syncs every 60 seconds</li>
                            <li>Check the EA logs in MT4/MT5 Expert tab</li>
                        </ul>

                        <h3 class="text-2xl font-bold text-gray-900 mt-8 mb-3">Need More Help?</h3>
                        <p class="text-gray-700 mb-4">Can't find what you're looking for?</p>
                        <div class="flex gap-4">
                            <a href="/faq" class="px-6 py-3 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">View FAQ</a>
                            <a href="/contact" class="px-6 py-3 bg-gray-100 text-gray-900 rounded font-semibold hover:bg-gray-200">Contact Support</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</x-public-layout>
