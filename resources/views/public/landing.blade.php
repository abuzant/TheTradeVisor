<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TheTradeVisor - Professional MT4/MT5 Trading Analytics Platform</title>
    <meta name="description" content="Enterprise-grade trading analytics platform. Real-time data from MT4/MT5 terminals worldwide. Trusted by professional traders and institutions.">
    <meta name="keywords" content="MT4 analytics, MT5 analytics, forex trading analytics, trading performance tracker, broker comparison, global trading data">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="TheTradeVisor - Professional MT4/MT5 Trading Analytics">
    <meta property="og:description" content="Enterprise-grade trading analytics platform. Real-time data from MT4/MT5 terminals worldwide.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="TheTradeVisor">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="TheTradeVisor - Professional MT4/MT5 Trading Analytics">
    <meta name="twitter:description" content="Enterprise-grade trading analytics platform. Real-time data from MT4/MT5 terminals worldwide.">
    
    <link rel="canonical" href="{{ url()->current() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Google Analytics --}}
    @if(config('services.google_analytics.enabled'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.tracking_id') }}', {
            'anonymize_ip': true,
            'page_title': 'TheTradeVisor - Professional MT4/MT5 Trading Analytics Platform',
            'page_path': '/'
        });
    </script>
    @endif
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-gray-900">TheTradeVisor</span>
                    <span class="ml-3 px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded">PROFESSIONAL</span>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="#analytics" class="text-gray-700 hover:text-gray-900 font-medium">Analytics</a>
                    <a href="#features" class="text-gray-700 hover:text-gray-900 font-medium">Features</a>
                    <a href="/pricing" class="text-gray-700 hover:text-gray-900 font-medium">Pricing</a>
                    <a href="/faq" class="text-gray-700 hover:text-gray-900 font-medium">FAQ</a>
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 font-medium">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Get Started</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl font-bold text-gray-900 mb-6">
                    Professional Trading Analytics<br>for MT4 & MT5 Platforms
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Enterprise-grade analytics platform aggregating real-time trading data from terminals worldwide. 
                    Make data-driven decisions with comprehensive market insights.
                </p>
                @guest
                <div class="flex justify-center gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        Start Free Trial
                    </a>
                    <a href="/features" class="px-8 py-3 bg-gray-100 text-gray-900 rounded font-semibold hover:bg-gray-200">
                        View Features
                    </a>
                </div>
                @endguest
                <p class="mt-6 text-sm text-gray-500">Free account • No credit card required • Cancel anytime</p>
            </div>
        </div>
    </section>

    {{-- Global Analytics Dashboard --}}
    <section id="analytics" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Global Trading Analytics</h2>
                <p class="text-gray-600">Live data from our network (Last 30 days)</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                {{-- Stat Card 1 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_traders']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Total Traders</div>
                </div>

                {{-- Stat Card 2 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-green-600">{{ number_format($stats['active_traders']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Active Traders</div>
                </div>

                {{-- Stat Card 3 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_trades_30d']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Total Trades</div>
                </div>

                {{-- Stat Card 4 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['avg_trades_per_day']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Avg Trades/Day</div>
                </div>

                {{-- Stat Card 5 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_volume_30d'], 0) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Total Volume</div>
                </div>

                {{-- Stat Card 6 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['avg_position_size'], 2) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Avg Position Size</div>
                </div>

                {{-- Stat Card 7 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['countries'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Countries</div>
                </div>

                {{-- Stat Card 8 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['top_country']->country_code ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-600 mt-1">Top Country</div>
                </div>

                {{-- Stat Card 9 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_symbols'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Symbols Traded</div>
                </div>

                {{-- Stat Card 10 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['most_traded_symbol']->symbol ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-600 mt-1">Most Traded</div>
                </div>

                {{-- Stat Card 11 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-green-600">{{ number_format($stats['winning_trades']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Winning Trades</div>
                </div>

                {{-- Stat Card 12 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-red-600">{{ number_format($stats['losing_trades']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Losing Trades</div>
                </div>

                {{-- Stat Card 13 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['win_rate'] }}%</div>
                    <div class="text-sm text-gray-600 mt-1">Global Win Rate</div>
                </div>

                {{-- Stat Card 14 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">${{ number_format(abs($stats['total_profit_30d']), 0) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Total P/L</div>
                </div>

                {{-- Stat Card 15 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_brokers'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Brokers</div>
                </div>

                {{-- Stat Card 16 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['mt4_accounts'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">MT4 Accounts</div>
                </div>

                {{-- Stat Card 17 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['mt5_accounts'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">MT5 Accounts</div>
                </div>

                {{-- Stat Card 18 --}}
                <div class="stat-card bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['data_points']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Data Points</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Professional Features</h2>
                <p class="text-gray-600">Enterprise-grade analytics tools for serious traders</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-6 border border-gray-200 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Real-Time Analytics</h3>
                    <p class="text-gray-600 mb-4">Track global trading activity in real-time with comprehensive metrics and insights from thousands of MT4/MT5 terminals.</p>
                    <a href="/features" class="text-blue-600 font-medium hover:text-blue-700">Learn more →</a>
                </div>

                <div class="p-6 border border-gray-200 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Performance Tracking</h3>
                    <p class="text-gray-600 mb-4">Detailed performance analytics with win rates, profit factors, risk metrics, and comprehensive trade history.</p>
                    <a href="/features" class="text-blue-600 font-medium hover:text-blue-700">Learn more →</a>
                </div>

                <div class="p-6 border border-gray-200 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Broker Comparison</h3>
                    <p class="text-gray-600 mb-4">Compare broker performance, execution quality, and spreads across different platforms and regions.</p>
                    <a href="/features" class="text-blue-600 font-medium hover:text-blue-700">Learn more →</a>
                </div>

                <div class="p-6 border border-gray-200 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Geographic Insights</h3>
                    <p class="text-gray-600 mb-4">Analyze trading patterns by country and region. See where the smart money is moving.</p>
                    <a href="/features" class="text-blue-600 font-medium hover:text-blue-700">Learn more →</a>
                </div>

                <div class="p-6 border border-gray-200 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Risk Analytics</h3>
                    <p class="text-gray-600 mb-4">Advanced risk management tools including volatility analysis, correlation matrices, and drawdown tracking.</p>
                    <a href="/features" class="text-blue-600 font-medium hover:text-blue-700">Learn more →</a>
                </div>

                <div class="p-6 border border-gray-200 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Data Export</h3>
                    <p class="text-gray-600 mb-4">Export your data in multiple formats (CSV, PDF) for further analysis and reporting.</p>
                    <a href="/features" class="text-blue-600 font-medium hover:text-blue-700">Learn more →</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Trusted by Traders Worldwide</h2>
                <p class="text-gray-600">See what professional traders are saying about TheTradeVisor</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Testimonial 1 --}}
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">"TheTradeVisor completely changed how I analyze my trading. The global insights are invaluable for understanding market sentiment."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">MK</div>
                        <div class="ml-3">
                            <p class="font-semibold text-gray-900">Michael K.</p>
                            <p class="text-sm text-gray-600">Professional Forex Trader</p>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 2 --}}
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">"Best analytics platform I've used. The broker comparison feature alone saved me thousands in spreads and commissions."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">SA</div>
                        <div class="ml-3">
                            <p class="font-semibold text-gray-900">Sarah A.</p>
                            <p class="text-sm text-gray-600">Day Trader, UAE</p>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 3 --}}
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">"Our trading firm uses TheTradeVisor Enterprise. The API integration and unlimited accounts make it perfect for our operations."</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold">JR</div>
                        <div class="ml-3">
                            <p class="font-semibold text-gray-900">James R.</p>
                            <p class="text-sm text-gray-600">Trading Firm Director</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Trust Badges --}}
            <div class="mt-16 text-center">
                <p class="text-sm text-gray-600 mb-6">Trusted by traders in {{ $stats['countries'] }}+ countries</p>
                <div class="flex justify-center items-center space-x-8 text-gray-400">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-medium">SSL Encrypted</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-medium">GDPR Compliant</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                        <span class="text-sm font-medium">24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-16 bg-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Get Started?</h2>
            <p class="text-xl text-blue-100 mb-8">Join professional traders using TheTradeVisor for data-driven decisions</p>
            @guest
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-white text-blue-600 rounded font-semibold hover:bg-gray-100">
                Start Free Trial
            </a>
            @endguest
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h4 class="text-white font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/features" class="hover:text-white">Features</a></li>
                        <li><a href="/pricing" class="hover:text-white">Pricing</a></li>
                        <li><a href="{{ route('analytics') }}" class="hover:text-white">Analytics</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/faq" class="hover:text-white">FAQ</a></li>
                        <li><a href="/docs" class="hover:text-white">Documentation</a></li>
                        <li><a href="/api-docs" class="hover:text-white">API</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/about" class="hover:text-white">About</a></li>
                        <li><a href="/contact" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm">
                <p>© {{ date('Y') }} TheTradeVisor. Professional Trading Analytics Platform.</p>
            </div>
        </div>
    </footer>

</body>
</html>
