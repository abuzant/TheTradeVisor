<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheTradeVisor - Global Trading Analytics Platform</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Google Analytics -->
    @if(config('services.google_analytics.enabled'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.tracking_id') }}', {
            'anonymize_ip': true
        });
    </script>
    @endif

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">

    {{-- Navigation --}}
    <x-public-nav :fixed="true" />

    {{-- Hero Section --}}
    <section class="pt-32 pb-20 bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    See What <span class="text-indigo-600">Thousands</span> of Traders<br>Are Doing Right Now
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    The world's first platform that aggregates real-time trading data from MT5 terminals worldwide.
                    Get insights you can't find anywhere else.
                </p>
                <div class="flex justify-center gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-lg shadow-lg">
                            Start Free Today
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-lg shadow-lg">
                            Go to Dashboard
                        </a>
                    @endguest
                    <a href="#features" class="px-8 py-4 bg-white text-gray-700 rounded-lg hover:bg-gray-50 font-semibold text-lg shadow-lg border border-gray-300">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-indigo-600">{{ number_format($stats['total_traders']) }}+</div>
                    <div class="text-gray-600 mt-2">Active Traders</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-indigo-600">{{ number_format($stats['total_accounts']) }}+</div>
                    <div class="text-gray-600 mt-2">Trading Accounts</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-indigo-600">{{ number_format($stats['total_trades']) }}+</div>
                    <div class="text-gray-600 mt-2">Trades Analyzed</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-indigo-600">{{ $stats['countries'] }}+</div>
                    <div class="text-gray-600 mt-2">Countries</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Insights You Can't Get Anywhere Else</h2>
                <p class="text-xl text-gray-600">See what's really happening in the markets</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">

                {{-- Feature 1 --}}
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Market Sentiment</h3>
                    <p class="text-gray-600">
                        See real-time buy/sell ratios across all major pairs. Know what thousands of traders are doing right now.
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Regional Patterns</h3>
                    <p class="text-gray-600">
                        Discover what traders in different countries focus on. Find regional trading opportunities.
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Optimal Hours</h3>
                    <p class="text-gray-600">
                        See when the market is most active. Trade during peak hours when opportunities are highest.
                    </p>
                </div>

                {{-- Feature 4 --}}
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Top Performers</h3>
                    <p class="text-gray-600">
                        Track which symbols are most profitable across all traders. Follow the smart money.
                    </p>
                </div>

                {{-- Feature 5 --}}
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Broker Comparison</h3>
                    <p class="text-gray-600">
                        Compare spreads, execution, and trader performance across different brokers.
                    </p>
                </div>

                {{-- Feature 6 --}}
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Your Analytics</h3>
                    <p class="text-gray-600">
                        Beautiful dashboards for your own trading accounts. Track performance across multiple brokers.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Get started in 3 simple steps</p>
            </div>

            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Sign Up Free</h3>
                    <p class="text-gray-600">Create your account in seconds. No credit card required.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Install EA</h3>
                    <p class="text-gray-600">Add our lightweight Expert Advisor to your MT5 terminal. It runs in the background.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Get Insights</h3>
                    <p class="text-gray-600">Access global analytics and track your own performance. Share data, get insights.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 bg-indigo-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Ready to See What Others Can't?
            </h2>
            <p class="text-xl text-indigo-100 mb-8">
                Join thousands of traders already using TheTradeVisor
            </p>
            @guest
                <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 font-semibold text-lg shadow-lg">
                    Get Started Free →
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="inline-block px-8 py-4 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 font-semibold text-lg shadow-lg">
                    Go to Dashboard →
                </a>
            @endguest
        </div>
    </section>

    {{-- Footer --}}
    <x-public-footer />

</body>
</html>
