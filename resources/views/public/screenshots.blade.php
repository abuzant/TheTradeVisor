<x-public-layout>
    <x-slot name="title">Platform Screenshots - See TheTradeVisor in Action</x-slot>
    <x-slot name="description">Explore real screenshots of TheTradeVisor's powerful trading analytics platform. See performance tracking, risk analytics, and real-time monitoring in action.</x-slot>

    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-5xl font-bold text-gray-900 mb-6">Platform Screenshots</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    See TheTradeVisor's powerful analytics platform in action with real screenshots from our users
                </p>
            </div>

            {{-- Account Performance --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">📈 Account Performance Tracking</h2>
                    <p class="text-gray-600 text-lg">Monitor your balance and equity trends across all your trading accounts with beautiful, interactive charts.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/account-performance.png') }}" alt="Account Performance Chart" class="w-full rounded-lg shadow-lg">
                </div>
                <div class="mt-4 grid md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Balance & Equity Tracking
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        30-Day Historical View
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Multi-Account Support
                    </div>
                </div>
            </div>

            {{-- Open Positions --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">💼 Live Open Positions</h2>
                    <p class="text-gray-600 text-lg">Real-time monitoring of all your open positions with current prices, volumes, and P&L.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/open-positions.png') }}" alt="Open Positions Dashboard" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Recent Closed Positions --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">📋 Trade History</h2>
                    <p class="text-gray-600 text-lg">Complete history of closed positions with detailed entry/exit prices and profit/loss calculations.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/closed-positions.png') }}" alt="Recent Closed Positions" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Equity Curve --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">📉 Equity Curve Analysis</h2>
                    <p class="text-gray-600 text-lg">Visualize your account equity over time to identify trends and patterns in your trading performance.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/equity-curve.png') }}" alt="Equity Curve Chart" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Trading by Symbol --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">🎯 Symbol Distribution</h2>
                    <p class="text-gray-600 text-lg">Understand which symbols you trade most with beautiful donut charts and detailed breakdowns.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/trading-by-symbol.png') }}" alt="Trading by Symbol Chart" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Trading Activity by Hour --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">⏰ Trading Activity Timeline</h2>
                    <p class="text-gray-600 text-lg">Analyze when you trade most actively throughout the day to optimize your trading schedule.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/trading-by-hour.png') }}" alt="Trading Activity by Hour" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Trading Session Analysis --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">🌐 Trading Session Performance</h2>
                    <p class="text-gray-600 text-lg">Compare your performance across different trading sessions (Sydney, Tokyo, London, New York) with radar charts.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/trading-session-analysis.png') }}" alt="Trading Session Analysis" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Risk Analytics --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">⚠️ Risk Analytics Dashboard</h2>
                    <p class="text-gray-600 text-lg">Advanced risk management with win rate analysis, profit factors, and risk scores for each symbol.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/risk-analytics.png') }}" alt="Risk Analytics Dashboard" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Performance Leaderboards --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">🏆 Performance Leaderboards</h2>
                    <p class="text-gray-600 text-lg">See top-performing symbols, brokers, and countries based on global trading data.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/performance-leaderboards.png') }}" alt="Performance Leaderboards" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Real-Time Activity Monitor --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">🔴 Real-Time Activity Monitor</h2>
                    <p class="text-gray-600 text-lg">Live updates of trading activity with automatic refresh every minute.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/realtime-activity.png') }}" alt="Real-Time Activity Monitor" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Profit/Loss Distribution --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">💰 Profit/Loss Distribution</h2>
                    <p class="text-gray-600 text-lg">Visualize your win rate and average profit/loss with clear, actionable metrics.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/profit-loss-distribution.png') }}" alt="Profit/Loss Distribution" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">🔥 Recent Activity Feed</h2>
                    <p class="text-gray-600 text-lg">Stay updated with recent trades and current open positions at a glance.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/recent-activity.png') }}" alt="Recent Activity Feed" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Trading Patterns --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">📊 Trading Patterns Analysis</h2>
                    <p class="text-gray-600 text-lg">Discover patterns in your trading with day-of-week performance analysis and position patterns.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/trading-patterns.png') }}" alt="Trading Patterns Analysis" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- Market Volatility --}}
            <div class="mb-20">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">📈 Market Volatility Analysis</h2>
                    <p class="text-gray-600 text-lg">Track symbol performance trends and volatility levels to make informed trading decisions.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <img src="{{ asset('images/screenshots/market-volatility.png') }}" alt="Market Volatility Analysis" class="w-full rounded-lg shadow-lg">
                </div>
            </div>

            {{-- CTA --}}
            <div class="text-center mt-16 p-12 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg text-white">
                <h2 class="text-4xl font-bold mb-4">Ready to Experience These Features?</h2>
                <p class="text-xl mb-8 text-blue-100">Start tracking your trading performance today with TheTradeVisor</p>
                <div class="flex justify-center gap-4">
                    @guest
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Get Started Free
                    </a>
                    <a href="{{ route('pricing') }}" class="px-8 py-4 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-400 transition">
                        View Pricing
                    </a>
                    @else
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Go to Dashboard
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
