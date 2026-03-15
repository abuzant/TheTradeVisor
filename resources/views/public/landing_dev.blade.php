<x-public-layout>
    <x-slot name="title">TheTradeVisor - Professional MT4/MT5 Trading Analytics</x-slot>
    <x-slot name="description">Enterprise-grade trading analytics platform. Real-time data from MT4/MT5 terminals worldwide. Trusted by professional traders and institutions.</x-slot>
    <x-slot name="keywords">MT4 analytics, MT5 analytics, forex trading analytics, trading performance tracker, broker comparison, global trading data</x-slot>
    
    {{-- Reuse the RDF Schema from main landing --}}
    <x-slot name="head">
        <style>
            .stat-card { transition: all 0.3s ease; }
            .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
            .hero-gradient { background: linear-gradient(135deg, #F3F4F6 0%, #EFF6FF 100%); }
            .text-gradient { background: linear-gradient(90deg, #2563EB 0%, #7C3AED 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        </style>
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "TheTradeVisor",
            "applicationCategory": "FinanceApplication",
            "operatingSystem": "Web, Cloud, Windows (MT4/MT5 Plugin)",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "USD",
                "description": "Free for traders, Enterprise pricing for brokers"
            },
            "description": "Enterprise-grade trading analytics platform for MT4 and MT5. Track performance, analyze equity curves, and share public portfolios.",
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.8",
                "ratingCount": "150"
            },
            "featureList": [
                "Real-time MT4/MT5 Analytics",
                "Global Market Insights",
                "Broker Comparison",
                "Public Trading Profiles",
                "Equity Curve Tracking"
            ],
            "screenshot": "https://thetradevisor.com/platform-screenshots/Account%20Overview.png",
            "author": {
                "@type": "Organization",
                "name": "TheTradeVisor",
                "url": "https://thetradevisor.com",
                "logo": "{{ url('/logo.svg') }}",
                "sameAs": [
                    "https://twitter.com/thetradevisor",
                    "https://linkedin.com/company/thetradevisor"
                ],
                "contactPoint": {
                    "@type": "ContactPoint",
                    "email": "hello@thetradevisor.com",
                    "contactType": "customer support"
                }
            }
        }
        </script>
    </x-slot>

    {{-- Hero Section --}}
    <section class="hero-gradient relative overflow-hidden pt-20 pb-32 lg:pt-32 lg:pb-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left Content --}}
                <div>
                    <div class="inline-block px-4 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold mb-6">
                        🚀 Now <b class="text-gradient">100% Free</b> for Traders
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight mb-6">
                        Master Your <br>
                        <span class="text-gradient">Trading Performance</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Connect your MT4 & MT5 accounts in seconds.<br />
                        Get institutional-grade analytics, share verified track records, and benchmark your performance against the global market.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @guest
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-blue-600 text-white rounded-lg font-bold text-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl text-center">
                            Start Analyzing Free
                        </a>
                        @else
                        <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-blue-600 text-white rounded-lg font-bold text-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl text-center">
                            Go to Dashboard
                        </a>
                        @endguest
                        <a href="/analytics" class="px-8 py-4 bg-white text-gray-700 border border-gray-200 rounded-lg font-bold text-lg hover:bg-gray-50 transition shadow-sm hover:shadow text-center">
                            Explore Live Data
                        </a>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        No credit card required
                        <span class="mx-2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Works with any broker
                    </p>
                </div>

                {{-- Right Image (3D Perspective) --}}
                <div class="relative hidden lg:block">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl opacity-20 blur-2xl transform rotate-3"></div>
                    <img src="https://thetradevisor.com/platform-screenshots/Account%20Overview.png" alt="Dashboard Preview" class="relative rounded-xl shadow-2xl border border-gray-200 transform -rotate-2 hover:rotate-0 transition duration-500">
                    
                    {{-- Floating Badge --}}
                    <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-lg shadow-xl border border-gray-100 flex items-center gap-3 animate-bounce" style="animation-duration: 3s;">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Global Win Rate</div>
                            <div class="text-lg font-bold text-gray-900">{{ $stats['win_rate'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Decorative Blobs --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute top-0 left-0 -ml-20 -mt-20 w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </section>

    {{-- Social Proof --}}
    <section class="py-10 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-6">Trusted by 1,000,000's of traders using</p>
            <div class="flex justify-center items-center gap-12 grayscale opacity-75">
                <span class="text-xl font-bold text-gray-600 flex items-center gap-2"><img src="https://www.metatrader4.com/i/metatrader-4-logo.png" class="h-8" alt="MT4 / MetaTrader 4">&nbsp;</span>
                <span class="text-xl font-bold text-gray-600 flex items-center gap-2"><img src="https://www.metatrader5.com/i/metatrader-5-logo.png" class="h-8" alt="MT5 / MetaTrader 5">&nbsp;</span>
            </div><br />
            <spam class="text-center text-gray-600">TheTradeVisor is a comprehensive, enterprise-grade trading analytics platform designed to bridge the gap between retail traders and institutional-grade performance tracking. It aggregates, analyzes, and visualizes trading data from MetaTrader 4 (MT4) and MetaTrader 5 (MT5) platforms in real-time.</span>
        </div>
    </section>

    {{-- Global Analytics Dashboard (Hidden for now) --}}
    {{-- @include('partials.landing-stats-block') --}}

    {{-- Feature 1: Real-Time --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl">
                        <img src="https://thetradevisor.com/platform-screenshots/Realtime%20Activity%20Monitor.png" alt="Realtime Activity Monitor" class="rounded-xl shadow-lg w-full">
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <div class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-600 text-xs font-bold mb-4">LIVE SYNC</div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Your Metatrader,<br>Live on the Web</h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Stop taking screenshots of your terminal. Our lightweight Expert Advisor syncs your trades in real-time to a beautiful web dashboard. Accessible from any device, anywhere.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Sub-second latency updates
                        </li>
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Works with any MT4/MT5 broker
                        </li>
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Bank-grade encryption
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature: Risk Analytics --}}
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-block px-3 py-1 rounded-full bg-red-100 text-red-600 text-xs font-bold mb-4">RISK MANAGEMENT</div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Protect Your Capital<br>Like a Pro</h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Understanding your risk is the first step to consistency. Visualize your drawdown, analyze exposure heatmaps, and identify dangerous trading patterns before they cost you money.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Drawdown Analysis & Alerts
                        </li>
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Risk of Ruin Calculator
                        </li>
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            Exposure Heatmaps
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 p-8 rounded-2xl">
                        <img src="https://thetradevisor.com/platform-screenshots/Risk%20Analytics%20Dashboard.png" alt="Risk Analytics Dashboard" class="rounded-xl shadow-lg w-full">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature 2: Public Profiles --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-block px-3 py-1 rounded-full bg-purple-100 text-purple-600 text-xs font-bold mb-4">SOCIAL PROOF</div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Showcase Your Success</h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Build your reputation with a verified public profile. Share your equity curve, win rate, and trading history with investors or social media followers. You control exactly what to show.
                    </p>
                    <div class="flex gap-4">
                        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="font-bold text-gray-900">🎖️ Verified</div>
                            <div class="text-sm text-gray-500">Track Record</div>
                        </div>
                        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="font-bold text-gray-900">👑 Privacy</div>
                            <div class="text-sm text-gray-500">Controls</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl">
                        <img src="https://thetradevisor.com/platform-screenshots/Equity%20Curve.png" alt="Public Profile Preview" class="rounded-xl shadow-lg w-full">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature: Symbol Performance --}}
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1">
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-2xl">
                        <img src="https://thetradevisor.com/platform-screenshots/Symbol%20Performance.png" alt="Symbol Performance Analytics" class="rounded-xl shadow-lg w-full">
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <div class="inline-block px-3 py-1 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold mb-4">DEEP DIVE</div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Master Every Symbol</h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Stop trading blindly. Analyze your performance per currency pair. Discover which symbols make you money and which ones are draining your account.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Win Rate by Symbol
                        </li>
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Best Trading Hours
                        </li>
                        <li class="flex items-center gap-3 text-gray-700">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            Long vs Short Performance
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature 3: Broker Intel --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1">
                    <div class="bg-gradient-to-br from-green-50 to-teal-50 p-8 rounded-2xl">
                        <img src="https://thetradevisor.com/platform-screenshots/Platform%20Performance%20Matrix.png" alt="Broker Analytics Preview" class="rounded-xl shadow-lg w-full">
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <div class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-600 text-xs font-bold mb-4">MARKET INTELLIGENCE</div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Know Your Broker's<br>True Performance</h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Don't rely on marketing claims. See real execution speeds, slippage, and spread data crowdsourced from thousands of live accounts.
                    </p>
                    <a href="#" class="text-blue-600 font-semibold hover:text-blue-700 flex items-center gap-2">
                        View Broker Leaderboard <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature: API & Export --}}
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-block px-3 py-1 rounded-full bg-gray-800 text-white text-xs font-bold mb-4">DEVELOPER FRIENDLY</div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Your Data, Your Rules</h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Need custom reports? Or building your own trading tools? Access your entire trading history via our robust REST API or export clean CSV/PDF reports in one click.
                    </p>
                    <div class="bg-gray-900 rounded-lg p-6 font-mono text-sm text-gray-300 shadow-xl">
                        <div class="flex items-center gap-2 mb-4 border-b border-gray-700 pb-4">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="ml-auto text-xs text-gray-500">bash</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-purple-400">curl</span> -X GET https://api.thetradevisor.com/v1/trades \<br>
                            &nbsp;&nbsp;-H <span class="text-green-400">"Authorization: Bearer YOUR_API_KEY"</span>
                        </div>
                        <div class="mt-4 text-gray-500"># Response</div>
                        <div class="text-blue-300">
                            {<br>
                            &nbsp;&nbsp;"trades": [<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;{ "symbol": "EURUSD", "profit": 124.50, ... }<br>
                            &nbsp;&nbsp;]<br>
                            }
                        </div>
                    </div>
                </div>
                <div class="space-y-8">
                    <div class="flex gap-6 items-start">
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">One-Click Exports</h3>
                            <p class="text-gray-600">Download your complete trading history in CSV or professional PDF formats for tax reporting or analysis.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 items-start">
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">REST API Access</h3>
                            <p class="text-gray-600">Full programmatic access to your account data. Integrate with Excel, Python scripts, or your own custom dashboard.</p>
                        </div>
                    </div>
                    <div class="flex gap-6 items-start">
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Daily Digest Emails</h3>
                            <p class="text-gray-600">Wake up to a summary of yesterday's performance. Win rate, P/L, and key stats delivered straight to your inbox.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-24 bg-gradient-to-br from-blue-900 to-indigo-900 text-white overflow-hidden relative">
        <div class="absolute inset-0 bg-[url('/grid-pattern.svg')] opacity-10"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl lg:text-5xl font-bold mb-8">Ready to Level Up Your Trading?</h2>
            <p class="text-xl text-blue-100 mb-12 max-w-2xl mx-auto">Join 17,000+ traders who trust TheTradeVisor for their daily analytics. It's completely free.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @guest
                <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-900 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-lg hover:shadow-xl">
                    Get Started Now
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white text-blue-900 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-lg hover:shadow-xl">
                    Go to Dashboard
                </a>
                @endguest
                <a href="/features" class="px-8 py-4 bg-transparent border border-blue-400 text-white rounded-lg font-bold text-lg hover:bg-blue-800/30 transition">
                    View All Features
                </a>
            </div>
        </div>
    </section>

</x-public-layout>
