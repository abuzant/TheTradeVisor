<x-public-layout>
    <x-slot name="title">API Documentation - TheTradeVisor | REST API Reference</x-slot>
    <x-slot name="description">Complete REST API documentation for TheTradeVisor. Authentication, endpoints, request/response examples, and rate limits.</x-slot>

    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h1 class="text-5xl font-bold text-gray-900 mb-4">API Documentation</h1>
                <p class="text-xl text-gray-600">RESTful API for accessing your trading data programmatically</p>
                <div class="mt-4 inline-block px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-semibold">
                    Base URL: <code class="bg-blue-200 px-2 py-1 rounded">https://api.thetradevisor.com/v1</code>
                </div>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                {{-- Sidebar --}}
                <div class="md:col-span-1">
                    <nav class="sticky top-20 space-y-1">
                        <a href="#authentication" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded font-medium">Authentication</a>
                        <a href="#rate-limits" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Rate Limits</a>
                        <a href="#accounts" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Accounts</a>
                        <a href="#trades" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Trades</a>
                        <a href="#snapshots" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Account Snapshots</a>
                        <a href="#analytics" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Analytics</a>
                        <a href="#errors" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Error Codes</a>
                    </nav>
                </div>

                {{-- Content --}}
                <div class="md:col-span-3">
                    
                    {{-- Authentication --}}
                    <div id="authentication" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Authentication</h2>
                        <p class="text-gray-700 mb-6">All API requests require authentication using your API key. Include it in the Authorization header:</p>
                        
                        <div class="bg-gray-900 text-gray-100 p-6 rounded-lg mb-6 overflow-x-auto">
                            <pre class="text-sm"><code>Authorization: Bearer YOUR_API_KEY</code></pre>
                        </div>

                        <div class="p-4 bg-blue-50 border-l-4 border-blue-600 rounded mb-6">
                            <p class="text-blue-900 font-semibold">📝 Getting Your API Key</p>
                            <p class="text-blue-800 mt-1">Generate your API key from the <a href="{{ route('settings.api-key') }}" class="underline">Settings → API Key</a> page in your dashboard.</p>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">Example Request</h3>
                        <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                            <pre class="text-sm"><code>curl -X GET "https://api.thetradevisor.com/v1/accounts" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"</code></pre>
                        </div>
                    </div>

                    {{-- Rate Limits --}}
                    <div id="rate-limits" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Rate Limits</h2>
                        <p class="text-gray-700 mb-6">API rate limits are based on your subscription tier and are calculated per hour:</p>
                        
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 border-b">Plan</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 border-b">Requests/Hour</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 border-b">Window</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr>
                                        <td class="px-6 py-4 border-b text-gray-700">Free</td>
                                        <td class="px-6 py-4 border-b text-gray-700">100</td>
                                        <td class="px-6 py-4 border-b text-gray-700">Rolling 60 minutes</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 border-b text-gray-700">Pro</td>
                                        <td class="px-6 py-4 border-b text-gray-700">1,000</td>
                                        <td class="px-6 py-4 border-b text-gray-700">Rolling 60 minutes</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 text-gray-700">Enterprise</td>
                                        <td class="px-6 py-4 text-gray-700">Unlimited</td>
                                        <td class="px-6 py-4 text-gray-700">No limits</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="space-y-4">
                            <div class="p-4 bg-blue-50 border-l-4 border-blue-600 rounded">
                                <p class="text-blue-900 font-semibold">📊 Rate Limit Headers</p>
                                <p class="text-blue-800 mt-1">Every API response includes rate limit information in headers:</p>
                                <ul class="mt-2 text-sm text-blue-800 space-y-1">
                                    <li><code class="bg-blue-100 px-2 py-1 rounded">X-RateLimit-Limit</code> - Your hourly limit</li>
                                    <li><code class="bg-blue-100 px-2 py-1 rounded">X-RateLimit-Remaining</code> - Requests remaining</li>
                                    <li><code class="bg-blue-100 px-2 py-1 rounded">X-RateLimit-Reset</code> - Unix timestamp when limit resets</li>
                                </ul>
                            </div>

                            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-600 rounded">
                                <p class="text-yellow-900 font-semibold">⚠️ Rate Limit Exceeded</p>
                                <p class="text-yellow-800 mt-1">When you exceed your rate limit, you'll receive a <code class="bg-yellow-100 px-2 py-1 rounded">429 Too Many Requests</code> response with a <code class="bg-yellow-100 px-2 py-1 rounded">Retry-After</code> header indicating when you can retry.</p>
                            </div>

                            <div class="p-4 bg-green-50 border-l-4 border-green-600 rounded">
                                <p class="text-green-900 font-semibold">💡 Best Practices</p>
                                <ul class="mt-2 text-sm text-green-800 space-y-1">
                                    <li>• Monitor rate limit headers in your responses</li>
                                    <li>• Implement exponential backoff when approaching limits</li>
                                    <li>• Cache responses when possible to reduce API calls</li>
                                    <li>• Consider upgrading to Pro or Enterprise for higher limits</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Accounts Endpoint --}}
                    <div id="accounts" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Accounts</h2>
                        
                        <div class="mb-8">
                            <div class="flex items-center mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm font-semibold mr-3">GET</span>
                                <code class="text-lg font-mono">/api/v1/accounts</code>
                            </div>
                            <p class="text-gray-700 mb-4">Retrieve all your trading accounts.</p>
                            
                            <h4 class="font-bold text-gray-900 mb-2">Response Example:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                                <pre class="text-sm"><code>{
  "data": [
    {
      "id": 1,
      "account_number": "12345678",
      "broker_name": "IC Markets",
      "platform_type": "MT5",
      "account_currency": "USD",
      "balance": 10000.00,
      "equity": 10250.50,
      "is_active": true,
      "created_at": "2025-01-15T10:30:00Z"
    }
  ],
  "meta": {
    "total": 1
  }
}</code></pre>
                            </div>
                        </div>

                        <div class="mb-8">
                            <div class="flex items-center mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm font-semibold mr-3">GET</span>
                                <code class="text-lg font-mono">/api/v1/accounts/{id}</code>
                            </div>
                            <p class="text-gray-700 mb-4">Retrieve a specific account by ID.</p>
                            
                            <h4 class="font-bold text-gray-900 mb-2">Response Example:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                                <pre class="text-sm"><code>{
  "data": {
    "id": 1,
    "account_number": "12345678",
    "broker_name": "IC Markets",
    "platform_type": "MT5",
    "account_currency": "USD",
    "balance": 10000.00,
    "equity": 10250.50,
    "margin": 500.00,
    "free_margin": 9750.50,
    "profit": 250.50,
    "is_active": true,
    "last_sync_at": "2025-01-15T14:30:00Z"
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Trades Endpoint --}}
                    <div id="trades" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Trades</h2>
                        
                        <div class="mb-8">
                            <div class="flex items-center mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm font-semibold mr-3">GET</span>
                                <code class="text-lg font-mono">/api/v1/trades</code>
                            </div>
                            <p class="text-gray-700 mb-4">Retrieve your trade history with optional filters.</p>
                            
                            <h4 class="font-bold text-gray-900 mb-2">Query Parameters:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-700 mb-4 ml-4">
                                <li><code>account_id</code> - Filter by account ID</li>
                                <li><code>symbol</code> - Filter by trading symbol</li>
                                <li><code>from_date</code> - Start date (YYYY-MM-DD)</li>
                                <li><code>to_date</code> - End date (YYYY-MM-DD)</li>
                                <li><code>limit</code> - Results per page (default: 50, max: 100)</li>
                                <li><code>page</code> - Page number</li>
                            </ul>

                            <h4 class="font-bold text-gray-900 mb-2">Example Request:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto mb-4">
                                <pre class="text-sm"><code>curl -X GET "https://api.thetradevisor.com/v1/trades?symbol=EURUSD&limit=10" \
  -H "Authorization: Bearer YOUR_API_KEY"</code></pre>
                            </div>
                            
                            <h4 class="font-bold text-gray-900 mb-2">Response Example:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                                <pre class="text-sm"><code>{
  "data": [
    {
      "id": 12345,
      "account_id": 1,
      "symbol": "EURUSD",
      "type": "buy",
      "volume": 0.10,
      "open_price": 1.0850,
      "close_price": 1.0875,
      "profit": 25.00,
      "open_time": "2025-01-15T10:00:00Z",
      "close_time": "2025-01-15T12:30:00Z",
      "duration_minutes": 150
    }
  ],
  "meta": {
    "current_page": 1,
    "total_pages": 5,
    "total": 50,
    "per_page": 10
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Account Snapshots --}}
                    <div id="snapshots" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Account Snapshots</h2>
                        <p class="text-gray-700 mb-6">Track account metrics (balance, equity, margin) over time for historical analysis and trend visualization.</p>
                        
                        <div class="p-4 bg-blue-50 border-l-4 border-blue-600 rounded mb-8">
                            <p class="text-blue-900 font-semibold">📝 Finding Your Account ID</p>
                            <p class="text-blue-800 mt-1">The <code class="bg-blue-100 px-2 py-1 rounded">{account}</code> parameter is your <strong>Account ID</strong> (not account number). Find it in the <strong>"API ID"</strong> column on your <a href="/accounts" class="underline">Accounts page</a> with a copy button for easy access.</p>
                        </div>

                        {{-- Get Snapshots --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm font-semibold mr-3">GET</span>
                                <code class="text-lg font-mono">/api/v1/accounts/{account}/snapshots</code>
                            </div>
                            <p class="text-gray-700 mb-4">Get historical snapshots for a specific account.</p>
                            
                            <h4 class="font-bold text-gray-900 mb-2">Query Parameters:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-700 mb-4 ml-4">
                                <li><code>from</code> - Start date YYYY-MM-DD (optional)</li>
                                <li><code>to</code> - End date YYYY-MM-DD (optional)</li>
                                <li><code>interval</code> - raw, hourly, daily (default: raw)</li>
                                <li><code>limit</code> - Max records (default: 1000, max: 10000)</li>
                            </ul>

                            <h4 class="font-bold text-gray-900 mb-2">Example Request:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto mb-4">
                                <pre class="text-sm"><code>curl -X GET "https://api.thetradevisor.com/v1/accounts/2/snapshots?from=2025-11-01&to=2025-11-18&interval=daily" \
  -H "Authorization: Bearer YOUR_API_KEY"</code></pre>
                            </div>

                            <h4 class="font-bold text-gray-900 mb-2">Response Example:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                                <pre class="text-sm"><code>{
  "account_id": 2,
  "account_number": "1012306793",
  "currency": "AED",
  "count": 18,
  "snapshots": [
    {
      "balance": "197464.13",
      "equity": "143903.53",
      "margin": "11625.78",
      "free_margin": "132277.75",
      "margin_level": "1237.80",
      "profit": "-53560.60",
      "snapshot_time": "2025-11-18 15:11:22"
    }
  ]
}</code></pre>
                            </div>
                        </div>

                        {{-- Get Statistics --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm font-semibold mr-3">GET</span>
                                <code class="text-lg font-mono">/api/v1/accounts/{account}/snapshots/stats</code>
                            </div>
                            <p class="text-gray-700 mb-4">Get aggregated statistics with max drawdown calculation.</p>
                            
                            <h4 class="font-bold text-gray-900 mb-2">Query Parameters:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-700 mb-4 ml-4">
                                <li><code>days</code> - Time period in days (default: 30)</li>
                            </ul>

                            <h4 class="font-bold text-gray-900 mb-2">Response Example:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                                <pre class="text-sm"><code>{
  "period_days": "30",
  "total_snapshots": 7514,
  "balance": {
    "current": "197016.10",
    "highest": "200511.12",
    "lowest": "196660.43",
    "average": 197446.33
  },
  "equity": {
    "current": "142796.85",
    "highest": "175580.26",
    "lowest": "137879.70",
    "average": 158035.09,
    "max_drawdown": 21.47
  }
}</code></pre>
                            </div>
                        </div>

                        {{-- Export CSV --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm font-semibold mr-3">GET</span>
                                <code class="text-lg font-mono">/api/v1/accounts/{account}/snapshots/export</code>
                            </div>
                            <p class="text-gray-700 mb-4">Export snapshots as CSV file.</p>
                            
                            <h4 class="font-bold text-gray-900 mb-2">Example Request:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                                <pre class="text-sm"><code>curl -X GET "https://api.thetradevisor.com/v1/accounts/2/snapshots/export" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -o account_snapshots.csv</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Analytics Endpoint --}}
                    <div id="analytics" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Analytics</h2>
                        
                        <div class="mb-8">
                            <div class="flex items-center mb-3">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm font-semibold mr-3">GET</span>
                                <code class="text-lg font-mono">/api/v1/analytics/performance</code>
                            </div>
                            <p class="text-gray-700 mb-4">Get performance analytics for your accounts.</p>
                            
                            <h4 class="font-bold text-gray-900 mb-2">Query Parameters:</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-700 mb-4 ml-4">
                                <li><code>account_id</code> - Specific account (optional)</li>
                                <li><code>days</code> - Time period: 1, 7, 30 (default: 30)</li>
                            </ul>

                            <h4 class="font-bold text-gray-900 mb-2">Response Example:</h4>
                            <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                                <pre class="text-sm"><code>{
  "data": {
    "total_trades": 150,
    "winning_trades": 95,
    "losing_trades": 55,
    "win_rate": 63.3,
    "profit_factor": 1.85,
    "total_profit": 2500.00,
    "total_loss": -1350.00,
    "net_profit": 1150.00,
    "average_win": 26.32,
    "average_loss": -24.55,
    "largest_win": 150.00,
    "largest_loss": -80.00,
    "max_drawdown": 450.00,
    "sharpe_ratio": 1.42
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Error Codes --}}
                    <div id="errors" class="mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Error Codes</h2>
                        <p class="text-gray-700 mb-6">The API uses standard HTTP status codes:</p>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 border-b">Code</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 border-b">Meaning</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr>
                                        <td class="px-6 py-4 border-b font-mono text-green-600">200</td>
                                        <td class="px-6 py-4 border-b text-gray-700">Success</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 border-b font-mono text-red-600">401</td>
                                        <td class="px-6 py-4 border-b text-gray-700">Unauthorized - Invalid API key</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 border-b font-mono text-red-600">403</td>
                                        <td class="px-6 py-4 border-b text-gray-700">Forbidden - Insufficient permissions</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 border-b font-mono text-red-600">404</td>
                                        <td class="px-6 py-4 border-b text-gray-700">Not Found - Resource doesn't exist</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 border-b font-mono text-red-600">429</td>
                                        <td class="px-6 py-4 border-b text-gray-700">Too Many Requests - Rate limit exceeded</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 font-mono text-red-600">500</td>
                                        <td class="px-6 py-4 text-gray-700">Server Error - Something went wrong</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mt-8 mb-3">Error Response Format</h3>
                        <div class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-x-auto">
                            <pre class="text-sm"><code>{
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Invalid API key provided",
    "details": "The API key you provided is invalid or has been revoked"
  }
}</code></pre>
                        </div>
                    </div>

                    {{-- Need Help --}}
                    <div class="mt-16 p-8 bg-gray-50 rounded-lg">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Need Help?</h3>
                        <p class="text-gray-700 mb-6">Have questions about the API? We're here to help!</p>
                        <div class="flex gap-4">
                            <a href="/contact" class="px-6 py-3 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">Contact Support</a>
                            <a href="/docs" class="px-6 py-3 bg-gray-200 text-gray-900 rounded font-semibold hover:bg-gray-300">View Documentation</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</x-public-layout>
